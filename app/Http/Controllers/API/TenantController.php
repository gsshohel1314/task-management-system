<?php

namespace App\Http\Controllers\API;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;

class TenantController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->paginate(10);

        return $this->sendResponse($tenants, 'Tenants retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:tenants,email',
            'password'      => 'required|string',
            'subdomain'     => 'required|string|unique:tenants,subdomain',
        ]);

        $tenant = Tenant::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'subdomain' => $request->subdomain,
        ]);

        $tenant->domains()->create([
            'domain' => $request->subdomain . '.' . config('app.domain')
        ]);

        return $this->sendResponse($tenant, 'Tenant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('domains');

        return $this->sendResponse($tenant, 'Tenant retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:tenants,email,' . $tenant->id,
            'password'  => 'required|string',
            'subdomain' => 'required|string|unique:tenants,subdomain,' . $tenant->id,
        ]);

        $tenant->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'subdomain' => $request->subdomain,
        ]);

        $tenant->domains()->update([
            'domain' => $request->subdomain . '.' . config('app.domain')
        ]);

        return $this->sendResponse($tenant, 'Tenant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return $this->sendResponse([], 'Tenant deleted successfully.');
    }
}
