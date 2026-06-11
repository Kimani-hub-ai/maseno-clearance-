<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Welcome, {{ auth()->user()->name }}! This is the system administration panel.
                    <br><br>
                    <span class="text-sm text-gray-500">
                        User management, department settings, and system config coming in Week 5.
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
