<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'assigned_to',
        'status',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
        'due_date' => 'datetime',
    ];

    // Associated project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Assigned user
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Check if the task is completed
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    // Check if in progress
     public function isInProgress()
     {
         return $this->status === 'in progress';
     }
}

