<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_salary';
    public $incrementing = true;
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'id_salary';
    }

    protected $fillable = [
        'employee_id',
        'month',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary'
    ];

    protected $casts = [
        'month' => 'date',
        'basic_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id_employees');
    }
}
