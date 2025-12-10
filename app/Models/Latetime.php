<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Latetime extends Model
{
    protected $primaryKey = 'id_latetime';
    public $incrementing = true;
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'id_latetime';
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'id_employees');
    }
}
