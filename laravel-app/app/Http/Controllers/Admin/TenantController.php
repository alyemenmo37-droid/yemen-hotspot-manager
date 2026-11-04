<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount('users', 'cards')->get();
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'domain' => 'nullable|string|max:100|unique:tenants,domain',
        ]);

        Tenant::create($validated);
        return redirect()->route('admin.tenants.index')->with('success', 'تم إنشاء الشبكة');
    }

    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'domain' => 'nullable|string|max:100|unique:tenants,domain,' . $tenant->id,
        ]);

        $tenant->update($validated);
        return redirect()->route('admin.tenants.index')->with('success', 'تم التحديث');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return back()->with('success', 'تم الحذف');
    }
}
