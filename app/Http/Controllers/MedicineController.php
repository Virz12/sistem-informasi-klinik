<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('manage_medicines');

        $medicines = Medicine::when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            })
            ->when($request->low_stock, function ($query) {
                $query->whereColumn('stock_quantity', '<=', 'minimum_stock');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Medicines/Index', [
            'medicines' => MedicineResource::collection($medicines),
            'filters' => $request->only(['search', 'low_stock', 'expired']),
            'stats' => [
                'total_medicines' => Medicine::count(),
                'low_stock_count' => Medicine::whereColumn('stock_quantity', '<=', 'minimum_stock')->count(),
                'expired_count' => Medicine::where('expiry_date', '<', now())->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('manage_medicines');

        return Inertia::render('Medicines/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Medicine::create([
            'code' => $this->generateMedicineCode(),
            ...$request->validated()
        ]);

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Medicine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        $this->authorize('manage_medicines');

        if ($medicine->medicineRecords()->count() > 0) {
            return back()->with('error', 'Cannot delete medicine with medicine record history.');
        }

        $medicine->delete();

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    private function generateMedicineCode(): string
    {
        $lastMedicine = Medicine::latest('id')->first();
        $nextId = $lastMedicine ? $lastMedicine->id + 1 : 1;
        return 'MED' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
