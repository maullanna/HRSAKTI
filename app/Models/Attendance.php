<?php

namespace App\Models;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model

{

    protected $table = 'attendances';
    
    protected $fillable = [
        'uid', 'emp_id', 'state', 'attendance_time', 'attendance_date', 'status', 'type'
    ];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'id_employees');
    }
}
