<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
            'bill_number' => $this->bill_number,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'medical_record' => new MedicalRecordResource($this->whenLoaded('medicalRecord')),
            'total_amount' => number_format($this->total_amount, 2),
            'status' => $this->status,
            'cashier' => new EmployeeResource($this->whenLoaded('cashier')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
