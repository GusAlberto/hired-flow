@if ($isSearching)
    <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-blue-900">
                Search Results for &quot;{{ $searchQuery }}&quot;
            </h2>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ $searchResults->count() }} result{{ $searchResults->count() !== 1 ? 's' : '' }}
            </span>
        </div>

        @if ($searchResults->isEmpty())
            <div class="rounded-xl border border-dashed border-blue-300 bg-white px-4 py-6 text-center text-sm text-blue-600">
                No applications found matching "{{ $searchQuery }}"
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($searchResults as $app)
                    <article class="rounded-xl border border-blue-200 bg-white p-4 shadow-sm hover:shadow-md transition" wire:key="search-result-{{ $app->id }}">
                        <div class="mb-2 flex items-start justify-between">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-blue-700 uppercase">{{ $app->position }}</div>
                                <div class="text-sm text-gray-700">{{ $app->company }}</div>
                            </div>
                            @if ($app->deleted_at)
                                <span class="inline-flex rounded px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700">Archived</span>
                            @endif
                        </div>
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            @if ($app->city)
                                <div>📍 {{ $app->city }}</div>
                            @endif
                            @if ($app->location)
                                <div>🗺️ {{ $app->location }}</div>
                            @endif
                            @if ($app->applied_at)
                                <div>📅 Applied: {{ $app->applied_at->format('d/m/Y') }}</div>
                            @endif
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="button" wire:click="editApplication({{ $app->id }})" class="flex-1 rounded-lg bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-200 transition">
                                Edit
                            </button>
                            <button type="button" wire:click="deleteApplication({{ $app->id }})" wire:confirm="Delete this application?" class="flex-1 rounded-lg bg-red-100 px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-200 transition">
                                Delete
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endif
