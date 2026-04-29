<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'service_area' => 'nullable|string|max:500',
            'delivery_capacity' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'timings' => 'nullable|string|max:255',
            'social_media' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Distributor::create($validated);
        
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'service_area' => 'nullable|string|max:500',
            'delivery_capacity' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'timings' => 'nullable|string|max:255',
            'social_media' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $distributor = Distributor::findOrFail($id);
        $distributor->update($validated);
        
        return redirect()->route('admin.distributors.index')->with('success', 'Distributor updated successfully');
    }

    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();
        
        return redirect()->route('admin.distributors.index')->with('success', 'Distributor deleted successfully');
    }
}