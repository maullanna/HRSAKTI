<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_schedule';
    public $incrementing = true;
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'id_schedule';
    }

    protected $table = 'schedules';

    protected $fillable = [
        'time_in',
        'time_out',
        'name',
        'description'
    ];

    /**
     * Get the employees that belong to this schedule.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'schedule_employees', 'schedule_id', 'emp_id');
    }
}
