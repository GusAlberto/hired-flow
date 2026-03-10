<div class="p-8">
    @php
        $columns = [
            'applied' => ['label' => 'Applied', 'items' => $applied],
            'waiting' => ['label' => 'Waiting', 'items' => $waiting],
            'interview' => ['label' => 'Interview', 'items' => $interview],
            'rejected' => ['label' => 'Rejected', 'items' => $rejected],
            'offer' => ['label' => 'Offer', 'items' => $offer],
        ];
    @endphp

    @if (session('status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2200)" x-show="show" class="fixed right-6 top-6 z-50 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-xl">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-8 flex items-center justify-between gap-4">
        <h1 class="text-3xl font-bold">
            Job Application Tracker
        </h1>

        <button type="button" wire:click="openCreateForm" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
            + New application
        </button>
    </div>

    <div class="grid grid-cols-3 gap-6 mb-8">

        <div class="bg-white shadow p-4 rounded">

            Total applications

            <div class="text-2xl font-bold">
                {{ $total }}
            </div>

        </div>

        <div class="bg-white shadow p-4 rounded">

            Interviews

            <div class="text-2xl font-bold">
                {{ $interviews }}
            </div>

        </div>

        <div class="bg-white shadow p-4 rounded">

            Offers

            <div class="text-2xl font-bold">
                {{ $offers }}
            </div>

        </div>

    </div>

    @if ($isFormOpen)
    <form wire:submit.prevent="saveApplication" class="bg-white shadow rounded p-4 mb-8 space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $editingApplicationId ? 'Edit application' : 'New application' }}
                </h2>
                <p class="text-sm text-gray-500">
                    Fill in the details shown in the board card.
                </p>
            </div>

            @if ($editingApplicationId)
                <button type="button" wire:click="cancelEditing" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Cancel
                </button>
            @else
                <button type="button" wire:click="cancelEditing" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Close
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <input type="text" placeholder="Company name" wire:model.live="company" class="w-full border rounded px-3 py-2" />
                @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="text" placeholder="Position" wire:model.live="position" class="w-full border rounded px-3 py-2" />
                @error('position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="text" placeholder="City" wire:model.live="city" class="w-full border rounded px-3 py-2" />
                @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="text" placeholder="Location" wire:model.live="location" class="w-full border rounded px-3 py-2" />
                @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="date" wire:model.live="applied_at" class="w-full border rounded px-3 py-2" />
                @error('applied_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="url" placeholder="Job URL (optional)" wire:model.live="job_url" class="w-full border rounded px-3 py-2" />
                @error('job_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        @if ($editingApplicationId)
            <div>
                <textarea placeholder="Notes" wire:model.live="notes" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-semibold">
            {{ $editingApplicationId ? 'Save changes' : 'Add application' }}
        </button>
    </form>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
        @foreach ($columns as $status => $column)
        <div class="rounded-2xl bg-gray-50 p-4 border border-gray-200">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="font-bold text-gray-900">{{ $column['label'] }}</h2>
                <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-white px-2.5 py-1 text-sm font-semibold text-gray-700 border border-gray-200">
                    {{ $column['items']->count() }}
                </span>
            </div>

            <div id="{{ $status }}" class="space-y-3 min-h-24">
                @foreach ($column['items'] as $app)
                <article class="card rounded-2xl border border-gray-200 bg-white p-4 shadow-sm" data-id="{{ $app->id }}" wire:key="application-{{ $app->id }}">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-blue-700">
                                {{ $app->position }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $app->applied_at?->format('d/m/Y') }}
                            </div>
                        </div>

                        <details class="card-actions relative">
                            <summary class="cursor-pointer list-none rounded-xl border border-gray-200 px-2.5 py-1 text-lg leading-none text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
                                ⋯
                            </summary>

                            <div class="absolute right-0 z-10 mt-2 w-36 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                                <button type="button" wire:click="editApplication({{ $app->id }})" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                    Edit
                                </button>
                                <button type="button" wire:click="deleteApplication({{ $app->id }})" wire:confirm="Delete this application?" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                    Delete
                                </button>
                            </div>
                        </details>
                    </div>

                    <div class="space-y-2 text-sm text-gray-700">
                        <div>
                            <span class="font-medium">🏢 Company name:</span>
                            {{ $app->company }}
                        </div>
                        <div>
                            <span class="font-medium">📍 City:</span>
                            {{ $app->city ?: 'Not informed' }}
                        </div>
                        <div>
                            <span class="font-medium">🗺️ Location:</span>
                            {{ $app->location ?: 'Not informed' }}
                        </div>
                        @if ($app->notes)
                        <div>
                            <span class="font-medium">📝 Notes:</span>
                            {{ $app->notes }}
                        </div>
                        @endif
                    </div>
                </article>
                @endforeach

                @if ($column['items']->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white/60 px-4 py-6 text-center text-sm text-gray-400">
                    No applications in this column yet.
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    function initSortables() {
        ['applied', 'waiting', 'interview', 'rejected', 'offer'].forEach(status => {
            const el = document.getElementById(status)
            if (!el) return

            // avoid creating multiple Sortable instances for the same element
            if (el._sortable) return

            el._sortable = new Sortable(el, {
                group: 'jobs',
                animation: 150,
                filter: 'details, summary, button',
                preventOnFilter: false,
                onEnd: function(evt) {
                    const id = evt.item.dataset.id
                    const newStatus = evt.to.id

                    // Livewire v3/v4: dispatch with named payload { id, status }
                    Livewire.dispatch('moveApplication', { id: id, status: newStatus })
                }
            })
        })
    }

    // initialize asap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSortables)
    } else {
        initSortables()
    }

    // re-initialize after Livewire navigations / loads
    document.addEventListener('livewire:load', initSortables)
    document.addEventListener('livewire:navigated', initSortables)
</script>