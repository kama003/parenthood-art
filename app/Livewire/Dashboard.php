<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Donor;
use App\Models\Couple;
use App\Models\Sample;
use App\Models\HospitalOrder;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'activeDonors' => Donor::where('status', 'active')->count(),
            'registeredCouples' => Couple::count(),
            'availableSamples' => Sample::where('status', 'available')->count(),
            'pendingOrders' => HospitalOrder::where('status', 'pending')->count(),
            'recentSamples' => Sample::with('donor')->latest()->take(5)->get(),
        ]);
    }
}
