<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
    ];

    protected $attributes = [
        'roles' => '["'.UserRole::EMPLOYEE->value.'"]',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'roles' => 'array',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Check if user has a specific role
     * 
     * @param UserRole|string $role
     * @return bool
     */
    public function hasRole($role)
    {
        if ($role instanceof UserRole) {
            $role = $role->value;
        }
        
        return in_array($role, $this->roles);
    }
    
    /**
     * Check if user has any of the given roles
     * 
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user has all of the given roles
     * 
     * @param array $roles
     * @return bool
     */
    public function hasAllRoles(array $roles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Add a role to the user
     * 
     * @param UserRole|string $role
     * @return $this
     */
    public function addRole($role)
    {
        if ($role instanceof UserRole) {
            $role = $role->value;
        }
        
        if (!in_array($role, $this->roles)) {
            $roles = $this->roles;
            $roles[] = $role;
            $this->roles = $roles;
        }
        
        return $this;
    }
    
    /**
     * Remove a role from the user
     * 
     * @param UserRole|string $role
     * @return $this
     */
    public function removeRole($role)
    {
        if ($role instanceof UserRole) {
            $role = $role->value;
        }
        
        $this->roles = array_values(array_diff($this->roles, [$role]));
        
        return $this;
    }
    
    /**
     * Set current active role for the session
     * 
     * @param UserRole|string $role
     * @return bool
     */
    public function setActiveRole($role)
    {
        if ($role instanceof UserRole) {
            $role = $role->value;
        }
        
        if ($this->hasRole($role)) {
            session(['active_role' => $role]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get current active role
     * 
     * @return string|null
     */
    public function getActiveRole()
    {
        return session('active_role', $this->roles[0] ?? null);
    }
    
    public function isAdmin()
    {
        return $this->getActiveRole() == UserRole::ADMIN->value;
    }

    public function isHr()
    {
        return $this->getActiveRole() == UserRole::HR->value;
    }

    public function isEmployee()
    {
        return $this->getActiveRole() == UserRole::EMPLOYEE->value;
    }
}