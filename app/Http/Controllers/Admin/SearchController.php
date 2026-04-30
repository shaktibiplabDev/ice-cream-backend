<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return redirect()->route('admin.dashboard');
        }

        $results = [];

        // Search inquiries
        $inquiries = Inquiry::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('business_name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($inquiries as $inquiry) {
            $results[] = [
                'type' => 'Inquiry',
                'title' => $inquiry->name,
                'subtitle' => $inquiry->business_name ?? 'No business name',
                'status' => $inquiry->status,
                'url' => route('admin.inquiries.show', $inquiry->id),
                'date' => $inquiry->created_at,
            ];
        }

        // Search distributors
        $distributors = Distributor::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('contact_person', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($distributors as $distributor) {
            $results[] = [
                'type' => 'Distributor',
                'title' => $distributor->name,
                'subtitle' => $distributor->contact_person ?? 'No contact person',
                'status' => $distributor->is_active ? 'Active' : 'Inactive',
                'url' => route('admin.distributors.show', $distributor->id),
                'date' => $distributor->created_at,
            ];
        }

        // Search products
        $products = Product::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'type' => 'Product',
                'title' => $product->name,
                'subtitle' => 'SKU: ' . ($product->sku ?? 'N/A'),
                'status' => $product->is_active ? 'Active' : 'Inactive',
                'url' => route('admin.products.show', $product->id),
                'date' => $product->created_at,
            ];
        }

        // Search warehouses
        $warehouses = Warehouse::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%")
                  ->orWhere('city', 'like', "%{$query}%")
                  ->orWhere('manager_name', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($warehouses as $warehouse) {
            $results[] = [
                'type' => 'Warehouse',
                'title' => $warehouse->name,
                'subtitle' => $warehouse->code . ' - ' . $warehouse->city,
                'status' => $warehouse->is_active ? 'Active' : 'Inactive',
                'url' => route('admin.warehouses.show', $warehouse->id),
                'date' => $warehouse->created_at,
            ];
        }

        // Sort by date descending
        usort($results, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('admin.search.index', [
            'query' => $query,
            'results' => $results,
            'count' => count($results),
        ]);
    }
}
