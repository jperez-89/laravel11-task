<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskComponent extends Component
{
    public $id;
    public $title;
    public $description;
    public $modal = false;
    public $isEdit = false;

    public function render()
    {
        $user = Auth::user();
        $tasks = $user->tasks;
        return view('livewire.task-component', compact('tasks'));
    }

    public function clearFields()
    {
        $this->id = '';
        $this->title = '';
        $this->description = '';
        $this->isEdit = false;
    }

    public function closeModal()
    {
        $this->clearFields();
        $this->modal = false;
    }

    public function createUpdateTask()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        Task::updateOrCreate([
            'id' => $this->id,
        ], [
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => Auth::user()->id,
            'created_at' => now()
        ]);

        $this->closeModal();
    }

    public function openModal($id = null)
    {
        if ($id != null) {
            $task = Task::find($id);
            $this->id = $task->id;
            $this->title = $task->title;
            $this->description = $task->description;
            $this->isEdit = true;
        } else {
            $this->clearFields();
        }

        $this->modal = true;
    }

    public function deleteTask($id)
    {
        Task::destroy($id);
    }
}
