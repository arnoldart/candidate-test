<?php

namespace App\Services;

use App\Contracts\SupplierServiceInterface;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierService implements SupplierServiceInterface
{
    public function export(Supplier $supplier): string
    {
        $supplier->load('cltLayups.cltLayers');
        return $supplier->toJson(JSON_PRETTY_PRINT);
    }

    public function exportAll(): string
    {
        $suppliers = Supplier::with('cltLayups.cltLayers')->get();
        return $suppliers->toJson(JSON_PRETTY_PRINT);
    }

    public function import(Supplier $supplier, array $data, string $globalStrategy, array $resolutionMap, bool $isDryRun): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'conflicts_detected' => 0,
        ];
        
        $conflicts = [];

        try {
            DB::beginTransaction();

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

                    // Conflict detected
                    $stats['conflicts_detected']++;

                    if ($currentStrategy === 'skip') {
                        $stats['skipped']++;
                        if ($isDryRun) {
                            $conflicts[] = [
                                'layup_name' => $layupData['name'],
                                'existing_layers' => $existingLayersData,
                                'importing_layers' => $importingLayersData
                            ];
                        }
                        continue;
                    }

                    if ($currentStrategy === 'duplicate') {
                        // Append timestamp or counter string to name
                        $newName = $layupData['name'] . ' (Imported ' . time() . ')';
                        if (!$isDryRun) {
                            $newLayup = $supplier->cltLayups()->create([
                                'name' => $newName,
                                'species_grade' => $existingLayup->species_grade,
                                'status' => $existingLayup->status,
                                'created_by' => $existingLayup->created_by,
                            ]);
                            $this->processLayers($newLayup, $importingLayersData);
                            $stats['created']++;
                        }
                        continue;
                    }

                    if ($currentStrategy === 'overwrite') {
                        if (!$isDryRun) {
                            $existingLayup->cltLayers()->delete();
                            $this->processLayers($existingLayup, $importingLayersData);
                            $stats['updated']++;
                        }
                        continue;
                    }
                } else {
                    // Layup does not exist; create it
                    if (!$isDryRun) {
                        $newLayup = $supplier->cltLayups()->create([
                            'name' => $layupData['name'],
                            'species_grade' => $layupData['species_grade'] ?? null,
                            'status' => $layupData['status'] ?? 'Draft',
                            'created_by' => $layupData['created_by'] ?? null,
                        ]);
                        
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
                        
                        $this->processLayers($newLayup, $importingLayersData);
                        $stats['created']++;
                    }
                }
            }

            if ($isDryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            return ['success' => true, 'stats' => $stats, 'conflicts' => $conflicts];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function processLayers($layup, $layersData)
    {
        foreach ($layersData as $index => $layer) {
            $layup->cltLayers()->create([
                'layer_order' => $layer['order'] ?? ($index + 1),
                'thickness' => $layer['thickness'],
                'width' => $layer['width'],
                'angle' => $layer['angle'],
            ]);
        }
    }
}
