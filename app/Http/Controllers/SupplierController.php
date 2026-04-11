<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Contracts\SupplierServiceInterface;

class SupplierController extends Controller
{
    public function __construct(protected SupplierServiceInterface $supplierService)
    {
    }

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
        $jsonData = $this->supplierService->export($supplier);
        $fileName = 'supplier_' . str_replace(' ', '_', strtolower($supplier->name)) . '_data.json';
        
        return response($jsonData)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function exportAll()
    {
        $jsonData = $this->supplierService->exportAll();
        $fileName = 'all_suppliers_export_' . date('Ymd_His') . '.json';
        
        return response($jsonData)
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

        $strategy = $request->input('strategy', 'skip');
        $isDryRun = $request->boolean('dry_run', false);
        $resolutionMap = json_decode($request->input('resolution_map', '{}'), true) ?? [];

        try {
            $result = $this->supplierService->import($supplier, $data, $strategy, $resolutionMap, $isDryRun);

            return response()->json([
                'success' => true,
                'message' => $isDryRun ? 'Dry run completed successfully.' : 'Import completed successfully.',
                'stats' => $result['stats'],
                'conflicts' => $result['conflicts']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }
}