<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'due_date',
        'status',
        'task_list_id',
    ];

    public function markAsComplete()
    {
        $this->update(['status' => 'completed']);
        $this->save();

        $allTasksCompleted = !Task::where('task_list_id', $this->task_list_id)->where('status', '<>', 'completed')->exists();

        if ($allTasksCompleted) {
            $taskList = TaskList::find($this->task_list_id);
            $taskList->update(['status' => 'completed']);
        }
    }

    public function markAsPending()
    {
        $this->update(['status' => 'pending']);

        $taskList = TaskList::find($this->task_list_id);
        if ($taskList->status == 'completed') {
            $taskList->update(['status' => 'pending']);
        }
    }
}