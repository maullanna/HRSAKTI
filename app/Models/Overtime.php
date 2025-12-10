<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    // Set primary key
    protected $primaryKey = 'id_overtime';
    public $incrementing = true;
    protected $keyType = 'int';

    // Set route key name for route model binding
    public function getRouteKeyName()
    {
        return 'id_overtime';
    }

    protected $fillable = [
        'emp_id',
        'id_section',
        'id_section_employee',
        'id_wadir_employee',
        'id_sdm_employee',
        'id_director_employee',
        'overtime_date',
        'start_time',
        'end_time',
        'duration',
        'reason',
        'status',
        'section_approved',
        'section_approved_by',
        'section_approved_at',
        'wadir_approved',
        'wadir_approved_by',
        'wadir_approved_at',
        'sdm_approved',
        'sdm_approved_by',
        'sdm_approved_at',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'section_approved' => 'boolean',
        'wadir_approved' => 'boolean',
        'sdm_approved' => 'boolean',
        'section_approved_at' => 'datetime',
        'wadir_approved_at' => 'datetime',
        'sdm_approved_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'id_employees');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'id_section', 'id_section');
    }

    // Self-reference relationships
    public function sectionEmployee()
    {
        return $this->belongsTo(Employee::class, 'id_section_employee', 'id_employees');
    }

    public function wadirEmployee()
    {
        return $this->belongsTo(Employee::class, 'id_wadir_employee', 'id_employees');
    }

    public function sdmEmployee()
    {
        return $this->belongsTo(Employee::class, 'id_sdm_employee', 'id_employees');
    }

    public function directorEmployee()
    {
        return $this->belongsTo(Employee::class, 'id_director_employee', 'id_employees');
    }

    // Approver relationships
    public function sectionApprover()
    {
        return $this->belongsTo(Employee::class, 'section_approved_by', 'id_employees');
    }

    public function wadirApprover()
    {
        return $this->belongsTo(Employee::class, 'wadir_approved_by', 'id_employees');
    }

    public function sdmApprover()
    {
        return $this->belongsTo(Employee::class, 'sdm_approved_by', 'id_employees');
    }
}
