<?php

namespace App\Http\Controllers;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CltLayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, CltLayup $layup): View
    {
        $query = $layup->cltLayers();
        // Layer biasanya tidak disearch pakai nama, tapi mungkin di sort
        $layers = $query->orderBy('layer_order')->paginate(10)->withQueryString();
        return view('cltlayers.index', compact('layup', 'layers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CltLayer $cltLayer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CltLayer $cltLayer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CltLayer $cltLayer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CltLayer $cltLayer)
    {
        //
    }
}
