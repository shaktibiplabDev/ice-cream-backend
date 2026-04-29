@extends('layouts.admin')

@section('title', 'Distributors')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Distributors</h3>
            <a href="{{ route('admin.distributors.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                + Add New Distributor
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone/Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($distributors as $distributor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $distributor->id }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $distributor->name }}</div>
                            <div class="text-xs text-gray-500">{{ $distributor->address }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $distributor->contact_person ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm">{{ $distributor->phone ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $distributor->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $distributor->service_area ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $distributor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $distributor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.distributors.show', $distributor->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('admin.distributors.edit', $distributor->id) }}" class="text-purple-600 hover:text-purple-900 mr-3">Edit</a>
                            <form action="{{ route('admin.distributors.destroy', $distributor->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this distributor?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $distributors->links() }}
        </div>
    </div>
@endsection