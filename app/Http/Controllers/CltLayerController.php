<?php

namespace App\Http\Controllers;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Http\Requests\CltLayerRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\View\View;

class CltLayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, CltLayup $layup): View
    {
        $query = $layup->cltLayers();
        $layers = $query->orderBy('layer_order')->get();
        return view('cltlayers.index', compact('layup', 'layers'));
    }

    public function store(CltLayerRequest $request, CltLayup $layup): RedirectResponse
    {
        $layup->cltLayers()->create($request->validated());

        return redirect()->route('layups.layers.index', $layup)
            ->with('success', 'CLT Layer berhasil ditambahkan.');
    }

    public function update(CltLayerRequest $request, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $layer->update($request->validated());

        return redirect()->route('layups.layers.index', $layup)
            ->with('success', 'CLT Layer berhasil diperbarui.');
    }

    public function sync(Request $request, CltLayup $layup): RedirectResponse
    {
        $request->validate([
            'layers' => 'nullable|array',
            'layers.*.thickness' => 'required|numeric|min:0',
            'layers.*.width' => 'required|numeric|min:0',
            'layers.*.angle' => 'required|numeric',
        ]);

        $layup->cltLayers()->delete();

        if ($request->has('layers')) {
            $layersData = [];
            foreach ($request->layers as $index => $layer) {
                $layersData[] = [
                    'layer_order' => $index + 1,
                    'thickness' => $layer['thickness'],
                    'width' => $layer['width'],
                    'angle' => $layer['angle'],
                ];
            }
            $layup->cltLayers()->createMany($layersData);
        }

        return redirect()->route('layups.layers.index', $layup)
            ->with('success', 'Layer composition saved successfully.');
    }
}
