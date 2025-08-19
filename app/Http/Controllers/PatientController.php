<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientResource;
use App\Models\District;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view_patients');
        
        $patients = Patient::with('district')
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
            })
            ->paginate(15)
            ->withQueryString();
            
        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => request()->only('search')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create_patients');
        
        return Inertia::render('Patients/Create', [
            'districts' => District::get(['id', 'name'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $patient = Patient::create([
            'patient_id' => $this->generatePatientId(),
            ...$request->validated()
        ]);
        
        return redirect()->route('patients.index')
            ->with('success', 'Patient created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $this->authorize('view_patients');

        $patient->load([
            'district',
            'medicalRecords.doctor',
            'medicalRecords.prescriptions.medicine',
            'bills.cashier'
        ]);

        return Inertia::render('Patients/Show', [
            'patient' => new PatientResource($patient),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $this->authorize('edit_patients');

        return Inertia::render('Patients/Edit', [
            'patient' => new PatientResource($patient->load('district')),
            'district' => District::orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $patient->update($request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $this->authorize('edit_patients');

        if ($patient->medicalRecords()->count() > 0) {
            return back()->with('error', 'Cannot delete patient with medical records.');
        }

        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    private function generatePatientId(): string
    {
        $lastPatient = Patient::latest('id')->first();
        $nextId = $lastPatient ? $lastPatient->id + 1 : 1;
        return 'PAT' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
