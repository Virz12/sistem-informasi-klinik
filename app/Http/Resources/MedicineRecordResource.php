<?php

namespace App\Http\Resources;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineRecordResource extends JsonResource
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
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'medical_record' => new MedicalRecordResource($this->whenLoaded('medicalRecord')),
            'quantity' => $this->quantity,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
