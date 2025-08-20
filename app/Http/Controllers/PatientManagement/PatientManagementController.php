<?php

namespace App\Http\Controllers\PatientManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\District;
use App\Models\Patient;
use Inertia\Inertia;

class PatientManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::with('district')
            ->latest()
            ->paginate(8);

        $districts = District::all();

        return Inertia::render('PatientManagement/Patient/Index', [
            'title' => 'Manajemen Pasien',
            'patients' => PatientResource::collection($patients),
            'districts' => $districts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $data = $request->validated();

        $data['patient_id'] = $this->generatePatientId();

        Patient::create($data);

        return redirect()->back()->with('success', 'Pasien berhasil didaftarkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $this->authorize('edit_patients');

        return Inertia::render('PatientManagement/Edit', [
            'patient' => new PatientResource($patient->load('district')),
            'district' => District::orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $data = $request->validated();

        $patient->update($data);

        return redirect()->back()->with('success', 'Pasien berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->back()->with('success', 'Pasien berhasil dihapus.');
    }

    private function generatePatientId(): string
    {
        $lastPatient = Patient::latest('id')->first();
        $nextId = $lastPatient ? $lastPatient->id + 1 : 1;
        return 'PAT' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
