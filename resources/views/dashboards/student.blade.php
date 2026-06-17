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
                        Use the options below to manage your clearance application.
                    </p>
                </div>
            </div>

            {{-- Action Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                {{-- Apply Card --}}
                <a href="/student/apply"
                    class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-blue-600 text-3xl mb-3">📋</div>
                    <h4 class="font-semibold text-gray-800">Apply for Clearance</h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Submit a new clearance application.
                    </p>
                </a>

                {{-- Status Card --}}
                <a href="/student/status"
                    class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-yellow-500 text-3xl mb-3">📊</div>
                    <h4 class="font-semibold text-gray-800">Track Status</h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Check your clearance status per department.
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
