<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitWebController extends Controller
{
        public function index()
    {
        $units = Unit::paginate(10);
        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Unit', 'url' => '#']];
        return view('General.units', compact('units', 'breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = [['label' => 'Home', 'url' => route('units.index')], ['label' => 'Tambah Unit', 'url' => '#']];
        return view('General.units-add', compact('breadcrumbs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:units,name'
        ]);

        Unit::create($request->only('name'));

        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        $breadcrumbs = [['label' => 'Home', 'url' => route('units.index')], ['label' => 'Edit Data Unit', 'url' => '#']];
        return view('General.units-edit', compact('unit', 'breadcrumbs'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|unique:units,name,' . $unit->id
        ]);

        $unit->update($request->only('name'));

        return redirect()->route('units.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus.');
    }
}
