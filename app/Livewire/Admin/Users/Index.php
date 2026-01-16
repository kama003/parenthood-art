<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $name = '';
    public $email = '';
    public $role = 'hospital';
    public $password = '';
    public $generatedPassword = null;
    public $editingUserId = null;

    public function create()
    {
        $this->resetForm();
        $this->modal('user-modal')->show();
    }

    public function generatePassword()
    {
        $this->password = Str::random(10);
        $this->generatedPassword = $this->password;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = ''; // Don't show existing password
        $this->generatedPassword = null;

        $this->modal('user-modal')->show();
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,hospital,doctor',
        ];

        if ($this->editingUserId) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->editingUserId;
            $rules['password'] = 'nullable|string|min:8';
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update($data);
            $message = 'User updated successfully.';
        } else {
            User::create($data);
            $message = 'User created successfully.';
            
            // If we generated a password, we might want to show it in a flashing message or ensure the admin noted it.
            if ($this->generatedPassword) {
                $message .= ' Password: ' . $this->generatedPassword;
            }
        }

        $this->modal('user-modal')->close();
        $this->dispatch('toast', message: $message);
        
        $this->resetForm();
    }

    public function delete($id)
    {
        if ($id === auth()->id()) {
            $this->dispatch('toast', message: 'You cannot delete your own account.', type: 'error');
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();

        $this->dispatch('toast', message: 'User deleted successfully.');
    }

    public function resetForm()
    {
        $this->editingUserId = null;
        $this->reset(['name', 'email', 'role', 'password', 'generatedPassword']);
        $this->role = 'hospital';
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return view('livewire.admin.users.index', [
            'users' => User::latest()->paginate(10),
        ]);
    }
}
