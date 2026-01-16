<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">Samples Inventory</flux:heading>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin() && count($selected) > 0)
            <flux:button variant="danger" icon="trash" wire:click="deleteSelected" wire:confirm="Are you sure you want to delete selected samples?">Delete Selected</flux:button>
            @endif
            <flux:modal.trigger name="add-sample">
                <flux:button variant="primary" icon="plus" wire:click="create">Add New Sample</flux:button>
            </flux:modal.trigger>
        </div>
    </div>



    <div class="mb-4 border-b border-zinc-200 dark:border-zinc-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            @foreach(['all' => 'All', 'available' => 'Available', 'sold' => 'Sold', 'returned' => 'Returned', 'expired' => 'Expired'] as $key => $label)
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
                            <input type="checkbox" class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 shadow-sm focus:ring-indigo-500" disabled title="Select individual rows">
                            @endif
                         </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Sample ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Donor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Blood Group</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Vials</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Freeze Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Expiry</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Hospital</th>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($samples as $sample)
                        <tr wire:key="sample-{{ $sample->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(auth()->user()->isAdmin())
                                <input type="checkbox" wire:model.live="selected" value="{{ $sample->id }}" class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $sample->sample_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $sample->donor->donor_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400"><flux:badge size="sm">{{ $sample->blood_group }}</flux:badge></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $sample->vials_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $sample->freeze_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $sample->expiry_date?->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:badge size="sm" :color="$sample->status === 'available' ? 'green' : 'zinc'">{{ ucfirst($sample->status) }}</flux:badge>
                            </td>
                            @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $sample->user->name ?? 'Bank' }}</td>
                            @endif
                            @if(auth()->user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 flex gap-2">
                                <button wire:click="edit({{ $sample->id }})" class="px-3 py-1 border border-zinc-200 dark:border-zinc-700 rounded-md text-xs font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">Edit</button>
                                <button wire:click="delete({{ $sample->id }})" wire:confirm="Are you sure you want to delete this sample?" class="px-3 py-1 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-md text-xs font-medium hover:bg-red-50 dark:hover:bg-red-900/10">Delete</button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (auth()->user()->isDoctor() || auth()->user()->isAdmin()) ? 8 : 8 }}" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500 dark:text-zinc-400">
                                No samples found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal name="add-sample" class="w-full md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingSampleId ? 'Edit Sample' : 'Add New Sample' }}</flux:heading>
                <flux:subheading>{{ $editingSampleId ? 'Update sample details.' : 'Record details of semen/egg sample.' }}</flux:subheading>
            </div>

            <flux:input label="Sample ID" wire:model="sampleId" placeholder="Auto-gen..." />
            
            <flux:input label="Donor ID" wire:model="donorId" placeholder="Search Donor..." />
            <flux:input label="Couple ID (Optional)" wire:model="coupleId" placeholder="Search Couple..." />
            
            <div class="grid grid-cols-2 gap-4">
                 <flux:select label="Blood Group" wire:model="bloodGroup" placeholder="Select">
                    <flux:select.option value="A+">A+</flux:select.option>
                    <flux:select.option value="A-">A-</flux:select.option>
                    <flux:select.option value="B+">B+</flux:select.option>
                    <flux:select.option value="B-">B-</flux:select.option>
                    <flux:select.option value="O+">O+</flux:select.option>
                    <flux:select.option value="O-">O-</flux:select.option>
                    <flux:select.option value="AB+">AB+</flux:select.option>
                    <flux:select.option value="AB-">AB-</flux:select.option>
                </flux:select>
                <flux:input label="Vials Count" type="number" wire:model="vialsCount" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Freeze Date" type="date" wire:model.live="freezeDate" />
                <flux:input label="Expiry Date" type="date" wire:model="expiryDate" readonly />
            </div>

            <flux:select label="Status" wire:model="status" placeholder="Select">
                <flux:select.option value="available">Available</flux:select.option>
                <flux:select.option value="sold">Sold</flux:select.option>
                <flux:select.option value="returned">Returned</flux:select.option>
                <flux:select.option value="expired">Expired</flux:select.option>
            </flux:select>

            <div class="flex justify-end space-x-2">
                 <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingSampleId ? 'Update Sample' : 'Save Sample' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
