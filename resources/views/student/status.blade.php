<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Application Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">Your Clearance Status</h3>

                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-3 border">Department</th>
                                <th class="p-3 border">Status</th>
                                <th class="p-3 border">Remarks</th>
                                <th class="p-3 border">Date Reviewed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 border">Library</td>
                                <td class="p-3 border">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">
                                        Pending
                                    </span>
                                </td>
                                <td class="p-3 border">-</td>
                                <td class="p-3 border">-</td>
                            </tr>
                            <tr>
                                <td class="p-3 border">Finance</td>
                                <td class="p-3 border">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">
                                        Pending
                                    </span>
                                </td>
                                <td class="p-3 border">-</td>
                                <td class="p-3 border">-</td>
                            </tr>
                            <tr>
                                <td class="p-3 border">Hostel</td>
                                <td class="p-3 border">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">
                                        Pending
                                    </span>
                                </td>
                                <td class="p-3 border">-</td>
                                <td class="p-3 border">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
