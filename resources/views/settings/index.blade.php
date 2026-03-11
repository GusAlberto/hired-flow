<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Settings
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-5 flex flex-wrap gap-2">
                <a href="{{ route('settings.index', ['tab' => 'archiving']) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $tab === 'archiving' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                    Archiving
                </a>
            </div>

            @if ($tab === 'archiving')
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Archiving Rules</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Define how many days applications stay in the Kanban before being moved to <strong>Archived</strong>.
                    </p>

                    <form method="POST" action="{{ route('settings.archiving.update') }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="archive_after_days" class="mb-1 block text-sm font-medium text-gray-700">
                                Days before auto-archive (1 to 365)
                            </label>
                            <input
                                id="archive_after_days"
                                name="archive_after_days"
                                type="number"
                                min="1"
                                max="365"
                                value="{{ old('archive_after_days', $archiveAfterDays) }}"
                                class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2"
                            />
                            @error('archive_after_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
