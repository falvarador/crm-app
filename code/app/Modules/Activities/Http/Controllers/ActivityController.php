<?php

namespace App\Modules\Activities\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Activities\Http\Requests\StoreActivityRequest;
use App\Modules\Core\Support\CrmStateRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct(private readonly CrmStateRepository $repository) {}

    public function index(Request $request): View|string
    {
        $state = $this->repository->all();
        $filter = (string) $request->query('filter', 'all');

        $filteredActivities = array_values(array_filter(
            $state['activities'],
            static function (array $act) use ($filter): bool {
                return match ($filter) {
                    'pending' => ! $act['completed'],
                    'completed' => (bool) $act['completed'],
                    'llamada' => $act['type'] === 'llamada',
                    'reunion' => $act['type'] === 'reunion',
                    default => true,
                };
            }
        ));

        $pendingActivities = array_values(array_filter(
            $filteredActivities,
            static fn (array $act): bool => ! $act['completed']
        ));
        $completedActivities = array_values(array_filter(
            $filteredActivities,
            static fn (array $act): bool => (bool) $act['completed']
        ));

        $totalPending = count(array_filter(
            $state['activities'],
            static fn (array $act): bool => ! $act['completed']
        ));
        $totalCompleted = count(array_filter(
            $state['activities'],
            static fn (array $act): bool => (bool) $act['completed']
        ));

        $view = view('modules.activities.index', [
            'filter' => $filter,
            'pendingActivities' => $pendingActivities,
            'completedActivities' => $completedActivities,
            'totalPending' => $totalPending,
            'totalCompleted' => $totalCompleted,
            'overdueCount' => 2,
            'companies' => $state['companies'],
        ]);

        if ($request->hasHeader('X-Fragment-Request')) {
            $fragment = (string) $request->header('X-Fragment-Name', 'activities-feed');
            $allowed = ['activities-kpi', 'activities-feed'];

            return $view->fragment(in_array($fragment, $allowed, true) ? $fragment : 'activities-feed');
        }

        return $view;
    }

    public function store(StoreActivityRequest $request): JsonResponse|RedirectResponse
    {
        $activity = $this->repository->addActivity($request->validated());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Actividad registrada correctamente.',
                'activity' => $activity,
            ], 201);
        }

        return redirect()->route('activities.index');
    }

    public function toggle(Request $request, string $activity): JsonResponse|RedirectResponse
    {
        $updated = $this->repository->toggleActivity($activity);

        if ($updated === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Actividad no encontrada.',
            ], 404);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => $updated['completed'] ? 'Tarea marcada como completada.' : 'Tarea reabierta como pendiente.',
                'activity' => $updated,
            ]);
        }

        return redirect()->back();
    }
}
