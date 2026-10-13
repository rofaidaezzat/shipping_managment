<?php

namespace App\Http\Controllers\Ports;

use App\Exports\PortExport;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessDataJob;
use App\Models\Company;
use App\Models\Port;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PortController extends Controller
{
    /**
     * Export all ports to Excel and dispatch background ProcessDataJob.
     */
    public function export()
    {
        // 1. Dispatch background job to demonstrate queue handling
        ProcessDataJob::dispatch();

        // 2. Download Excel spreadsheet using Maatwebsite Excel
        $filename = 'ports_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new PortExport, $filename);
    }

    /**
     * Display a listing of ports (HTML or JSON).
     */
    public function index(Request $request)
    {
        $ports = Port::with('company')->withCount('containers')->orderBy('name')->get();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Ports retrieved successfully.',
                'results' => $ports->count(),
                'data'    => $ports,
            ], 200);
        }

        return view('ports.index', compact('ports'));
    }

    /**
     * Show form to create a new port (HTML).
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        return view('ports.create', compact('companies'));
    }

    /**
     * Store a newly created port (JSON or Web).
     * Validation is done in the controller only.
     * Composite unique: code must be unique per company.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'type'       => ['required', 'string', 'in:Sea,Air,Land'],
            'name'       => ['required', 'string', 'max:150'],
            'country'    => ['nullable', 'string', 'max:100'],
            'code'       => [
                'required',
                'string',
                'max:20',
                Rule::unique('ports')->where(fn ($q) => $q->where('company_id', $request->input('company_id'))),
            ],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $port = Port::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 201,
                'message' => 'Port created successfully.',
                'data'    => $port->load('company'),
            ], 201);
        }

        return redirect()->route('ports.index')
            ->with('success', "Port '{$port->name}' ({$port->code}) created successfully!");
    }

    /**
     * Display the specified port with containers (JSON or Web).
     */
    public function show(Request $request, Port $port)
    {
        $port->load(['company', 'containers']);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Port retrieved successfully.',
                'data'    => $port,
            ], 200);
        }

        return view('ports.show', compact('port'));
    }

    /**
     * Show form to edit a port (HTML).
     */
    public function edit(Port $port)
    {
        $companies = Company::orderBy('name')->get();
        return view('ports.edit', compact('port', 'companies'));
    }

    /**
     * Update the specified port (JSON or Web).
     * Validation is done in the controller only.
     */
    public function update(Request $request, Port $port)
    {
        $companyId = $request->input('company_id', $port->company_id);

        $validated = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'type'       => ['required', 'string', 'in:Sea,Air,Land'],
            'name'       => ['required', 'string', 'max:150'],
            'country'    => ['nullable', 'string', 'max:100'],
            'code'       => [
                'required',
                'string',
                'max:20',
                Rule::unique('ports')
                    ->where(fn ($q) => $q->where('company_id', $companyId))
                    ->ignore($port->id),
            ],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $port->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Port updated successfully.',
                'data'    => $port->load('company'),
            ], 200);
        }

        return redirect()->route('ports.index')
            ->with('success', "Port '{$port->name}' updated successfully!");
    }

    /**
     * Delete a port (JSON or Web).
     * RESTRICT: Cannot delete if port has associated containers.
     */
    public function destroy(Request $request, Port $port)
    {
        if ($port->containers()->exists()) {
            $msg = "Cannot delete port '{$port->name}' because it has {$port->containers()->count()} container(s). Deletion prevented by RESTRICT policy.";

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => $msg,
                    'data'    => null,
                ], 422);
            }

            return redirect()->route('ports.index')->with('error', $msg);
        }

        try {
            $portName = $port->name;
            $port->delete();

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => "Port '{$portName}' deleted successfully.",
                    'data'    => null,
                ], 200);
            }

            return redirect()->route('ports.index')
                ->with('success', "Port '{$portName}' deleted successfully!");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => $e->getMessage(),
                    'data'    => null,
                ], 422);
            }

            return redirect()->route('ports.index')->with('error', $e->getMessage());
        }
    }
}
