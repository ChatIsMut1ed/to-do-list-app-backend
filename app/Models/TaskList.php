<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'user_id'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_list_id', 'id');
    }

    public function updateStatus()
    {
        $pendingTasks = $this->tasks()->where('status', 'pending')->count();
        if ($pendingTasks == 0) {
            $this->status = 'completed';
        } else {
            $this->status = 'pending';
        }
        $this->save();
    }
}