<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'phone', 'role_id','profile_image', 'status','premium_plan','premium_expiry'];

    protected $hidden = ['password'];


     // One User has one Institute
    public function institute()
    {
        return $this->hasOne(Institute::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function provider()
    {
        return $this->hasOne(TrainingProvider::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    
    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'user_id');
    }
   public function trainingsEnrolled() {
    return $this->belongsToMany(Training::class, 'enrollments', 'user_id', 'training_id')
                ->withPivot('status', 'payment_id', 'created_at');
}


    
}
