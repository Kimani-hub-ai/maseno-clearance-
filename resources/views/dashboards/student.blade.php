<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">
                        Welcome, {{ auth()->user()->name }}!
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Use the options below to manage your university application request.
                    </p>
                </div>
            </div>

            {{-- Action Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                {{-- Apply Card --}}
                <a href="{{ route('student.clearance.create') }}"
                    class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-blue-600 text-3xl mb-3">📋</div>
                    <h4 class="font-semibold text-gray-800">New Application</h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Submit a new request — graduation, deferral, transfer, or other.
                    </p>
                </a>

                {{-- Status Card --}}
                <a href="{{ route('student.clearance.index') }}"
                    class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-yellow-500 text-3xl mb-3">📊</div>
                    <h4 class="font-semibold text-gray-800">Track My Application</h4>
                    <p class="text-sm text-gray-500 mt-1">
                        View department approval status and any remarks.
                    </p>
                </a>

                {{-- Profile Card --}}
                <a href="/profile"
                    class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-green-500 text-3xl mb-3">👤</div>
                    <h4 class="font-semibold text-gray-800">My Profile</h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Update your personal information.
                    </p>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>