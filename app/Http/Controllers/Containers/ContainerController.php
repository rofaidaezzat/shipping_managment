<?php

namespace App\Http\Controllers\Containers;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\Port;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContainerController extends Controller
{
    /**
     * Display a listing of containers (HTML or JSON).
     */
    public function index(Request $request)
    {
        $containers = Container::with('port.company')->orderBy('container_number')->get();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Containers retrieved successfully.',
                'results' => $containers->count(),
                'data'    => $containers,
            ], 200);
        }

        return view('containers.index', compact('containers'));
    }

    /**
     * Show form to create a new container (HTML).
     */
    public function create()
    {
        $ports = Port::with('company')->orderBy('name')->get();
        return view('containers.create', compact('ports'));
    }

    /**
     * Store a newly created container (JSON or Web).
     * Validation is done in the controller only.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'port_id'          => ['nullable', 'integer', 'exists:ports,id'],
            'container_number' => ['required', 'string', 'max:20', 'unique:containers,container_number'],
            'type'             => ['required', Rule::in(Container::TYPES)],
            'status'           => ['required', Rule::in(Container::STATUSES)],
            'is_active'        => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $container = Container::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 201,
                'message' => 'Container created successfully.',
                'data'    => $container->load('port.company'),
            ], 201);
        }

        return redirect()->route('containers.index')
            ->with('success', "Container '{$container->container_number}' created successfully!");
    }

    /**
     * Display the specified container (JSON or Web).
     */
    public function show(Request $request, Container $container)
    {
        $container->load('port.company');

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Container retrieved successfully.',
                'data'    => $container,
            ], 200);
        }

        return view('containers.show', compact('container'));
    }

    /**
     * Show form to edit an existing container (HTML).
     */
    public function edit(Container $container)
    {
        $ports = Port::with('company')->orderBy('name')->get();
        return view('containers.edit', compact('container', 'ports'));
    }

    /**
     * Update the specified container (JSON or Web).
     * Validation is done in the controller only.
     */
    public function update(Request $request, Container $container)
    {
        $validated = $request->validate([
            'port_id'          => ['nullable', 'integer', 'exists:ports,id'],
            'container_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('containers', 'container_number')->ignore($container->id),
            ],
            'type'             => ['required', Rule::in(Container::TYPES)],
            'status'           => ['required', Rule::in(Container::STATUSES)],
            'is_active'        => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $container->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Container updated successfully.',
                'data'    => $container->load('port.company'),
            ], 200);
        }

        return redirect()->route('containers.index')
            ->with('success', "Container '{$container->container_number}' updated successfully!");
    }

    /**
     * Delete a container (JSON or Web).
     */
    public function destroy(Request $request, Container $container)
    {
        $containerNumber = $container->container_number;
        $container->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => "Container '{$containerNumber}' deleted successfully.",
                'data'    => null,
            ], 200);
        }

        return redirect()->route('containers.index')
            ->with('success', "Container '{$containerNumber}' deleted successfully!");
    }
}
