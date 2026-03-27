@extends('layouts.job-application')

@section('title', 'Create Application')
@section('activeMenu', 'applications.create')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Create application</h1>
            <p class="mt-1 text-sm text-slate-600">Add a new job opportunity and keep your pipeline organized.</p>
        </div>

        <form method="POST" action="{{ route('applications.store') }}"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf

            <div class="grid grid-cols-1 gap-6 p-5 lg:grid-cols-3">
                <section class="space-y-4 lg:col-span-2">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Role Information</h2>
                        <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="company" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Company</label>
                                <input id="company" name="company" type="text" value="{{ old('company') }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('company')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="position" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Position</label>
                                <input id="position" name="position" type="text" value="{{ old('position') }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="city" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">City</label>
                                <input id="city" name="city" type="text" value="{{ old('city') }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="location" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Work model</label>
                                <select id="location" name="location"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25">
                                    <option value="">Select</option>
                                    @foreach (['Remote', 'Hybrid', 'In person'] as $option)
                                        <option value="{{ $option }}" @selected(old('location') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Application Details</h2>
                        <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="applied_at" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Applied date</label>
                                <input id="applied_at" name="applied_at" type="date" value="{{ old('applied_at', now()->format('Y-m-d')) }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('applied_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="job_url" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Job URL</label>
                                <input id="job_url" name="job_url" type="url" value="{{ old('job_url') }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('job_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="space-y-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Quick Stats</h2>
                        <div class="mt-3 space-y-4">
                            <div>
                                <label for="personal_score" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Personal score</label>
                                <input id="personal_score" name="personal_score" type="number" min="0" max="10" step="0.1" value="{{ old('personal_score') }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('personal_score')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="salary_offered" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Company salary</label>
                                <input id="salary_offered" name="salary_offered" type="number" min="0" step="0.01" value="{{ old('salary_offered') }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('salary_offered')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="salary_expected" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Expected salary</label>
                                <input id="salary_expected" name="salary_expected" type="number" min="0" step="0.01" value="{{ old('salary_expected') }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:border-[#415A77] focus:outline-none focus:ring-2 focus:ring-[#415A77]/25" />
                                @error('salary_expected')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-[#0D1B2A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#415A77]">
                    Save application
                </button>
            </div>
        </form>
    </div>
@endsection
