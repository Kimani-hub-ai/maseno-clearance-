<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Clearance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if (!$application)
                {{-- No application yet --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No active clearance application</h3>
                    <p class="text-gray-600 mb-6">
                        Welcome, {{ $student->full_name }}. When you're ready to graduate,
                        apply for clearance to begin the department approval process.
                    </p>
                    <a href="{{ route('student.clearance.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Apply for Clearance
                    </a>
                </div>
            @else
                {{-- Active / completed application --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Academic Year {{ $application->academic_year }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                Submitted {{ $application->submitted_at?->diffForHumans() }}
                            </p>
                        </div>
                        @php
                            $statusColors = [
                                'submitted' => 'bg-blue-100 text-blue-800',
                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                'cleared' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                            $statusValue = $application->status->value;
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $application->status->label() }}
                        </span>
                    </div>

                    {{-- Progress bar --}}
                    <div class="mb-2">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full transition-all"
                                 style="width: {{ $application->progressPercentage() }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $application->progressPercentage() }}% of departments have approved
                        </p>
                    </div>
                </div>

                {{-- Per-department status cards --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Department Status</h3>
                    <div class="space-y-3">
                        @foreach ($application->departmentClearances as $clearance)
                            @php
                                $deptStatusColors = [
                                    'pending' => 'bg-gray-100 text-gray-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                                $deptStatusValue = $clearance->status->value;
                            @endphp
                            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $clearance->department->name }}</p>
                                    @if ($clearance->remarks)
                                        <p class="text-sm text-gray-500 mt-1">{{ $clearance->remarks }}</p>
                                    @endif
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $deptStatusColors[$deptStatusValue] ?? '' }}">
                                    {{ $clearance->status->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Document upload --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Supporting Documents</h3>

                    <form method="POST" action="{{ route('student.clearance.documents.upload') }}" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="flex items-center gap-3">
                            <select name="department_id" class="border-gray-300 rounded-md text-sm">
                                <option value="">General document</option>
                                @foreach ($application->departmentClearances as $clearance)
                                    <option value="{{ $clearance->department->id }}">{{ $clearance->department->name }}</option>
                                @endforeach
                            </select>
                            <input type="file" name="document" required class="text-sm" />
                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                                Upload
                            </button>
                        </div>
                        @error('document')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </form>

                    @if ($application->documents->isNotEmpty())
                        <ul class="divide-y divide-gray-100">
                            @foreach ($application->documents as $doc)
                                <li class="py-2 flex items-center justify-between text-sm">
                                    <span class="text-gray-700">{{ $doc->original_name }}</span>
                                    <a href="{{ route('student.clearance.documents.download', $doc) }}"
                                       class="text-indigo-600 hover:underline">Download</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No documents uploaded yet.</p>
                    @endif
                </div>

                {{-- Certificate (if cleared) --}}
                 <p class="text-sm text-gray-500">Certificate download will be available here once the certificate system is built.</p>
                    </div>
                @endif
        </div>
    </div>
</x-app-layout>
