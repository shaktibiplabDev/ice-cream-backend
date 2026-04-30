<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributorRequest;
use App\Models\Distributor;

class DistributorController extends Controller
{
    public function index()
    {
        $distributors = Distributor::latest()->paginate(20);
        return view('admin.distributors.index', compact('distributors'));
    }

    public function create()
    {
        return view('admin.distributors.create');
    }

    public function store(DistributorRequest $request)
    {
        Distributor::create($request->validated());
        
        return redirect()->route('admin.distributors.index')->with('success', 'Distributor added successfully');
    }

    public function show($id)
    {
        $distributor = Distributor::findOrFail($id);
        return view('admin.distributors.show', compact('distributor'));
    }

    public function edit($id)
    {
        $distributor = Distributor::findOrFail($id);
        return view('admin.distributors.edit', compact('distributor'));
    }

    public function update(DistributorRequest $request, $id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->update($request->validated());
        
        return redirect()->route('admin.distributors.index')->with('success', 'Distributor updated successfully');
    }

    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();
        
        return redirect()->route('admin.distributors.index')->with('success', 'Distributor deleted successfully');
    }
}
