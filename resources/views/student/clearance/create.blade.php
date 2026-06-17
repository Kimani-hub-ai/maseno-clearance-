<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Apply for Clearance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-gray-700 mb-6">
                    You are about to apply for clearance for the academic year
                    <strong>{{ $academicYear }}</strong>. Once submitted, your application
                    will be sent to all seven departments for review:
                </p>

                <ul class="list-disc list-inside text-sm text-gray-600 mb-6 space-y-1">
                    <li>Finance / Bursar</li>
                    <li>Library</li>
                    <li>Hostel / Dean of Students</li>
                    <li>ICT Department</li>
                    <li>Faculty / Department</li>
                    <li>Games &amp; Sports</li>
                    <li>Academic Registrar</li>
                </ul>

                <p class="text-sm text-amber-600 mb-6">
                    Make sure you have settled outstanding obligations (fees, library books,
                    hostel room, equipment) before applying to avoid rejection.
                </p>

                <form method="POST" action="{{ route('student.clearance.store') }}">
                    @csrf
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('student.clearance.index') }}" class="text-sm text-gray-600 underline">
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
