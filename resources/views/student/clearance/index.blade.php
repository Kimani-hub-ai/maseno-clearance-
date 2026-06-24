<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Application') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
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
                    <div class="text-5xl mb-4">📋</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No active application</h3>
                    <p class="text-gray-600 mb-6">
                        Hello, <strong>{{ $student->display_name }}</strong>. You have not submitted any application yet.
                        Applications can be for graduation, deferral, transfer, withdrawal, or other purposes.
                    </p>
                    <a href="{{ route('student.clearance.create') }}"
                       class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                        Submit New Application
                    </a>
                </div>

            @else
                {{-- Application type label map --}}
                @php
                    $typeLabels = [
                        'graduation' => '🎓 Graduation Clearance',
                        'deferral'   => '📅 Deferral of Studies',
                        'transfer'   => '🔄 Transfer to Another Institution',
                        'withdrawal' => '🚪 Withdrawal from University',
                        'other'      => '📝 Other',
                    ];
                    $statusColors = [
                        'pending'  => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];
                    $statusValue = $application->status->value;
                    $applicationType = $application->application_type ?? 'graduation';
                @endphp

                {{-- Application Overview Card --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ $typeLabels[$applicationType] ?? 'Application' }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-0.5">
                                Academic Year: <strong>{{ $application->academic_year }}</strong>
                            </p>
                            <p class="text-sm text-gray-500">
                                Applicant: <strong>{{ $application->student_full_name }}</strong>
                            </p>
                            <p class="text-sm text-gray-500">
                                Submitted: {{ $application->submitted_at?->format('d M Y, g:i A') }}
                                ({{ $application->submitted_at?->diffForHumans() }})
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $application->status->label() }}
                        </span>
                    </div>

                    {{-- Remarks (if registrar added any) --}}
                    @if ($application->remarks)
                        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm text-gray-700">
                            <strong>Registrar note:</strong> {{ $application->remarks }}
                        </div>
                    @endif

                    {{-- Progress Bar --}}
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Department approvals</span>
                            <span>{{ $application->progressPercentage() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full transition-all"
                                 style="width: {{ $application->progressPercentage() }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Per-Department Status --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Department Review Status</h3>
                    <div class="space-y-3">
                        @foreach ($application->departmentClearances as $clearance)
                            @php
                                $deptColors = [
                                    'pending'  => 'bg-gray-100 text-gray-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                                $deptIcons = [
                                    'pending'  => '⏳',
                                    'approved' => '✅',
                                    'rejected' => '❌',
                                ];
                                $deptStatusValue = $clearance->status->value;
                            @endphp
                            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $deptIcons[$deptStatusValue] ?? '' }}
                                        {{ $clearance->department->name }}
                                    </p>
                                    @if ($clearance->remarks)
                                        <p class="text-sm text-gray-500 mt-0.5 ml-5">{{ $clearance->remarks }}</p>
                                    @endif
                                    @if ($clearance->reviewed_at)
                                        <p class="text-xs text-gray-400 mt-0.5 ml-5">
                                            Reviewed {{ $clearance->reviewed_at->format('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $deptColors[$deptStatusValue] ?? '' }}">
                                    {{ $clearance->status->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Supporting Documents --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Supporting Documents</h3>

                    <form method="POST" action="{{ route('student.clearance.documents.upload') }}"
                          enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="flex flex-wrap items-center gap-3">
                            <select name="department_id"
                                    class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">General document</option>
                                @foreach ($application->departmentClearances as $clearance)
                                    <option value="{{ $clearance->department->id }}">
                                        {{ $clearance->department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="file" name="document" required class="text-sm" />
                            <button type="submit"
                                    class="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700 transition">
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

                {{-- Certificate Section (only when fully approved) --}}
                @if ($application->certificate)
                    <div class="bg-green-50 border border-green-200 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-md font-semibold text-green-800 mb-2">
                            🎉 Your certificate is ready!
                        </h3>
                        <p class="text-sm text-green-700 mb-4">
                            Certificate No: <strong>{{ $application->certificate->certificate_number }}</strong><br>
                            Issued: {{ $application->certificate->issued_at->format('d M Y') }}
                        </p>
                        @if ($application->certificate->pdf_path)
                            <a href="{{ route('student.certificate.download', $application->certificate) }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition">
                                ⬇ Download Certificate
                            </a>
                        @else
                            <p class="text-sm text-green-600">
                                Your certificate is being generated. Please check back in a few minutes.
                            </p>
                        @endif
                    </div>
                @endif

            @endif
        </div>
    </div>
</x-app-layout>