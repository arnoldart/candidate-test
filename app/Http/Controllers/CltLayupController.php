<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Contracts\CltLayupServiceInterface;

class CltLayupController extends Controller
{
    public function __construct(protected CltLayupServiceInterface $layupService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Supplier $supplier): View
    {
        $this->authorize('viewAny', CltLayup::class);

        $query = $supplier->cltLayups()->withCount('cltLayers')->withSum('cltLayers', 'thickness');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $layups = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('cltlayups.index', compact('supplier', 'layups'));
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
     public function store(CltLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('create', CltLayup::class);

        $supplier->cltLayups()->create($request->validated());
        return redirect()->route('layups.layers.index', $supplier->cltLayups()->latest('id')->first())
            ->with('success', 'CLT Layup berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CltLayup $cltLayup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CltLayup $cltLayup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CltLayupRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('update', $layup);

        $layup->update($request->validated());
        return redirect()->route('suppliers.layups.index', $supplier)
            ->with('success', 'CLT Layup berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('delete', $layup);

        $layup->delete();
        return redirect()->route('suppliers.layups.index', $supplier)
            ->with('success', 'CLT Layup berhasil dihapus.');
    }

    /**
     * Duplicate the specified resource and its layers.
     */
    public function duplicate(CltLayup $layup): RedirectResponse
    {
        $this->authorize('duplicate', $layup);

        $newLayup = $this->layupService->duplicate($layup);

        return redirect()->route('layups.layers.index', $newLayup)
            ->with('success', 'CLT Layup berhasil diduplikasi.');
    }
}
