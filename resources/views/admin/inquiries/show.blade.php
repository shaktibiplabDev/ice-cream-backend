@extends('layouts.admin')

@section('title', 'Inquiry Details')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Inquiry #{{ $inquiry->id }}</h3>
            <a href="{{ route('admin.inquiries.index') }}" class="text-purple-600 hover:text-purple-900">← Back to List</a>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <p class="text-gray-900">{{ $inquiry->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                    <p class="text-gray-900">{{ $inquiry->business_name ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <a href="mailto:{{ $inquiry->email }}" class="text-purple-600 hover:text-purple-900">{{ $inquiry->email }}</a>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Submitted Date</label>
                    <p class="text-gray-900">{{ $inquiry->created_at->format('F d, Y H:i:s') }}</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Requirement</label>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-900">{{ nl2br(e($inquiry->requirement)) }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                    <form action="{{ route('admin.inquiries.update-status', $inquiry->id) }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        @method('PUT')
                        <select name="status" class="border border-gray-300 rounded px-3 py-1">
                            <option value="new" {{ $inquiry->status == 'new' ? 'selected' : '' }}>New</option>
                            <option value="read" {{ $inquiry->status == 'read' ? 'selected' : '' }}>Read</option>
                            <option value="replied" {{ $inquiry->status == 'replied' ? 'selected' : '' }}>Replied</option>
                        </select>
                        <button type="submit" class="bg-purple-600 text-white px-4 py-1 rounded hover:bg-purple-700">Update</button>
                    </form>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Actions</label>
                    <div class="flex space-x-2">
                        <a href="mailto:{{ $inquiry->email }}?subject=Response to your inquiry - Celesty Ice Cream" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Reply via Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
