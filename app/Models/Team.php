<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'owner_id',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Team owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Team members
    public function members()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // Add a member to the team
    public function addMember($user, $role = 'member')
    {
        $this->members()->attach($user, ['role' => $role]);
    }

    // Remove a member from the team
    public function removeMember($user)
    {
        $this->members()->detach($user);
    }

    // Check if user is a member
    public function hasMember($user)
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    // Generate slug automatically
    public static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            $team->slug = \Str::slug($team->name);
        });

        static::updating(function ($team) {
            $team->slug = \Str::slug($team->name);
        });
    }
    public function updateMemberRole($userId, $newRole)
{
    return $this->members()->updateExistingPivot($userId, ['role' => $newRole]);
}

}