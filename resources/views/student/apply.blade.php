<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Apply for Clearance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Clearance Application Form</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Fill in the form below to apply for clearance from all departments.
                    </p>

                    <form method="POST" action="/student/apply">
                        @csrf

                        {{-- Student Number --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Student Number
                            </label>
                            <input type="text" name="student_number"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="e.g. SCT/2020/001" required>
                        </div>

                        {{-- Course --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Course / Programme
                            </label>
                            <input type="text" name="course"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="e.g. Bachelor of Science in Computer Science" required>
                        </div>

                        {{-- Year of Study --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Year of Study
                            </label>
                            <select name="year_of_study"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="1">Year 1</option>
                                <option value="2">Year 2</option>
                                <option value="3">Year 3</option>
                                <option value="4">Year 4</option>
                            </select>
                        </div>

                        {{-- Reason --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Reason for Clearance
                            </label>
                            <select name="reason"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="graduation">Graduation</option>
                                <option value="transfer">Transfer</option>
                                <option value="deferral">Deferral</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                            Submit Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
