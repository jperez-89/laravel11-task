<section class="bg-white dark:bg-gray-800">
    <div class="container">
        @if (session()->has('message'))
            <div class="flex bg-blue-500 text-white text-sm font-bold px-4 py-3" role="alert" x-data="{ show: true }"
                x-show="show">
                <p>{{ session('message') }}</p>
                <span class="text-white text-sm font-bold top-0 bottom-0 right-0 px-4 py-3" @click="show = false">
                    X
                </span>
            </div>
        @endif
        <div class="pt-2 relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="px-4 py-5 text-gray-700 border-b border-gray-200 gap-x-16 dark:border-gray-700">
                <div class="flex justify-between">
                    <div class="flex space-x-2">
                        <button wire:click="openModal" data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                            class="text-white text-sm block bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                            type="button">
                            Nuevo
                        </button>

                        <button wire:click="deleteAllTasks"
                            wire.comfirm="¿Estás seguro de que quieres eliminar todas las tareas?"
                            class="text-white text-sm block bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                            type="button">
                            Borrar todas las Tareas
                        </button>

                        <button wire:click="recoverAllTasks"
                            wire.comfirm="¿Estás seguro de que quieres recuperar todas las tareas?"
                            class="text-white text-sm block bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg px-5 py-2.5 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800"
                            type="button">
                            Recuperar Tareas
                        </button>
                    </div>

                    <input wire:model.debounce.100ms="search" type="text"
                        class="w-64 px-3 py-2 text-sm text-gray-700 placeholder-gray-400 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Buscar por título...">
                </div>

                <table class="my-2 w-full text-sm text-left rtl text-gray-500 dark:text-gray-400" wire:poll="loadTasks">
                    <thead class=" text-gray-800 uppercase bg-blue-300 dark:bg-blue-700 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Título
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Descripción
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Acciones
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $task->id }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $task->title }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $task->description }}
                                </td>
                                <td class="px-6 py-4">
                                    @if (isset($task->pivot))
                                        <div class="flex flex-row space-x-1">
                                            <x-custom-button x-data=""
                                                wire:click="taskUnShared({{ $task }})">{{ __('Descompartir') }}</x-custom-button>
                                        </div>
                                    @endif
                                    @if ((isset($task->pivot) && $task->pivot->permission == 'edit') || Auth::user()->id == $task->user_id)
                                        <div class="flex flex-row space-x-1">
                                            <x-primary-button x-data=""
                                                wire:click="openModal({{ $task->id }})">{{ __('Editar') }}</x-primary-button>

                                            <x-secondary-button x-data=""
                                                wire:click="openShareModal({{ $task }})">{{ __('Compartir') }}</x-secondary-button>

                                            {{-- @if (isset($task->pivot) && ($task->pivot->permission == 'edit' || $task->pivot->permission == 'view'))
                                                <x-secondary-button x-data=""
                                                    wire:click="openShareModal({{ $task }})">{{ __('Compartir') }}</x-secondary-button>
                                            @else
                                                <x-secondary-button x-data=""
                                                    wire:click="taskUnShared({{ $task }})">{{ __('Descompartir') }}</x-secondary-button>
                                            @endif --}}
                                            <x-danger-button x-data=""
                                                wire:click="openDeleteModal({{ $task->id }})">{{ __('Eliminar') }}</x-danger-button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Crear/Actualizar --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            <!-- Modal overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
            <!-- Modal content -->
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            @if ($isEdit)
                                Editar Tarea
                            @else
                                Crear Tarea
                            @endif
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5">
                        @csrf
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="col-span-2">
                                <label for="title"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título</label>
                                <input wire:model='title' type="text" name="title" id="title"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="" required="">
                            </div>
                            <div class="col-span-2">
                                <label for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción</label>
                                <textarea wire:model='description' id="description" rows="4"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder=""></textarea>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center p-4 md:p-5 border-trounded-b">
                            <button wire:click="createUpdateTask" data-modal-hide="static-modal" type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                @if ($isEdit)
                                    Actualizar
                                @else
                                    Guardar
                                @endif
                            </button>
                            <button wire:click="closeModal()" data-modal-hide="static-modal" type="button"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Compartir --}}
    <div id="shareModal" x-data="{ open: @entangle('shareModal') }" x-show="open"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            <!-- Modal overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
            <!-- Modal content -->
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Compartir Tarea
                        </h3>
                        <button type="button" @click="open = false"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-toggle="shareModal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5">
                        @csrf
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="col-span-2">
                                <label for="title"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Usuario</label>
                                <select wire:model='user_id' name="user_id" id="user_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="" required="">
                                    <option value="">Seleccionar Usuario</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Permiso</label>
                                <select wire:model='permiso' name="permiso" id="permiso"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="" required="">
                                    <option value="">Seleccionar Permiso</option>
                                    <option value="view">Ver</option>
                                    <option value="edit">Editar</option>
                                </select>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center p-4 md:p-5 border-trounded-b">
                            <button wire:click="shareTask" data-modal-hide="static-modal" type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Compartir
                            </button>

                            <button @click="open = false" data-modal-hide="static-modal" type="button"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Confirmar Eliminación --}}
    <div x-data="{ open: @entangle('deleteModal') }" x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            <!-- Modal overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
            <!-- Modal content -->
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <form wire:submit="deleteTask({{ $this->id }})" class="p-6">

                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('¿Estás seguro de que quieres eliminar esta tarea?') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Una vez que se elimine la tarea, todos sus recursos y datos se eliminarán de forma permanente.') }}
                        </p>

                        <div class="mt-6 flex justify-end">
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                @click="open = false">
                                Cancelar
                            </button>

                            <x-danger-button class="ms-3">
                                {{ __('Eliminar Tarea') }}
                            </x-danger-button>

                            {{-- <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-3">
                                Eliminar Tarea
                            </button> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
