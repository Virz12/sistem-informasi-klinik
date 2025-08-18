<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name', 'brand', 'dosage_form',
        'unit_price', 'stock_quantity', 'is_active'
    ];

    public function medicineRecords()
    {
        return $this->hasMany(MedicineRecord::class);
    }
}
