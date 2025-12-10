<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $primaryKey = 'id_role';
    public $incrementing = true;
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'id_role';
    }

    protected $fillable = ['slug', 'name', 'permissions', 'description', 'is_active'];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'role_users', 'role_id', 'user_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'role_id', 'id_role');
    }
}
