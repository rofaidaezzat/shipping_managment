<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies (HTML or JSON).
     */
    public function index(Request $request)
    {
        $companies = Company::withCount('ports')->orderBy('name')->get();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Companies retrieved successfully.',
                'results' => $companies->count(),
                'data'    => $companies,
            ], 200);
        }

        return view('companies.index', compact('companies'));
    }

    /**
     * Show form to create a new company (HTML).
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created company (JSON or Web).
     * Validation is done in the controller only.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:20', 'unique:companies,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $company = Company::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 201,
                'message' => 'Company created successfully.',
                'data'    => $company,
            ], 201);
        }

        return redirect()->route('companies.index')
            ->with('success', "Company '{$company->name}' created successfully!");
    }

    /**
     * Display the specified company with ports (JSON or Web).
     */
    public function show(Request $request, Company $company)
    {
        $company->load(['ports.containers']);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Company retrieved successfully.',
                'data'    => $company,
            ], 200);
        }

        return view('companies.show', compact('company'));
    }

    /**
     * Show form to edit a company (HTML).
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified company (JSON or Web).
     * Validation is done in the controller only.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:20', Rule::unique('companies', 'code')->ignore($company->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $company->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Company updated successfully.',
                'data'    => $company,
            ], 200);
        }

        return redirect()->route('companies.index')
            ->with('success', "Company '{$company->name}' updated successfully!");
    }

    /**
     * Delete a company (JSON or Web).
     * RESTRICT: Cannot delete if company has associated ports.
     */
    public function destroy(Request $request, Company $company)
    {
        if ($company->ports()->exists()) {
            $msg = "Cannot delete company '{$company->name}' because it has {$company->ports()->count()} associated port(s). Deletion prevented by RESTRICT policy.";

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => $msg,
                    'data'    => null,
                ], 422);
            }

            return redirect()->route('companies.index')->with('error', $msg);
        }

        try {
            $companyName = $company->name;
            $company->delete();

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => "Company '{$companyName}' deleted successfully.",
                    'data'    => null,
                ], 200);
            }

            return redirect()->route('companies.index')
                ->with('success', "Company '{$companyName}' deleted successfully!");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => $e->getMessage(),
                    'data'    => null,
                ], 422);
            }

            return redirect()->route('companies.index')->with('error', $e->getMessage());
        }
    }
}
