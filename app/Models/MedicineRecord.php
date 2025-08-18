<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineRecord extends Model
{
    protected $fillable = [
        'patient_id', 'medical_record_id', 'medicine_id',
        'quantity'
    ];

    public function patient()
    {
      return $this->belongsTo(Patient::class);
    }

    public function medicalRecord()
    {
      return $this->belongsTo(MedicalRecord::class);
    }

    public function medicine()
    {
      return $this->belongsTo(Medicine::class);
    }
}
