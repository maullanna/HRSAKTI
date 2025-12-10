<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    protected $primaryKey = 'id_leave';
    public $incrementing = true;
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'id_leave';
    }

    protected $fillable = [
        'emp_id',
        'leave_date',
        'leave_time',
        'type',
        'state',
        'status',
        'uid'
    ];

    protected $casts = [
        'leave_date' => 'date',
        'leave_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'id_employees');
    }

    public function getLeaveTypeAttribute()
    {
        $types = [
            'sick' => 'Sick Leave',
            'vacation' => 'Vacation Leave',
            'personal' => 'Personal Leave',
            'emergency' => 'Emergency Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'study' => 'Study Leave',
            'other' => 'Other'
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary'
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
