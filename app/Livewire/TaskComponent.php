<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskComponent extends Component
{
    public $title;
    public $description;
    public $modal = false;

    public function render()
    {
        $user = Auth::user();
        $tasks = $user->tasks;
        return view('livewire.task-component', compact('tasks'));
    }

    public function clearFields()
    {
        $this->title = '';
        $this->description = '';
    }

    public function openModal()
    {
        $this->clearFields();
        $this->modal = true;
    }

    public function closeModal()
    {
        $this->clearFields();
        $this->modal = false;
    }

    public function createTask()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $task = new Task();
        $task->title = $this->title;
        $task->description = $this->description;
        $task->user_id = Auth::id();
        $task->created_at = now();
        $task->save();

        $this->closeModal();
    }
}
