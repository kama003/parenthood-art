<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">Hospital Orders</flux:heading>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin() && count($selected) > 0)
            <flux:button variant="danger" icon="trash" wire:click="deleteSelected" wire:confirm="Are you sure you want to delete selected orders?">Delete Selected</flux:button>
            @endif
            <flux:modal.trigger name="new-order">
                <flux:button variant="primary" icon="plus" wire:click="create">New Order</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="mb-4 border-b border-zinc-200 dark:border-zinc-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            @foreach(['all' => 'Order History', 'pending' => 'Pending Orders', 'completed' => 'Completed Orders'] as $key => $label)
                <button 
                    wire:click="$set('tab', '{{ $key }}')"
                    class="{{ $tab === $key ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }} 
                           whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                         <th scope="col" class="px-6 py-3 text-left">
                            @if(auth()->user()->isAdmin())
                            <!-- Basic checkbox implementation for simplicity -->
                            <input type="checkbox" class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 shadow-sm focus:ring-indigo-500" disabled title="Select individual rows">
                            @endif
                         </th>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Order ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Hospital</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Patient</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Sample</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Vials</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($orders as $order)
                        <tr wire:key="order-{{ $order->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(auth()->user()->isAdmin())
                                <input type="checkbox" wire:model.live="selected" value="{{ $order->id }}" class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">#{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-900 dark:text-zinc-200">{{ $order->user->name ?? $order->hospital_name }}</span>
                                    @if($order->user)
                                    <span class="text-xs text-zinc-500">{{ $order->user->email }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-900 dark:text-zinc-200">{{ $order->couple->partner_1_name ?? 'N/A' }}</span>
                                    <span class="text-xs text-zinc-500">{{ $order->couple->registration_number ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $order->sample->sample_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $order->vials_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:badge size="sm" :color="$order->status === 'dispatched' ? 'green' : 'zinc'">{{ ucfirst($order->status) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 flex gap-2">
                                <button wire:click="view({{ $order->id }})" class="px-3 py-1 border border-zinc-200 dark:border-zinc-700 rounded-md text-xs font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">View</button>
                                
                                @if(auth()->user()->isAdmin())
                                    @if($order->status === 'pending')
                                        <button wire:click="approveOrder({{ $order->id }})" class="px-3 py-1 border border-zinc-200 dark:border-zinc-700 rounded-md text-xs font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">Dispatch</button>
                                    @endif
                                    <button wire:click="edit({{ $order->id }})" class="px-3 py-1 border border-zinc-200 dark:border-zinc-700 rounded-md text-xs font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">Edit</button>
                                    <button wire:click="delete({{ $order->id }})" wire:confirm="Are you sure you want to delete this order?" class="px-3 py-1 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-md text-xs font-medium hover:bg-red-50 dark:hover:bg-red-900/10">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500 dark:text-zinc-400">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal name="new-order" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $viewOnly ? 'View Hospital Order' : ($editingOrderId ? 'Edit Hospital Order' : 'New Hospital Order') }}</flux:heading>
                <flux:subheading>{{ $editingOrderId ? 'Update dispatch order details.' : 'Create a dispatch order for a clinic.' }}</flux:subheading>
            </div>

            @if(!auth()->user()->isHospital())
            <flux:select label="Assign to Hospital Account" wire:model="assignedHospitalId" placeholder="Select Hospital User" :disabled="$viewOnly">
                @foreach(\App\Models\User::where('role', 'hospital')->orderBy('name')->get() as $hospitalUser)
                <flux:select.option value="{{ $hospitalUser->id }}">{{ $hospitalUser->name }} ({{ $hospitalUser->email }})</flux:select.option>
                @endforeach
            </flux:select>
            @endif

            <flux:input label="Hospital Name" wire:model="hospitalName" placeholder="e.g. City IVF Center" :disabled="$viewOnly" />
            
            @if(!$createNewCouple)
            <flux:select label="Select Existing Couple" wire:model="coupleId" placeholder="Select Couple" :disabled="$viewOnly">
                @php
                    $coupleQuery = \App\Models\Couple::query();
                    // If regular user (hospital/doctor), only show own couples
                    if (!auth()->user()->isAdmin()) {
                        $coupleQuery->where('user_id', auth()->id());
                    }
                    $couples = $coupleQuery->get();
                @endphp
                @foreach($couples as $couple)
                <flux:select.option value="{{ $couple->id }}">{{ $couple->registration_number }} ({{ $couple->partner_1_name }})</flux:select.option>
                @endforeach
            </flux:select>
            @endif

            <div class="flex items-center gap-2 mb-2">
                <flux:switch wire:model.live="createNewCouple" label="Or Create New Patient/Couple" :disabled="$viewOnly" />
            </div>

            @if($createNewCouple)
            <div class="space-y-4 border p-4 rounded-lg border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                <flux:input label="Partner 1 Name (Patient)" wire:model="newPartner1Name" />
                <flux:input label="Partner 2 Name (Spouse/Partner - Optional)" wire:model="newPartner2Name" />
                <div class="grid grid-cols-2 gap-4">
                     <flux:input label="Contact Number (Optional)" wire:model="newContactNumber" />
                     <flux:input label="Email (Optional)" wire:model="newEmail" />
                </div>
            </div>
            @endif
            
            <flux:select label="Sample" wire:model="sampleId" placeholder="Select Available Sample" :disabled="$viewOnly">
                @foreach(\App\Models\Sample::where('status', 'available')->get() as $sample)
                <flux:select.option value="{{ $sample->id }}">{{ $sample->sample_id }} ({{ $sample->blood_group }}) - {{ $sample->vials_count }} vials</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input label="Vials Count" type="number" wire:model="vialsCount" :disabled="$viewOnly" />
            
            <flux:input label="Notes" wire:model="notes" :disabled="$viewOnly" />

            <flux:input type="file" label="Aadhaar Card Proof" wire:model="aadhaarFile" :disabled="$viewOnly" />

            <flux:checkbox wire:model="declarationAccepted" label="I declare these samples are for the specified patients." :disabled="$viewOnly" />

            <div class="flex justify-end space-x-2">
                 <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                @if(!$viewOnly)
                <flux:button type="submit" variant="primary">{{ $editingOrderId ? 'Update Order' : 'Create Order' }}</flux:button>
                @endif
            </div>
        </form>
    </flux:modal>
</div>
