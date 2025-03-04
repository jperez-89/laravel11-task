<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use PhpParser\Node\Stmt\Return_;

class TaskComponent extends Component
{
    public $id;
    public $title;
    public $description;
    public $tasks = [];
    public $miTask;
    public $search;
    public $isEdit = false;
    public $showModal = false;
    public $shareModal = false;
    public $deleteModal = false;
    public $user_id;
    public $users = [];
    public $permiso;

    public function mount()
    {
        $this->tasks = $this->getTasks();
        $this->users = User::all()->except(Auth::user()->id);
    }

    public function loadTasks()
    {
        $this->tasks = $this->getTasks();
    }

    public function render()
    {
        return view('livewire.task-component');
    }

    public function getTasks()
    {
        // Se obtiene el usuario autenticado y se buscan las tareas que le pertenecen
        $misTasks = Task::where('user_id', Auth::user()->id)->where('title', 'like', '%' . $this->search . '%')->get();

        // Se obtienen las tareas compartidas con el usuario
        $misSharedTasks = Auth::user()->sharedTasks;

        // Se unen las tareas propias con las compartidas
        return collect($misTasks)->merge($misSharedTasks);
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
        $this->showModal = false;
    }

    public function createUpdateTask()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        // Si existe la tarea, se modifica, sino se crea
        if ($this->id) {
            $task = Task::find($this->id);
            $task->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);
            session()->flash('message', 'Tarea actualizada correctamente');
        } else {
            $task = Task::create([
                'title' => $this->title,
                'description' => $this->description,
                'user_id' => Auth::user()->id,
            ]);
            session()->flash('message', 'Tarea creada correctamente');
        }

        $this->closeModal();
    }

    public function openModal($id = null)
    {
        // Si se envia un id, se busca la tarea y se asignan los valores a las propiedades, sino se limpian los campos
        if ($id != null) {
            $task = Task::find($id);
            $this->id = $task->id;
            $this->title = $task->title;
            $this->description = $task->description;
            $this->isEdit = true;
        } else {
            $this->clearFields();
        }

        $this->showModal = true;
    }

    public function openShareModal(Task $task)
    {
        $this->miTask = $task;
        $this->shareModal = true;
    }

    public function shareTask()
    {
        $this->validate([
            'user_id' => 'required',
        ]);

        $task = Task::find($this->miTask->id);
        $user = User::find($this->user_id);
        $user->sharedTasks()->attach($task->id, ['permission' => $this->permiso]);
        $this->shareModal = false;

        $this->permiso = '';
        $this->user_id = '';

        // session()->flash('message', 'Tarea compartida correctamente');
    }

    public function taskUnShared(Task $task)
    {
        $user = User::find(Auth::user()->id);
        $user->sharedTasks()->detach($task->id);
        // session()->flash('message', 'Tarea descompartidas correctamente');
    }

    public function openDeleteModal($id)
    {
        $this->id = $id;
        $this->deleteModal = true;
    }

    public function deleteTask($id)
    {
        Task::destroy($id);
        $this->deleteModal = false;
        // session()->flash('message', 'Tarea eliminada correctamente');
    }
}
