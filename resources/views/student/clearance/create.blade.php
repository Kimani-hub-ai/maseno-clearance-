<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit an Application') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Intro --}}
                <p class="text-gray-700 mb-6">
                    Complete the form below to submit your application for the academic year
                    <strong>{{ $academicYear }}</strong>. Your request will be forwarded to all
                    relevant departments for review and approval.
                </p>

                <form method="POST" action="{{ route('student.clearance.store') }}">
                    @csrf

                    {{-- Student Full Name --}}
                    <div class="mb-5">
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="{{ old('full_name', auth()->user()->student?->full_name ?? auth()->user()->name) }}"
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter your full name as it appears on your ID"
                        />
                        @error('full_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Application Type --}}
                    <div class="mb-5">
                        <label for="application_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Application Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="application_type"
                            name="application_type"
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="" disabled {{ old('application_type') ? '' : 'selected' }}>— Select type —</option>
                            <option value="graduation"   {{ old('application_type') === 'graduation'   ? 'selected' : '' }}>🎓 Graduation Clearance</option>
                            <option value="deferral"     {{ old('application_type') === 'deferral'     ? 'selected' : '' }}>📅 Deferral of Studies</option>
                            <option value="transfer"     {{ old('application_type') === 'transfer'     ? 'selected' : '' }}>🔄 Transfer to Another Institution</option>
                            <option value="withdrawal"   {{ old('application_type') === 'withdrawal'   ? 'selected' : '' }}>🚪 Withdrawal from University</option>
                            <option value="other"        {{ old('application_type') === 'other'        ? 'selected' : '' }}>📝 Other</option>
                        </select>
                        @error('application_type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Reason / Notes (optional) --}}
                    <div class="mb-6">
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">
                            Additional Notes <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea
                            id="remarks"
                            name="remarks"
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Any additional information for the departments reviewing your application..."
                        >{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Departments info --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <p class="text-sm font-medium text-amber-800 mb-2">
                            ⚠️ Before you submit, ensure you have settled all obligations with:
                        </p>
                        <ul class="list-disc list-inside text-sm text-amber-700 space-y-1">
                            <li>Finance / Bursar (fee balances)</li>
                            <li>Library (books, fines)</li>
                            <li>Hostel / Dean of Students (room, equipment)</li>
                            <li>ICT Department</li>
                            <li>Faculty / Department</li>
                            <li>Games &amp; Sports</li>
                            <li>Academic Registrar</li>
                        </ul>
                        <p class="text-xs text-amber-600 mt-2">
                            Unresolved obligations may result in rejection by that department.
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('student.dashboard') }}" class="text-sm text-gray-600 underline">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Submit Application') }}
                        </x-primary-button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>