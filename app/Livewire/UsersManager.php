<?php

namespace App\Livewire;

use App\Models\Institucion;
use App\Models\Persona;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UsersManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $perPage = 8;

    public $showModal = false;
    public $modalTitle = 'Nuevo Usuario';
    public $editingUserId = null;
    public $personaId = null;
    
    public $showDeleteModal = false;
    public $userToDeleteId = null;
    public $userToDeleteName = '';

    public $tipo = 'docente';
    public $nombres = '';
    public $apellidos = '';
    public $dni = '';
    public $telefono = '';
    public $direccion = '';
    public $fecha_nacimiento = null;
    public $email = '';
    public $role_id = null;
    public $institucion_id = null;
    public $password = '';
    public $password_confirmation = '';

    protected bool $isAdmin = false;
    protected ?int $currentInstitutionId = null;
    public bool $canSelectInstitution = false;

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user && $user->canManageUsers(), 403);
        
        // Cargar la relación role si no está cargada
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        // Verificar si es admin: rol_id = 1 o por nombre del rol
        $this->isAdmin = $user->role_id == 1 || $user->isAdmin();
        $this->currentInstitutionId = $user->institucion_id;
        if (!$this->isAdmin && !$this->currentInstitutionId) {
            abort(403, 'Tu usuario necesita una institución asignada.');
        }
        $this->canSelectInstitution = $this->isAdmin;
        if (!$this->isAdmin) {
            $this->institucion_id = $this->currentInstitutionId;
        }
    }

    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios',
        'apellidos.required' => 'Los apellidos son obligatorios',
        'email.required' => 'El correo es obligatorio',
        'email.email' => 'Ingrese un correo válido',
        'password.required' => 'La contraseña es obligatoria',
        'password.same' => 'Las contraseñas no coinciden',
        'role_id.required' => 'Seleccione un rol',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleId(): void
    {
        if (!$this->roleRequiresInstitution()) {
            $this->institucion_id = null;
        }
    }

    public function updated($property): void
    {
        if (in_array($property, ['nombres', 'apellidos', 'direccion'])) {
            $this->{$property} = mb_strtoupper($this->{$property});
        }

        if ($property === 'dni') {
            $this->dni = substr(preg_replace('/\D/', '', $this->dni ?? ''), 0, 8);
        }

        if ($property === 'telefono') {
            $this->telefono = substr(preg_replace('/\D/', '', $this->telefono ?? ''), 0, 9);
        }

        if ($property === 'email') {
            $this->email = strtolower($this->email);
        }
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->modalTitle = 'Registrar Usuario';
        if (!$this->isAdmin) {
            $this->institucion_id = $this->currentInstitutionId;
        }
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = $this->loadUser($userId);
        $this->editingUserId = $user->id;
        $this->personaId = optional($user->persona)->id;
        $this->tipo = optional($user->persona)->tipo ?? 'docente';
        $this->nombres = optional($user->persona)->nombres ?? '';
        $this->apellidos = optional($user->persona)->apellidos ?? '';
        $this->dni = optional($user->persona)->dni ?? '';
        $this->telefono = optional($user->persona)->telefono ?? '';
        $this->direccion = optional($user->persona)->direccion ?? '';
        $this->fecha_nacimiento = optional($user->persona)->fecha_nacimiento?->format('Y-m-d');
        $this->email = $user->email;
        $this->role_id = $user->role_id;
        $this->institucion_id = $user->institucion_id;
        $this->password = '';
        $this->password_confirmation = '';
        $this->modalTitle = 'Editar Usuario';
        $this->showModal = true;
    }

    public function save(): void
    {
        // Verificar que el usuario actual sigue autenticado y tiene permisos
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->canManageUsers()) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }
        
        // Recargar el estado de admin por si cambió
        if (!$currentUser->relationLoaded('role')) {
            $currentUser->load('role');
        }
        $isAdmin = $currentUser->role_id == 1 || $currentUser->isAdmin();
        
        // Verificar autorización antes de guardar
        if ($this->editingUserId) {
            $userToEdit = User::findOrFail($this->editingUserId);
            // Si no es admin, solo puede editar usuarios de su institución
            if (!$isAdmin && $userToEdit->institucion_id !== $currentUser->institucion_id) {
                abort(403, 'No tienes permisos para editar este usuario.');
            }
        }

        $this->validate($this->rules());

        $institucionId = $isAdmin ? $this->institucion_id : $currentUser->institucion_id;

        $personaData = [
            'tipo' => $this->tipo,
            'nombres' => mb_strtoupper($this->nombres),
            'apellidos' => mb_strtoupper($this->apellidos),
            'dni' => $this->dni ?: null,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'direccion' => mb_strtoupper($this->direccion),
            'telefono' => $this->telefono,
            'email' => strtolower($this->email),
            'institucion_id' => $institucionId,
        ];

        $persona = Persona::updateOrCreate(['id' => $this->personaId], $personaData);
        $this->personaId = $persona->id;

        $userData = [
            'name' => trim($this->nombres . ' ' . $this->apellidos),
            'email' => strtolower($this->email),
            'role_id' => $this->role_id,
            'persona_id' => $persona->id,
            'institucion_id' => $institucionId,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(
            ['id' => $this->editingUserId],
            $userData
        );

        $this->dispatch('swal', icon: 'success', title: 'Usuario guardado correctamente');

        $this->showModal = false;
        $this->resetForm();
    }

    public function deleteUser(): void
    {
        if (!$this->userToDeleteId) {
            return;
        }
        
        try {
            $user = $this->loadUser($this->userToDeleteId);
            $persona = $user->persona;
            $user->delete();

            if ($persona) {
                $persona->delete();
            }

            $this->showDeleteModal = false;
            $this->userToDeleteId = null;
            $this->userToDeleteName = '';
            $this->dispatch('swal', icon: 'success', title: 'Usuario eliminado');
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('swal', icon: 'error', title: 'Error al eliminar usuario', text: $e->getMessage());
        }
    }

    public function confirmUserDeletion(int $userId): void
    {
        $user = User::with('persona')->findOrFail($userId);
        $this->userToDeleteId = $userId;
        $this->userToDeleteName = optional($user->persona)->nombres . ' ' . optional($user->persona)->apellidos;
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->userToDeleteId = null;
        $this->userToDeleteName = '';
    }

    protected function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(['estudiante', 'docente'])],
            'nombres' => ['required', 'string', 'max:150'],
            'apellidos' => ['required', 'string', 'max:150'],
            'dni' => [
                'nullable',
                'digits:8',
                Rule::unique('personas', 'dni')->ignore($this->personaId),
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
                Rule::unique('personas', 'email')->ignore($this->personaId),
            ],
            'telefono' => ['nullable', 'digits:9'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'institucion_id' => [
                'nullable',
                'exists:instituciones,id',
                function ($attribute, $value, $fail) {
                    if ($this->roleRequiresInstitution() && !$value) {
                        $fail('La institución es obligatoria para este rol.');
                    }
                },
            ],
            'password' => [
                $this->editingUserId ? 'nullable' : 'required',
                'min:6',
                'same:password_confirmation',
            ],
            'password_confirmation' => [$this->editingUserId ? 'nullable' : 'required'],
        ];
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingUserId',
            'personaId',
            'tipo',
            'nombres',
            'apellidos',
            'dni',
            'telefono',
            'direccion',
            'fecha_nacimiento',
            'email',
            'role_id',
            'institucion_id',
            'password',
            'password_confirmation',
        ]);
        $this->tipo = 'docente';
        if (!$this->isAdmin) {
            $this->institucion_id = $this->currentInstitutionId;
        }
    }

    protected function roleRequiresInstitution(): bool
    {
        if (!$this->role_id) {
            return true;
        }

        $role = Role::find($this->role_id);
        if (!$role) {
            return true;
        }

        return !in_array(strtolower($role->name), ['admin', 'ugel']);
    }

    public function getRequiresInstitucionProperty(): bool
    {
        return $this->roleRequiresInstitution();
    }

    public function render()
    {
        $users = User::with(['persona.institucion', 'role', 'institucion'])
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('email', 'like', '%' . $this->search . '%')
                        ->orWhereHas('persona', function ($personaQuery) {
                            $personaQuery->where('nombres', 'like', '%' . $this->search . '%')
                                ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                                ->orWhere('dni', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('role', function ($roleQuery) {
                            $roleQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when(!$this->isAdmin && $this->currentInstitutionId, function ($query) {
                $query->where('institucion_id', $this->currentInstitutionId);
            })
            ->latest()
            ->paginate($this->perPage);

        $roles = Role::orderBy('name')->get();
        $instituciones = Institucion::orderBy('nombre')
            ->when(!$this->isAdmin && $this->currentInstitutionId, fn ($q) => $q->whereKey($this->currentInstitutionId))
            ->get();

        return view('livewire.users-manager', [
            'users' => $users,
            'roles' => $roles,
            'instituciones' => $instituciones,
            'canSelectInstitution' => $this->canSelectInstitution,
        ]);
    }

    protected function loadUser(int $userId): User
    {
        $user = User::with(['persona', 'institucion'])->findOrFail($userId);
        
        // Verificar autorización actual
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->canManageUsers()) {
            abort(403, 'No tienes permisos para acceder a este usuario.');
        }
        
        // Cargar relación role si no está cargada
        if (!$currentUser->relationLoaded('role')) {
            $currentUser->load('role');
        }
        
        $isAdmin = $currentUser->role_id == 1 || $currentUser->isAdmin();
        
        if ($isAdmin) {
            return $user;
        }
        
        if ($user->institucion_id !== $currentUser->institucion_id) {
            abort(403, 'No tienes permisos para acceder a este usuario.');
        }
        return $user;
    }
}
