<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $districts = District::latest()->paginate(10);

        return Inertia::render('Admin/District/Index', [
            'title' => 'Manajemen Wilayah',
            'districts' => $districts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        District::create($validated);

        return redirect()->route('admin.district.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(District $district)
    {
        return Inertia::render('Admin/District/Edit', [
            'title' => 'Edit District',
            'district' => $district
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, District $district)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $district->update($validated);

        return redirect()->route('admin.district.index')->with('success', 'Wilayah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district->delete();

        return redirect()->route('admin.district.index')->with('success', 'Wilayah berhasil dihapus.');
    }
}
