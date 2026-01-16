<?php

namespace App\Livewire\HospitalOrders;

use Livewire\Component;

use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;
    
    public $tab = 'all';

    public $hospitalName = '';
    public $coupleId = '';
    public $sampleId = '';
    public $vialsCount = 1;
    public $notes = '';
    public $aadhaarFile;
    public $declarationAccepted = false;

    public $editingOrderId = null;

    public $createNewCouple = false;
    public $newPartner1Name = '';
    public $newPartner2Name = '';
    public $newContactNumber = '';
    public $newEmail = '';

    public $assignedHospitalId = '';

    public $viewOnly = false;

    public function create()
    {
        $this->resetForm();
        $this->modal('new-order')->show();
    }

    public function edit($id)
    {
        $order = \App\Models\HospitalOrder::findOrFail($id);
        
        $this->editingOrderId = $order->id;
        $this->assignedHospitalId = $order->user_id;
        $this->hospitalName = $order->hospital_name;
        $this->coupleId = $order->couple_id;
        $this->sampleId = $order->sample_id;
        $this->vialsCount = $order->vials_count;
        $this->notes = $order->notes;
        $this->declarationAccepted = (bool) $order->declaration_accepted;
        $this->viewOnly = false;

        $this->modal('new-order')->show();
    }

    public function view($id)
    {
        $order = \App\Models\HospitalOrder::findOrFail($id);
        
        $this->editingOrderId = $order->id;
        $this->assignedHospitalId = $order->user_id;
        $this->hospitalName = $order->hospital_name;
        $this->coupleId = $order->couple_id;
        $this->sampleId = $order->sample_id;
        $this->vialsCount = $order->vials_count;
        $this->notes = $order->notes;
        $this->declarationAccepted = (bool) $order->declaration_accepted;
        $this->viewOnly = true;

        $this->modal('new-order')->show();
    }

    public function resetForm()
    {
        $this->editingOrderId = null;
        $this->viewOnly = false;
        $this->reset([
            'hospitalName', 'coupleId', 'sampleId', 'vialsCount', 'notes', 'aadhaarFile', 'declarationAccepted',
            'createNewCouple', 'newPartner1Name', 'newPartner2Name', 'newContactNumber', 'newEmail', 'assignedHospitalId'
        ]);
        $this->hospitalName = auth()->user()->name ?? '';
    }

    public function save()
    {
        $rules = [
            'hospitalName' => 'required',
            'sampleId' => 'required',
            'vialsCount' => 'required|integer|min:1',
            'declarationAccepted' => 'accepted',
        ];

        if (auth()->user()->isAdmin()) {
            $rules['assignedHospitalId'] = 'required';
        }

        // Aadhaar file is required only on creation
        if (!$this->editingOrderId) {
            $rules['aadhaarFile'] = 'required|image|max:2048';
        } else {
            $rules['aadhaarFile'] = 'nullable|image|max:2048';
        }

        if ($this->createNewCouple) {
            $rules = array_merge($rules, [
                'newPartner1Name' => 'required|string|max:255',
                'newPartner2Name' => 'nullable|string|max:255',
                'newContactNumber' => 'nullable|string|max:20',
                'newEmail' => 'nullable|email|max:255',
            ]);
        } else {
            $rules['coupleId'] = 'required';
        }

        $this->validate($rules);

        // Handle Couple (Create New or Use Existing)
        if ($this->createNewCouple) {
            // Generate Registration Number
             $latest = \App\Models\Couple::latest('id')->first();
             $nextId = $latest ? $latest->id + 1 : 1;
             $regNum = 'C-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

             $couple = \App\Models\Couple::create([
                 'registration_number' => $regNum,
                 'partner_1_name' => $this->newPartner1Name,
                 'partner_2_name' => $this->newPartner2Name,
                 'contact_number' => $this->newContactNumber,
                 'email' => $this->newEmail,
                 'user_id' => auth()->id(),
                 'status' => 'active',
             ]);
             
             $this->coupleId = $couple->id;
        }

        $userId = auth()->id();
        if (auth()->user()->isAdmin() && $this->assignedHospitalId) {
            $userId = $this->assignedHospitalId;
            // Optionally update hospital name if not set or if we want to sync it
            // $hospitalUser = \App\Models\User::find($userId);
            // $this->hospitalName = $hospitalUser->name; 
        }

        $data = [
            'hospital_name' => $this->hospitalName,
            'couple_id' => $this->coupleId,
            'sample_id' => $this->sampleId,
            'user_id' => $userId,
            'vials_count' => $this->vialsCount,
            'notes' => $this->notes,
            'status' => 'pending',
            'declaration_accepted' => $this->declarationAccepted,
        ];

        if ($this->aadhaarFile) {
            $data['aadhaar_file_path'] = $this->aadhaarFile->store('aadhaar_proofs', 'public');
        }

        if ($this->editingOrderId) {
            $order = \App\Models\HospitalOrder::find($this->editingOrderId);
            $order->update($data);
            $message = 'Order updated successfully.';
        } else {
            \App\Models\HospitalOrder::create($data);
            $message = 'Order created successfully.';
        }

        $this->resetForm();
        $this->modal('new-order')->close();
        $this->dispatch('toast', message: $message);
    }

    public function approveOrder($orderId)
    {
        $order = \App\Models\HospitalOrder::findOrFail($orderId);
        
        if ($order->status !== 'pending') {
            return;
        }

        $sample = \App\Models\Sample::find($order->sample_id);
        
        if (!$sample) {
            $this->dispatch('toast', message: 'Sample not found.', type: 'error');
            return;
        }

        if ($sample->vials_count < $order->vials_count) {
            $this->dispatch('toast', message: 'Insufficient vials in inventory.', type: 'error');
            return;
        }

        // Decrement vials
        $sample->decrement('vials_count', $order->vials_count);
        
        // If vials reach 0, mark as sold
        if ($sample->vials_count === 0) {
            $sample->update(['status' => 'sold']);
        }

        $order->update(['status' => 'dispatched']);
        $this->dispatch('toast', message: 'Order dispatched successfully.');
    }

    public $selected = [];

    public function delete($id)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        $order = \App\Models\HospitalOrder::findOrFail($id);
        $order->delete();

        $this->dispatch('toast', message: 'Order deleted successfully.');
    }

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        if (empty($this->selected)) {
            return;
        }

        \App\Models\HospitalOrder::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->dispatch('toast', message: 'Selected orders deleted successfully.');
    }

    public function render()
    {
        $user = auth()->user();
        $query = \App\Models\HospitalOrder::query();

        if ($user->isHospital()) {
            $query->where('user_id', $user->id);
        }
        
        if ($this->tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->tab === 'completed') {
            $query->whereIn('status', ['dispatched', 'delivered', 'completed']); // Assuming these are 'completed' states
        } // 'history' or 'all' shows all records

        return view('livewire.hospital-orders.index', [
            'orders' => $query->latest()->get(),
        ]);
    }
}
