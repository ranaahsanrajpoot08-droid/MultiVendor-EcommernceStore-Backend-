<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminController extends Controller {
    public function vendors() {
        return response()->json(Vendor::with('user')->latest()->get());
    }

    public function approveVendor($id) {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'approved']);
        return response()->json($vendor);
    }

    public function rejectVendor($id) {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'rejected']);
        return response()->json($vendor);
    }

    public function users() {
        return response()->json(User::latest()->get());
    }

    public function stats() {
        return response()->json([
            'users' => User::count(),
            'vendors' => Vendor::count(),
            'pending_vendors' => Vendor::where('status', 'pending')->count(),
            'orders' => \App\Models\Order::count(),
            'products' => \App\Models\Product::count(),
        ]);
    }
}
