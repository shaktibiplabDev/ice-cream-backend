<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::latest()->paginate(20);
        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        
        if ($inquiry->status == 'new') {
            $inquiry->update(['status' => 'read']);
        }
        
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function updateStatus(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Status updated successfully');
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();
        
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully');
    }
}