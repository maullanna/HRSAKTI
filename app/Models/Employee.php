<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $primaryKey = 'id_employees';
    public $incrementing = true;
    protected $keyType = 'int';
    
    public function getRouteKeyName()
    {
        return 'id_employees';
    }
    protected $table = 'employees';
    protected $fillable = [
        'name', 'employee_code', 'nik', 'tanggal_lahir', 'pendidikan', 'kontrak_kerja', 'kontrak_durasi', 'position', 'email', 'pin_code', 'phone', 'hire_date', 'status', 'role_id', 'section_id', 'wadir_id'
    ];

  
    protected $hidden = [
        'pin_code', 'remember_token',
    ];

    /**
     * Accessor: Allow $employee->id to still work by returning id_employees
     * This maintains backward compatibility while using id_employees as primary key
     */
    public function getIdAttribute()
    {
        return $this->attributes['id_employees'] ?? $this->getKey();
    }


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

    /**
     * Get contract end date
     * @return Carbon|null
     */
    public function getContractEndDateAttribute()
    {
        if (!$this->hire_date || !$this->kontrak_durasi) {
            return null;
        }
        
        $needDuration = ['Magang', 'Kontrak', 'PKL', 'Freelance'];
        if (!in_array($this->kontrak_kerja, $needDuration)) {
            return null; // Tetap tidak punya end date
        }
        
        return Carbon::parse($this->hire_date)->addMonths($this->kontrak_durasi);
    }

    /**
     * Check if contract is expiring soon (within 1 month)
     * @return bool
     */
    public function isContractExpiringSoon()
    {
        $endDate = $this->contract_end_date;
        if (!$endDate) {
            return false;
        }
        
        $oneMonthFromNow = Carbon::now()->addMonth();
        return $endDate->lte($oneMonthFromNow) && $endDate->gte(Carbon::now());
    }

    /**
     * Check if contract is expired
     * @return bool
     */
    public function isContractExpired()
    {
        $endDate = $this->contract_end_date;
        if (!$endDate) {
            return false;
        }
        
        return $endDate->lt(Carbon::now());
    }

    /**
     * Get days until contract expires
     * @return int|null
     */
    public function getDaysUntilContractExpires()
    {
        $endDate = $this->contract_end_date;
        if (!$endDate) {
            return null;
        }
        
        return Carbon::now()->diffInDays($endDate, false); // negative if expired
    }

}
