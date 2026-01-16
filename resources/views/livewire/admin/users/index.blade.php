<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">User Management</flux:heading>
        <flux:modal.trigger name="user-modal">
            <flux:button variant="primary" icon="plus" wire:click="create">New User</flux:button>
        </flux:modal.trigger>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Joined</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:badge size="sm" :color="$user->role === 'admin' ? 'purple' : ($user->role === 'doctor' ? 'blue' : 'emerald')">{{ ucfirst($user->role) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 flex gap-2">
                                <button wire:click="edit({{ $user->id }})" class="px-3 py-1 border border-zinc-200 dark:border-zinc-700 rounded-md text-xs font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">Edit</button>
                                @if($user->id !== auth()->id())
                                <button wire:click="delete({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" class="px-3 py-1 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-md text-xs font-medium hover:bg-red-50 dark:hover:bg-red-900/10">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500 dark:text-zinc-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $users->links() }}
        </div>
    </div>

    <flux:modal name="user-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingUserId ? 'Edit User' : 'New User' }}</flux:heading>
                <flux:subheading>{{ $editingUserId ? 'Update user details.' : 'Create a new user account.' }}</flux:subheading>
            </div>

            <flux:input label="Name" wire:model="name" placeholder="Full Name" />
            <flux:input label="Email" type="email" wire:model="email" placeholder="email@example.com" />
            
            <flux:select label="Role" wire:model="role">
                <flux:select.option value="hospital">Hospital</flux:select.option>
                <flux:select.option value="doctor">Doctor</flux:select.option>
                <flux:select.option value="admin">Admin</flux:select.option>
            </flux:select>

            <div class="space-y-2">
                <flux:input label="Password" type="text" wire:model="password" placeholder="{{ $editingUserId ? 'Leave blank to keep current' : 'Enter password' }}" />
                <div class="flex items-center justify-between">
                    <button type="button" wire:click="generatePassword" class="text-xs text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Generate Random Password</button>
                    @if($generatedPassword)
                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">Generated: <span class="font-mono select-all">{{ $generatedPassword }}</span></span>
                    @endif
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                 <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingUserId ? 'Update User' : 'Create User' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
