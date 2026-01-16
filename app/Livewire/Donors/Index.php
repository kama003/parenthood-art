<?php

namespace App\Livewire\Donors;

use Livewire\Component;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Response;

class Index extends Component
{
    use WithFileUploads;

    public $importFile;
    public $showImportModal = false;
    public $donorNumber = '';
    public $age = '';
    public $aadhaar = '';
    public $bloodGroup = '';
    public $status = 'active';
    public $eyeColor = '';
    public $hairColor = '';
    public $bodyStructure = '';
    public $complexion = '';

    public function mount()
    {
        $this->generateDonorNumber();
    }

    public $editingDonorId = null;

    public function generateDonorNumber()
    {
        if ($this->editingDonorId) return; // Don't regenerate if editing

        $latest = \App\Models\Donor::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $this->donorNumber = 'D-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function edit($id)
    {
        $donor = \App\Models\Donor::findOrFail($id);
        
        $this->editingDonorId = $donor->id;
        $this->donorNumber = $donor->donor_number;
        $this->age = $donor->age;
        $this->aadhaar = $donor->aadhaar_number;
        $this->bloodGroup = $donor->blood_group;
        $this->status = $donor->status;
        $this->eyeColor = $donor->eye_color;
        $this->hairColor = $donor->hair_color;
        $this->bodyStructure = $donor->body_structure;
        $this->complexion = $donor->complexion;

        $this->modal('add-donor')->show();
    }

    public function create()
    {
        $this->resetForm();
        $this->modal('add-donor')->show();
    }

    public function resetForm()
    {
        $this->editingDonorId = null;
        $this->reset(['age', 'aadhaar', 'bloodGroup', 'status', 'eyeColor', 'hairColor', 'bodyStructure', 'complexion']);
        $this->generateDonorNumber();
    }

    public function save()
    {
        $rules = [
            'age' => 'required|integer|min:18|max:50',
            'bloodGroup' => 'required',
            'aadhaar' => 'nullable|digits:12',
        ];

        if (!$this->editingDonorId) {
            $rules['donorNumber'] = 'required|unique:donors,donor_number';
        }

        $this->validate($rules);

        $data = [
            'age' => $this->age,
            'aadhaar_number' => $this->aadhaar,
            'blood_group' => $this->bloodGroup,
            'status' => $this->status,
            'eye_color' => $this->eyeColor,
            'hair_color' => $this->hairColor,
            'body_structure' => $this->bodyStructure,
            'complexion' => $this->complexion,
        ];

        if ($this->editingDonorId) {
            $donor = \App\Models\Donor::find($this->editingDonorId);
            $donor->update($data);
            $message = 'Donor updated successfully.';
        } else {
            $data['donor_number'] = $this->donorNumber;
            \App\Models\Donor::create($data);
            $message = 'Donor added successfully.';
        }

        $this->resetForm();
        $this->modal('add-donor')->close();
        $this->dispatch('toast', message: $message);
    }

    public function export()
    {
        $donors = \App\Models\Donor::all();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="donors.csv"',
        ];

        $callback = function() use ($donors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Donor Number', 'Age', 'Blood Group', 'Status', 'Eye Color', 'Hair Color', 'Body Structure', 'Complexion', 'Aadhaar Number']);

            foreach ($donors as $donor) {
                fputcsv($file, [
                    $donor->donor_number,
                    $donor->age,
                    $donor->blood_group,
                    $donor->status,
                    $donor->eye_color,
                    $donor->hair_color,
                    $donor->body_structure,
                    $donor->complexion,
                    $donor->aadhaar_number,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->importFile->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file); // Skip header

        while (($row = fgetcsv($file)) !== false) {
            // Assuming format: Donor Number, Age, Blood Group...
            // If Donor Number is present, check uniqueness. If not or auto-gen needed, logic varies.
            // For bulk import, we'll assume they provide data. If Donor Number exists, we skip or update?
            // "Upload bulk details" usually implies new data.
            // Let's assume standard order matching export.
            
            // Map keys
            $data = [
                'donor_number' => $row[0] ?? null,
                'age' => $row[1] ?? null,
                'blood_group' => $row[2] ?? null,
                'status' => $row[3] ?? 'active',
                'eye_color' => $row[4] ?? null,
                'hair_color' => $row[5] ?? null,
                'body_structure' => $row[6] ?? null,
                'complexion' => $row[7] ?? null,
                'aadhaar_number' => $row[8] ?? null,
            ];

            if ($data['donor_number']) {
                \App\Models\Donor::updateOrCreate(
                    ['donor_number' => $data['donor_number']],
                    $data
                );
            }
        }

        fclose($file);
        
        $this->reset(['importFile', 'showImportModal']);
        $this->modal('import-modal')->close();
        $this->dispatch('toast', message: 'Donors imported successfully.');
    }

    public function delete($id)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        $donor = \App\Models\Donor::findOrFail($id);
        $donor->delete();

        $this->dispatch('toast', message: 'Donor deleted successfully.', type: 'success');
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

        \App\Models\Donor::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->dispatch('toast', message: 'Selected donors deleted successfully.', type: 'success');
    }

    public $search = '';

    public function render()
    {
        if (auth()->user()->isHospital()) {
            abort(403, 'Unauthorized access.');
        }

        $query = \App\Models\Donor::query();

        if ($this->search) {
             $query->where(function($q) {
                 $q->where('donor_number', 'like', '%' . $this->search . '%')
                   ->orWhere('blood_group', 'like', '%' . $this->search . '%')
                   ->orWhere('aadhaar_number', 'like', '%' . $this->search . '%');
             });
        }

        return view('livewire.donors.index', [
            'donors' => $query->latest()->get(),
        ]);
    }
}
