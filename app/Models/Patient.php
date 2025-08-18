<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'birth_data',
        'gender',
        'phone',
        'address',
        'allergies'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function medicineRecords()
    {
        return $this->hasMany(MedicineRecord::class);
    }
    
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
