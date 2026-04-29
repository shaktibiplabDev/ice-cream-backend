@extends('layouts.admin')

@section('title', 'Inquiries')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">All Inquiries</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Business</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($inquiries as $inquiry)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $inquiry->id }}</td>
                        <td class="px-6 py-4">{{ $inquiry->name }}</td>
                        <td class="px-6 py-4">{{ $inquiry->business_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $inquiry->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $inquiry->status == 'new' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $inquiry->status == 'read' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $inquiry->status == 'replied' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($inquiry->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $inquiry->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.inquiries.show', $inquiry->id) }}" class="text-purple-600 hover:text-purple-900 mr-3">View</a>
                            <form action="{{ route('admin.inquiries.destroy', $inquiry->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this inquiry?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $inquiries->links() }}
        </div>
    </div>
@endsection
