<?php

namespace App\Livewire\Couples;

use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $registrationNumber = '';
    public $partner1Name = '';
    public $partner1Aadhaar; // File object
    public $partner2Name = '';
    public $partner2Aadhaar; // File object
    public $contactNumber = '';
    public $email = '';
    public $status = 'active';

    public $editingCoupleId = null;

    public function mount()
    {
        $this->generateRegistrationNumber();
    }

    public function generateRegistrationNumber()
    {
        if ($this->editingCoupleId) return;

        $latest = \App\Models\Couple::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $this->registrationNumber = 'C-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    public function create()
    {
        $this->resetForm();
        $this->modal('couple-modal')->show();
    }

    public function edit($id)
    {
        $couple = \App\Models\Couple::findOrFail($id);
        
        $this->editingCoupleId = $couple->id;
        $this->registrationNumber = $couple->registration_number;
        $this->partner1Name = $couple->partner_1_name;
        $this->partner2Name = $couple->partner_2_name;
        $this->contactNumber = $couple->contact_number;
        $this->email = $couple->email;
        $this->status = $couple->status;

        $this->modal('couple-modal')->show();
    }

    public function save()
    {
        $rules = [
            'partner1Name' => 'required|string|max:255',
            'partner1Aadhaar' => 'nullable|image|max:2048',
            'partner2Name' => 'nullable|string|max:255',
            'partner2Aadhaar' => 'nullable|image|max:2048',
            'contactNumber' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required',
        ];

        // Additional validation logic if needed
        // e.g. require partner1aadhaar on creation? Maybe optional/required depending on strictness.
        // Assuming optional above, but let's make partner1 file required on creation if strict.
        // if (!$this->editingCoupleId) { 
        //    $rules['partner1Aadhaar'] = 'required|image|max:2048'; 
        // }

        if (!$this->editingCoupleId) {
            $rules['registrationNumber'] = 'required|unique:couples,registration_number';
        }

        $this->validate($rules);

        $data = [
            'partner_1_name' => $this->partner1Name,
            'partner_2_name' => $this->partner2Name,
            'contact_number' => $this->contactNumber,
            'email' => $this->email,
            'status' => $this->status,
        ];

        if ($this->partner1Aadhaar) {
            $data['partner_1_aadhaar_path'] = $this->partner1Aadhaar->store('aadhaar_proofs', 'public');
        }
        if ($this->partner2Aadhaar) {
            $data['partner_2_aadhaar_path'] = $this->partner2Aadhaar->store('aadhaar_proofs', 'public');
        }

        if ($this->editingCoupleId) {
            $couple = \App\Models\Couple::find($this->editingCoupleId);
            $couple->update($data);
            $message = 'Couple updated successfully.';
        } else {
            $data['registration_number'] = $this->registrationNumber;
            // Associate with logged-in user if not admin, or just generally useful.
            $data['user_id'] = auth()->id();
            \App\Models\Couple::create($data);
            $message = 'Couple registered successfully.';
        }

        $this->resetForm();
        $this->modal('couple-modal')->close();
        $this->dispatch('toast', message: $message);
    }

    public function resetForm()
    {
        $this->editingCoupleId = null;
        $this->reset(['partner1Name', 'partner2Name', 'contactNumber', 'email', 'status', 'partner1Aadhaar', 'partner2Aadhaar']);
        $this->generateRegistrationNumber();
        $this->status = 'active';
    }

    public function delete($id)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        $couple = \App\Models\Couple::findOrFail($id);
        $couple->delete();

        $this->dispatch('toast', message: 'Couple deleted successfully.', type: 'success');
    }

    public $selected = [];

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        if (empty($this->selected)) {
            return;
        }

        \App\Models\Couple::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->dispatch('toast', message: 'Selected couples deleted successfully.', type: 'success');
    }

    public $search = '';

    public function render()
    {
        $query = \App\Models\Couple::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('registration_number', 'like', '%' . $this->search . '%')
                  ->orWhere('partner_1_name', 'like', '%' . $this->search . '%')
                  ->orWhere('partner_2_name', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_number', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        if (auth()->user()->isHospital()) {
            $query->where('user_id', auth()->id());
        }

        return view('livewire.couples.index', [
            'couples' => $query->latest()->get(),
        ]);
    }
}
