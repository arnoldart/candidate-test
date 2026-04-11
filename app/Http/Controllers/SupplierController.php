<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::query()->withCount('cltLayups');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();
        
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dibuat.');
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        if ($request->has('redirect_to')) {
            return redirect($request->input('redirect_to'))
                ->with('success', 'Supplier berhasil diperbarui.');
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function export(Supplier $supplier)
    {
        $supplier->load('cltLayups.cltLayers');
        $fileName = 'supplier_' . str_replace(' ', '_', strtolower($supplier->name)) . '_data.json';
        
        return response($supplier->toJson(JSON_PRETTY_PRINT))
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function exportAll()
    {
        $suppliers = Supplier::with('cltLayups.cltLayers')->get();
        $fileName = 'all_suppliers_export_' . date('Ymd_His') . '.json';
        
        return response($suppliers->toJson(JSON_PRETTY_PRINT))
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function import(Request $request, Supplier $supplier)
    {
        $request->validate([
            'file' => 'required|file|mimes:json,csv,txt|max:10240', // 10MB max
            'strategy' => 'in:skip,overwrite,duplicate',
            'dry_run' => 'boolean',
            'resolution_map' => 'nullable|string'
        ]);

        $fileContent = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($fileContent, true);

        if (!isset($data['clt_layups']) || !is_array($data['clt_layups'])) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON structure. Missing clt_layups array.'], 422);
        }

        $globalStrategy = $request->input('strategy', 'skip');
        $isDryRun = $request->boolean('dry_run', false);
        $resolutionMap = json_decode($request->input('resolution_map', '{}'), true) ?? [];

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'conflicts_detected' => 0,
        ];
        
        $conflicts = [];

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($data['clt_layups'] as $layupData) {
                if (!isset($layupData['name'])) continue;

                $existingLayup = $supplier->cltLayups()->where('name', $layupData['name'])->first();
                $currentStrategy = $resolutionMap[$layupData['name']] ?? $globalStrategy;

                if ($existingLayup) {
                    $existingLayersData = $existingLayup->cltLayers()->orderBy('layer_order')->get()->map(function($layer, $index) {
                        return [
                            'order' => $layer->layer_order ?? ($index + 1),
                            'thickness' => (float) $layer->thickness,
                            'width' => (float) $layer->width,
                            'angle' => (float) $layer->angle,
                        ];
                    })->toArray();

                    $importingLayersData = [];
                    if (isset($layupData['clt_layers']) && is_array($layupData['clt_layers'])) {
                        foreach ($layupData['clt_layers'] as $idx => $inLayer) {
                            $importingLayersData[] = [
                                'order' => $inLayer['layer_order'] ?? ($idx + 1),
                                'thickness' => (float) ($inLayer['thickness'] ?? 0),
                                'width' => (float) ($inLayer['width'] ?? 0),
                                'angle' => (float) ($inLayer['angle'] ?? 0),
                            ];
                        }
                    }

                    // Auto-skip if data is perfectly identical
                    if ($existingLayersData === $importingLayersData) {
                        $stats['skipped']++;
                        continue;
                    }

                    $stats['conflicts_detected']++;

                    if ($isDryRun && !isset($resolutionMap[$layupData['name']])) {
                        $conflicts[] = [
                            'layup_name' => $layupData['name'],
                            'existing_layers' => $existingLayersData,
                            'importing_layers' => $importingLayersData,
                        ];
                    }

                    if ($currentStrategy === 'skip') {
                        $stats['skipped']++;
                        continue;
                    }

                    if ($currentStrategy === 'overwrite') {
                        // Delete child layers and reuse layup
                        $existingLayup->cltLayers()->delete();
                        $layup = $existingLayup;
                        $stats['updated']++;
                    } elseif ($currentStrategy === 'duplicate') {
                        // Create a new one with copy string
                        $layup = $supplier->cltLayups()->create([
                            'name' => $layupData['name'] . ' (Copy ' . time() . ')'
                        ]);
                        $stats['created']++;
                    }
                } else {
                    $layup = $supplier->cltLayups()->create([
                        'name' => $layupData['name']
                    ]);
                    $stats['created']++;
                }

                // Insert layers
                if (isset($layupData['clt_layers']) && is_array($layupData['clt_layers'])) {
                    foreach ($layupData['clt_layers'] as $layerData) {
                        $layup->cltLayers()->create([
                            'thickness' => $layerData['thickness'] ?? 0,
                            'width' => $layerData['width'] ?? 0,
                            'angle' => $layerData['angle'] ?? 0,
                            'layer_order' => $layerData['layer_order'] ?? 0,
                        ]);
                    }
                }
            }

            if ($isDryRun) {
                \Illuminate\Support\Facades\DB::rollBack();
            } else {
                \Illuminate\Support\Facades\DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => $isDryRun ? 'Dry run completed successfully.' : 'Import completed successfully.',
                'stats' => $stats,
                'conflicts' => $conflicts
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }
}