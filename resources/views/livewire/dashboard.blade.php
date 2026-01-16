<div class="p-6 space-y-8">
    <div class="flex justify-between items-center">
        <flux:heading size="xl">Dashboard Overview</flux:heading>
        <flux:text class="text-sm">{{ now()->format('l, d F Y') }}</flux:text>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <flux:subheading>Active Donors</flux:subheading>
            <flux:heading size="xl" class="mt-2">{{ $activeDonors }}</flux:heading>
        </div>
        <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <flux:subheading>Registered Couples</flux:subheading>
            <flux:heading size="xl" class="mt-2">{{ $registeredCouples }}</flux:heading>
        </div>
        <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <flux:subheading>Available Samples</flux:subheading>
            <flux:heading size="xl" class="mt-2 text-green-600 dark:text-green-400">{{ $availableSamples }}</flux:heading>
        </div>
        <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <flux:subheading>Pending Orders</flux:subheading>
            <flux:heading size="xl" class="mt-2 text-amber-600 dark:text-amber-400">{{ $pendingOrders }}</flux:heading>
        </div>
    </div>

    <!-- Recent Samples -->
    <div>
        <flux:heading size="lg" class="mb-4">Recent Samples added</flux:heading>
        <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Sample ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Donor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Blood Group</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Date Added</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($recentSamples as $sample)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $sample->sample_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $sample->donor->donor_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    <flux:badge size="sm">{{ $sample->blood_group }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $sample->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    <flux:badge size="sm" :color="$sample->status === 'available' ? 'green' : 'zinc'">{{ ucfirst($sample->status) }}</flux:badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500 dark:text-zinc-400">
                                    No samples found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
