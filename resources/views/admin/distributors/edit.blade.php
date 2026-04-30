@extends('layouts.admin')

@section('title', 'Edit Distributor')

@section('content')
    <div class="page-header">
        <h1>
            <small>Distribution</small>
            Edit Distributor
        </h1>
        <a href="{{ route('admin.distributors.show', $distributor->id) }}" class="date-badge">View profile</a>
    </div>

    <form method="POST" action="{{ route('admin.distributors.update', $distributor->id) }}" id="distributorForm">
        @csrf
        @method('PUT')
        @include('admin.distributors._form', ['mode' => 'edit', 'distributor' => $distributor])
    </form>
@endsection
