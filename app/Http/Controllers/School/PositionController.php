<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\PositionPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $positions = Position::withCount('permissions')->orderBy('name')->get();

        $selectedPosition = null;
        $permissionsByModule = [];

        if ($request->filled('position_id')) {
            $selectedPosition = Position::with('permissions')->find($request->integer('position_id'));
            $permissionsByModule = $selectedPosition?->permissions->keyBy('module') ?? [];
        }

        return view('school.positions.index', [
            'positions' => $positions,
            'modules' => Position::MODULES,
            'selectedPosition' => $selectedPosition,
            'permissionsByModule' => $permissionsByModule,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $position = Position::create([
            'school_id' => $request->user()->school_id,
            'name' => $data['name'],
        ]);

        return redirect()->route('school.positions.index', ['position_id' => $position->id])
            ->with('status', 'Position created. Now set its module permissions below.');
    }

    public function updatePermissions(Request $request, Position $position): RedirectResponse
    {
        $data = $request->validate([
            'modules' => ['array'],
            'modules.*.can_view' => ['nullable', 'boolean'],
            'modules.*.can_create' => ['nullable', 'boolean'],
            'modules.*.can_edit' => ['nullable', 'boolean'],
            'modules.*.can_delete' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $position) {
            foreach (Position::MODULES as $module => $label) {
                $flags = $data['modules'][$module] ?? [];

                PositionPermission::updateOrCreate(
                    ['position_id' => $position->id, 'module' => $module],
                    [
                        'can_view' => (bool) ($flags['can_view'] ?? false),
                        'can_create' => (bool) ($flags['can_create'] ?? false),
                        'can_edit' => (bool) ($flags['can_edit'] ?? false),
                        'can_delete' => (bool) ($flags['can_delete'] ?? false),
                    ]
                );
            }
        });

        return redirect()->route('school.positions.index', ['position_id' => $position->id])
            ->with('status', 'Permissions updated.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()->route('school.positions.index')->with('status', 'Position deleted.');
    }
}
