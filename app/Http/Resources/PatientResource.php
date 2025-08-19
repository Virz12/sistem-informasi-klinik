<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'patient_id' => $this->patient_id,
            'name' => $this->name,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'age' => $this->birth_date?->age,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'district' => new DistrictResource($this->whenLoaded('district')),
            'allergies' => $this->allergies,
            'medical_records_count' => $this->when(
                $this->relationLoaded('medicalRecords'),
                fn () => $this->medicalRecords->count()
            ),
            'last_visit' => $this->when(
                $this->relationLoaded('medicalRecords'),
                fn () => $this->medicalRecords->sortByDesc('visit_date')->first()?->visit_date?->format('Y-m-d H:i')
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
