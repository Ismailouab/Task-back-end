<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Add this line
use App\Notifications\CustomResetPassword;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;


    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'has_personal_workspace' => 'boolean',
    ];

    // Teams this user owns
    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    // Teams this user belongs to
    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // Update last active time
    public function updateLastActive()
    {
        $this->last_active_at = now();
        $this->save();
    }
    // Project owner relationship
    public function ownedProjects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    // In your User model
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}