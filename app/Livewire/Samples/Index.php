<?php

namespace App\Livewire\Samples;

use Livewire\Component;

class Index extends Component
{
    public $sampleId = '';
    public $donorId = '';
    public $coupleId = '';
    public $bloodGroup = '';
    public $vialsCount = 1;
    public $freezeDate = '';
    public $status = 'available';
    public $assigningHospitalId = '';

    public $editingSampleId = null;

    public function create()
    {
        $this->resetForm();
        $this->modal('add-sample')->show();
    }

    public function edit($id)
    {
        $sample = \App\Models\Sample::findOrFail($id);
        
        $this->editingSampleId = $sample->id;
        $this->sampleId = $sample->sample_id;
        $this->donorId = $sample->donor_id;
        $this->coupleId = $sample->couple_id;
        $this->bloodGroup = $sample->blood_group;
        $this->vialsCount = $sample->vials_count;
        $this->freezeDate = $sample->freeze_date->format('Y-m-d');
        $this->status = $sample->status;

        $this->modal('add-sample')->show();
    }

    public function resetForm()
    {
        $this->editingSampleId = null;
        $this->editingSampleId = null;
        $this->reset(['sampleId', 'donorId', 'coupleId', 'bloodGroup', 'vialsCount', 'freezeDate', 'status']);
        $this->status = 'available'; // Default
    }

    public function save()
    {
        $rules = [
            'donorId' => 'required|exists:donors,id',
            'bloodGroup' => 'required',
            'freezeDate' => 'required|date',
        ];

        if (!$this->editingSampleId) {
             $this->sampleId = 'SMP-' . strtoupper(\Illuminate\Support\Str::random(8));
        }

        $this->validate($rules);

        $data = [
            'sample_id' => $this->sampleId,
            'donor_id' => $this->donorId,
            'couple_id' => $this->coupleId ?: null,
            'user_id' => auth()->id(),
            'blood_group' => $this->bloodGroup,
            'vials_count' => $this->vialsCount,
            'freeze_date' => $this->freezeDate,
            'status' => $this->status,
        ];
        
        // Quick fix for donor_id if it's numeric (ID) vs string (Number) - UI should be clearer, but assuming input is ID for now or using dropdown
        // Actually, let's fix the UI to assume it's an ID from a dropdown later. For now, trust the input.

        if ($this->editingSampleId) {
            $sample = \App\Models\Sample::find($this->editingSampleId);
            $sample->update($data);
            $message = 'Sample updated successfully.';
        } else {
            \App\Models\Sample::create($data);
            $message = 'Sample added successfully.';
        }

        $this->resetForm();
        $this->modal('add-sample')->close();
        $this->dispatch('toast', message: $message);
    }

    public function delete($id)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        $sample = \App\Models\Sample::findOrFail($id);
        $sample->delete();

        $this->dispatch('toast', message: 'Sample deleted successfully.', type: 'success');
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

        \App\Models\Sample::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->dispatch('toast', message: 'Selected samples deleted successfully.', type: 'success');
    }

    public function assignSelected()
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        $this->validate([
            'assigningHospitalId' => 'required|exists:users,id',
        ]);

        if (empty($this->selected)) {
             $this->dispatch('toast', message: 'No samples selected.', type: 'error');
             return;
        }

        \App\Models\Sample::whereIn('id', $this->selected)->update([
            'user_id' => $this->assigningHospitalId
        ]);

        $this->selected = [];
        $this->assigningHospitalId = '';
        $this->modal('assign-samples')->close();
        $this->dispatch('toast', message: 'Samples assigned to hospital successfully.', type: 'success');
    }

    public function getHospitalsProperty()
    {
        return \App\Models\User::where('role', 'hospital')->orderBy('name')->get();
    }

    public function getDonorsProperty()
    {
        return \App\Models\Donor::latest()->get();
    }

    public function getCouplesProperty()
    {
        return \App\Models\Couple::latest()->get();
    }

    public $tab = 'all';

    public function render()
    {
        $user = auth()->user();
        $query = \App\Models\Sample::query();

        if ($user->isHospital()) {
            $query->where('user_id', $user->id);
        }

        if ($this->tab === 'inventory') {
            if ($user->isAdmin()) {
                $query->where('user_id', '<>', $user->id);
            }
        } elseif ($this->tab !== 'all') {
            $query->where('status', $this->tab);
        }

        return view('livewire.samples.index', [
            'samples' => $query->latest()->get(),
        ]);
    }
}
