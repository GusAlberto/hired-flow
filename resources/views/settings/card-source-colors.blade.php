<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Card Colors By Job Source
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="mb-5 text-sm text-gray-600">
                    Customize background colors for cards based on job URL source (LinkedIn, Indeed, and others).
                </p>

                <form method="POST" action="{{ route('settings.card-source-colors.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    @foreach ($sourceLabels as $key => $label)
                        <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 p-4 sm:grid-cols-[1fr_auto_auto] sm:items-center">
                            <div>
                                <div class="font-medium text-gray-900">{{ $label }}</div>
                                <div class="text-xs text-gray-500">Default: {{ strtoupper($defaultColors[$key]) }}</div>
                            </div>

                            <input
                                type="color"
                                name="colors[{{ $key }}]"
                                value="{{ old("colors.$key", $sourceColors[$key]) }}"
                                class="h-10 w-16 cursor-pointer rounded border border-gray-300 bg-white"
                            />

                            <input
                                type="text"
                                name="colors[{{ $key }}]"
                                value="{{ old("colors.$key", $sourceColors[$key]) }}"
                                pattern="^#[0-9A-Fa-f]{6}$"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-sm sm:w-36"
                            />
                        </div>

                        @error("colors.$key")
                            <p class="-mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @endforeach

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Save colors
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
