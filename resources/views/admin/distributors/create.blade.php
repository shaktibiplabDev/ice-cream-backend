@extends('layouts.admin')

@section('title', 'Add Distributor')

@section('content')
    <div class="page-header">
        <h1>
            <small>Distribution</small>
            Add Distributor
        </h1>
        <a href="{{ route('admin.distributors.index') }}" class="date-badge">Back to distributors</a>
    </div>

    <form method="POST" action="{{ route('admin.distributors.store') }}" id="distributorForm">
        @csrf
        @include('admin.distributors._form', ['mode' => 'create'])
    </form>
@endsection
