<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">Couples Registry</flux:heading>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin() && count($selected) > 0)
            <flux:button variant="danger" icon="trash" wire:click="deleteSelected" wire:confirm="Are you sure you want to delete selected couples?">Delete Selected</flux:button>
            @endif
            <flux:button variant="primary" icon="plus" wire:click="create">Register Couple</flux:button>
        </div>
    </div>

    <div class="mb-6 max-w-lg">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by Reg. No, Name, Contact, or Email..." />
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Reg. No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Partner 1</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Partner 2</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        @if(auth()->user()->isAdmin())
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($couples as $couple)
                        <tr wire:key="couple-{{ $couple->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(auth()->user()->isAdmin())
                                <input type="checkbox" wire:model.live="selected" value="{{ $couple->id }}" class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">#{{ $couple->registration_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $couple->partner_1_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $couple->partner_2_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <div>{{ $couple->contact_number }}</div>
                                <div class="text-xs">{{ $couple->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:badge size="sm" :color="$couple->status === 'active' ? 'green' : 'zinc'">{{ ucfirst($couple->status) }}</flux:badge>
                            </td>
                            @if(auth()->user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 flex gap-2">
                                <button wire:click="edit({{ $couple->id }})" class="px-3 py-1 border border-zinc-200 dark:border-zinc-700 rounded-md text-xs font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">Edit</button>
                                <button wire:click="delete({{ $couple->id }})" wire:confirm="Are you sure you want to delete this couple?" class="px-3 py-1 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-md text-xs font-medium hover:bg-red-50 dark:hover:bg-red-900/10">Delete</button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500 dark:text-zinc-400">
                                No couples registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal name="couple-modal" class="w-full">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingCoupleId ? 'Edit Couple' : 'Register New Couple' }}</flux:heading>
                <flux:subheading>{{ $editingCoupleId ? 'Update couple details.' : 'Enter details of the couple.' }}</flux:subheading>
            </div>

            <flux:input label="Registration Number" wire:model="registrationNumber" readonly />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <flux:heading size="sm" class="mb-2">Partner 1 (Patient)</flux:heading>
                    <flux:input label="Name" wire:model="partner1Name" placeholder="Full Name" />
                    <flux:input type="file" label="Aadhaar Card" wire:model="partner1Aadhaar" />
                </div>
                
                <div class="space-y-4">
                    <flux:heading size="sm" class="mb-2">Partner 2 (Spouse/Optional)</flux:heading>
                    <flux:input label="Name" wire:model="partner2Name" placeholder="Full Name (Optional)" />
                    <flux:input type="file" label="Aadhaar Card" wire:model="partner2Aadhaar" />
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Contact Number" wire:model="contactNumber" />
                <flux:input label="Email" type="email" wire:model="email" />
            </div>

            <flux:select label="Status" wire:model="status" placeholder="Select">
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
                <flux:select.option value="discharged">Discharged</flux:select.option>
            </flux:select>

            <div class="flex justify-end space-x-2">
                 <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingCoupleId ? 'Update Couple' : 'Register Couple' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
