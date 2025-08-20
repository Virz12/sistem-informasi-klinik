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
        'patient_id',
        'birth_date',
        'gender',
        'district_id',
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
}
