<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'team_id',
        'owner_id',
        'status',
        
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Project owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Associated team
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Tasks related to the project
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
