<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">Donors</flux:heading>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin())
                @if(count($selected) > 0)
                <flux:button variant="danger" icon="trash" wire:click="deleteSelected" wire:confirm="Are you sure you want to delete selected donors?">Delete Selected</flux:button>
                @endif
            <flux:button icon="arrow-down-tray" wire:click="export">Export</flux:button>
            <flux:modal.trigger name="import-modal">
                <flux:button icon="arrow-up-tray">Import</flux:button>
            </flux:modal.trigger>
            @endif
            <flux:modal.trigger name="add-donor">
                <flux:button variant="primary" icon="plus" wire:click="create">Add New Donor</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="mb-6 max-w-lg">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by Number, Blood Group, or Aadhaar..." />
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Group</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Age</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Body</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Complexion</th>
                        @if(auth()->user()->isAdmin())
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($donors as $donor)
                        <tr wire:key="donor-{{ $donor->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(auth()->user()->isAdmin())
                                <input type="checkbox" wire:model.live="selected" value="{{ $donor->id }}" class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $donor->donor_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:badge size="sm">{{ $donor->blood_group }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $donor->age }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:badge size="sm" :color="$donor->status === 'active' ? 'green' : 'zinc'">{{ ucfirst($donor->status) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $donor->body_structure }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $donor->complexion }}</td>
                            @if(auth()->user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 flex gap-2">
                                <button wire:click="edit({{ $donor->id }})" class="px-3 py-1 border border-zinc-200 dark:border-zinc-700 rounded-md text-xs font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">Edit</button>
                                <button wire:click="delete({{ $donor->id }})" wire:confirm="Are you sure you want to delete this donor?" class="px-3 py-1 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-md text-xs font-medium hover:bg-red-50 dark:hover:bg-red-900/10">Delete</button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500 dark:text-zinc-400">
                                No donors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal name="add-donor" class="w-full md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingDonorId ? 'Edit Donor' : 'Add New Donor' }}</flux:heading>
                <flux:subheading>{{ $editingDonorId ? 'Update donor details.' : 'Enter the details of the donor.' }}</flux:subheading>
            </div>

            <flux:input label="Donor Number" wire:model="donorNumber" readonly />
            
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Age" type="number" wire:model="age" />
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
            </div>

            <flux:input label="Aadhaar Number" wire:model="aadhaar" placeholder="12-digit number" />

            <flux:radio.group label="Status" wire:model="status" variant="segmented">
                <flux:radio value="active" label="Active" />
                <flux:radio value="inactive" label="Inactive" />
            </flux:radio.group>

            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Eye Color" wire:model="eyeColor" />
                <flux:input label="Hair Color" wire:model="hairColor" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:select label="Body Structure" wire:model="bodyStructure" placeholder="Select">
                    <flux:select.option value="Slim">Slim</flux:select.option>
                    <flux:select.option value="Moderate">Moderate</flux:select.option>
                    <flux:select.option value="Obese">Obese</flux:select.option>
                </flux:select>
                
                <flux:select label="Complexion" wire:model="complexion" placeholder="Select">
                    <flux:select.option value="Fair">Fair</flux:select.option>
                    <flux:select.option value="Medium">Medium</flux:select.option>
                    <flux:select.option value="Dark">Dark</flux:select.option>
                </flux:select>
            </div>

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingDonorId ? 'Update Donor' : 'Save Donor' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="import-modal" class="w-full md:w-96">
        <form wire:submit="import" class="space-y-6">
            <div>
                <flux:heading size="lg">Import Donors</flux:heading>
                <flux:subheading>Upload CSV file to import donors.</flux:subheading>
            </div>

            <flux:input type="file" label="CSV File" wire:model="importFile" accept=".csv,.txt" />

            <div class="flex justify-end space-x-2">
                 <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Import</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
