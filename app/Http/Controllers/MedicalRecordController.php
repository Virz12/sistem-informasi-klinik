<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Http\Resources\PatientResource;
use App\Models\Bill;
use App\Models\MedicalRecord;
use App\Models\MedicalServices;
use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view_medical_records');

        $records = MedicalRecord::with(['patient', 'doctor', 'medicineRecord'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('patient_id', 'like', "%{$search}%");
                })->orWhere('record_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('visit_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('MedicalRecords/Index', [
            'records' => MedicalRecordResource::collection($records),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create_medical_records');

        $patient = null;
        if ($request->patient_id) {
            $patient = Patient::with('district')->find($request->patient_id);
        }

        return Inertia::render('MedicalRecords/Create', [
            'patient' => $patient ? new PatientResource($patient) : null,
            'patients' => Patient::select('id', 'patient_id', 'name')
                ->orderBy('name')
                ->get(),
            'medicines' => Medicine::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->orderBy('name')
                ->get(['id', 'name', 'strength', 'unit_price', 'stock_quantity']),
            'actions' => MedicalServices::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalRecordRequest $request)
    {
        $data = $request->validated();

        $record = MedicalRecord::create([
            'record_number' => $this->generateRecordNumber(),
            'patient_id' => $data['patient_id'],
            'doctor_id' => Auth::user()->employee->id,
            'visit_date' => $data['visit_date'],
            'status' => 'in_progress'
        ]);

        // Create medicine records
        if (!empty($data['medicines'])) {
            foreach ($data['medicines'] as $medicine) {
                $medic = Medicine::find($medicine['medicine_id']);
                $totalPrice = $medic->unit_price * $medicine['quantity'];

                $record->medicineRecord()->create([
                    'medicine_id' => $medicine['medicine_id'],
                    'medical_record_id' => $record->id,
                    'quantity' => $medicine['quantity'],
                    'total_price' => $totalPrice,
                ]);

                // Update medicine stock
                $medic->decrement('stock_quantity', $medicine['quantity']);
            }
        }

        // Create bill
        $this->createBill($record, $data['actions'] ?? []);

        return redirect()
            ->route('medical-records.show', $record)
            ->with('success', 'Medical record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorize('view_medical_records');

        $medicalRecord->load([
            'patient.district',
            'doctor',
            'medicineRecords',
            'bill'
        ]);

        return Inertia::render('MedicalRecords/Show', [
            'record' => new MedicalRecordResource($medicalRecord),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $this->authorize('edit_medical_records');

        return Inertia::render('MedicalRecords/Edit', [
            'record' => new MedicalRecordResource($medicalRecord->load(['patient', 'doctor'])),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord)
    {
        $medicalRecord->update($request->validated());

        return redirect()
            ->route('medical-records.show', $medicalRecord)
            ->with('success', 'Medical record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $this->authorize('edit_medical_records');
        
        if ($medicalRecord->count() > 0) {
            return back()->with('error', 'Cannot delete medical record with medical records.');
        }

        $medicalRecord->delete();

        return redirect()
            ->route('medical-records.index')
            ->with('success', 'medical record deleted successfully.');
    }

    private function generateRecordNumber(): string
    {
        $lastRecord = MedicalRecord::latest('id')->first();
        $nextId = $lastRecord ? $lastRecord->id + 1 : 1;
        return 'MR' . date('Ymd') . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    private function createBill(MedicalRecord $record, array $actionIds = [])
    {
        $bill = Bill::create([
            'bill_number' => $this->generateBillNumber(),
            'patient_id' => $record->patient_id,
            'medical_record_id' => $record->id,
            'total_amount' => 0,
            'status' => 'pending',
        ]);

        $subtotal = 0;

        // Add medicine items
        foreach ($record->medicineRecords as $medicineRecord) {
            $subtotal += $medicineRecord->total_price;
        }

        // Add action items
        // if (!empty($actionIds)) {
        //     $actions = Action::whereIn('id', $actionIds)->get();
        //     foreach ($actions as $action) {
        //         $bill->billItems()->create([
        //             'type' => 'action',
        //             'item_id' => $action->id,
        //             'name' => $action->name,
        //             'quantity' => 1,
        //             'unit_price' => $action->price,
        //             'total_price' => $action->price,
        //         ]);
        //         $subtotal += $action->price;
        //     }
        // }

        $bill->update([
            'total_amount' => $subtotal,
        ]);
    }

    private function generateBillNumber(): string
    {
        $lastBill = Bill::latest('id')->first();
        $nextId = $lastBill ? $lastBill->id + 1 : 1;
        return 'BILL' . date('Ymd') . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
