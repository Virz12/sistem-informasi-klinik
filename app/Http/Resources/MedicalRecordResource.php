<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'record_number' => $this->record_number,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'doctor' => new EmployeeResource($this->whenLoaded('doctor')),
            'visit_date' => $this->visit_date?->format('Y-m-d H:i'),
            'chief_complaint' => $this->chief_complaint,
            'physical_examination' => $this->physical_examination,
            'diagnosis' => $this->diagnosis,
            'treatment_plan' => $this->treatment_plan,
            'notes' => $this->notes,
            'status' => $this->status,
            'medicine_record' => MedicineRecordResource::collection($this->whenLoaded('medicineRecord')),
            'bill' => new BillResource($this->whenLoaded('bill')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
