<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'visit_date', 'status'
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

        public function medicineRecords()
    {
        return $this->hasMany(MedicineRecord::class);
    }

    public function patient()
    {
      return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
      return $this->belongsTo(Employee::class);
    }
}
