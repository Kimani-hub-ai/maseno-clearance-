<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_students'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Students</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_officers'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Department Officers</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_applications'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Applications</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['cleared'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Fully Cleared</div>
                </div>
            </div>

            {{-- Create Officer Form --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Create Department Officer Account</h3>
                <form method="POST" action="{{ route('admin.officers.store') }}"
                      class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" required
                               class="w-full border-gray-300 rounded-md text-sm"
                               placeholder="e.g. Jane Mwangi">
                        @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required
                               class="w-full border-gray-300 rounded-md text-sm"
                               placeholder="e.g. jane@maseno.ac.ke">
                        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select name="department_id" required class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full border-gray-300 rounded-md text-sm">
                        @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            Create Officer Account
                        </button>
                    </div>
                </form>
            </div>

            {{-- Officers List --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Officers</h3>
                @if($officers->isEmpty())
                    <p class="text-sm text-gray-500">No officers created yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Email</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Department</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($officers as $officer)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $officer->name }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $officer->email }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $officer->departmentOfficer?->department?->name ?? 'Unassigned' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $officer->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $officer->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
