<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;
    
    public function getRouteKeyName()
    {
        return 'id';
    }
    protected $table = 'employees';
    protected $fillable = [
        'name', 'employee_code', 'nik', 'tanggal_lahir', 'pendidikan', 'kontrak_kerja', 'position', 'email', 'pin_code', 'phone', 'address', 'basic_salary', 'hire_date', 'status', 'role_id', 'section_id', 'wadir_id'
    ];

  
    protected $hidden = [
        'pin_code', 'remember_token',
    ];


    public function check()
    {
        return $this->hasMany(Check::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
    public function latetime()
    {
        return $this->hasMany(Latetime::class);
    }
    public function leave()
    {
        return $this->hasMany(Leave::class);
    }
    public function overtime()
    {
        return $this->hasMany(Overtime::class);
    }
    public function schedules()
    {
        return $this->belongsToMany('App\Models\Schedule', 'schedule_employees', 'emp_id', 'schedule_id');
    }

    /**
     * Get the password for the user.
     * For employee, we use pin_code as password
     */
    public function getAuthPassword()
    {
        return $this->pin_code;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

}
