<div class="p-8">

    <h1 class="text-3xl font-bold mb-8">
        Job Application Tracker
    </h1>

    <div class="grid grid-cols-3 gap-6 mb-8">

        <div class="bg-white shadow p-4 rounded">

            Total Applications

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

    <form wire:submit.prevent="addApplication" class="bg-white shadow rounded p-4 mb-8 flex gap-4">

        <input type="text" placeholder="Company" wire:model="company" class="border rounded px-3 py-2 w-1/4" />

        <input type="text" placeholder="Position" wire:model="position" class="border rounded px-3 py-2 w-1/4" />

        <input type="date" wire:model="applied_at" class="border rounded px-3 py-2" />

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-semibold">
            Add Application
        </button>

    </form>

    <div class="grid grid-cols-5 gap-6">

        <div>
            <h2 class="font-bold mb-3">Applied</h2>
            <div id="applied">
                @foreach($applied as $app)

                <div class="card" data-id="{{ $app->id }}">

                    <strong>{{ $app->company }}</strong>

                    <div class="text-sm text-gray-500">
                        {{ $app->position }}
                    </div>

                </div>

                @endforeach
            </div>
        </div>

        <div>
            <h2 class="font-bold mb-3">Waiting</h2>
            <div id="waiting">
                @foreach($waiting as $app)
                <div class="card" data-id="{{ $app->id }}">
                    <strong>{{ $app->company }}</strong>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <h2 class="font-bold mb-3">Interview</h2>
            <div id="interview">
                @foreach($interview as $app)
                <div class="card" data-id="{{ $app->id }}">
                    <strong>{{ $app->company }}</strong>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <h2 class="font-bold mb-3">Rejected</h2>
            <div id="rejected">
                @foreach($rejected as $app)
                <div class="card" data-id="{{ $app->id }}">
                    <strong>{{ $app->company }}</strong>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <h2 class="font-bold mb-3">Offer</h2>
            <div id="offer">
                @foreach($offer as $app)
                <div class="card" data-id="{{ $app->id }}">
                    <strong>{{ $app->company }}</strong>
                </div>
                @endforeach
            </div>
        </div>

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