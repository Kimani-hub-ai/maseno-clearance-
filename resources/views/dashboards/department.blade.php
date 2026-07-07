<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Department Clearance Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    Welcome back, <strong>{{ auth()->user()->name }}</strong>!
                    You are reviewing clearance requests for your department.
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-700">Pending Student Clearances</h3>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @if($pendingClearances->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500 text-sm">No pending clearance requests for your department right now.</p>
                    </div>
                @else
                    <div class="overflow-x-auto border border-gray-100 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reg Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Student Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Programme</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingClearances as $checkpoint)
                                    @php $student = $checkpoint->application->student; @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            {{ $student->reg_number ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $student->full_name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $student->programme ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                             <form method="POST"
                                             action="{{ route('department.clearance.review', $checkpoint->id) }}"
                                             class="flex flex-col gap-2">
                                          @csrf
                                              <input type="text"
                                                 name="remarks"
                                                 placeholder="Optional remarks..."
                                                 class="border-gray-300 rounded-md text-xs py-1 px-2 w-full">
                                             <div class="flex gap-2">
                                             <button type="submit" name="action" value="approve"
                                                 style="flex:1;padding:4px 12px;background:#16a34a;color:white;border:none;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;">
                                                 Approve
                                             </button>
                                             <button type="submit" name="action" value="reject"
                                                 style="flex:1;padding:4px 12px;background:#dc2626;color:white;border:none;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;">
                                                 Reject
                                             </button>
                                             </div>
                                             </form>
                                          </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pendingClearances->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>