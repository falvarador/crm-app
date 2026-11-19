<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Support\CrmStateRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly CrmStateRepository $repository) {}

    public function index(Request $request): View|string
    {
        $state = $this->repository->all();
        $period = (string) $request->query('period', '30d');
        if (! in_array($period, ['30d', 'month', 'quarter'], true)) {
            $period = '30d';
        }
        $start = match ($period) {
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            default => now()->subDays(30),
        };
        $end = match ($period) {
            'month' => now()->endOfMonth(),
            'quarter' => now()->endOfQuarter(),
            default => now()->addDays(30)->endOfDay(),
        };
        $periodDeals = collect($state['deals'])->filter(fn (array $deal): bool => $deal['created_at'] >= $start && $deal['created_at'] <= now());
        $openDeals = $periodDeals->whereNotIn('stage', ['ganada', 'perdida']);
        $closedDeals = $periodDeals->whereIn('stage', ['ganada', 'perdida']);
        $conversionRate = $closedDeals->isEmpty() ? 0 : round($closedDeals->where('stage', 'ganada')->count() / $closedDeals->count() * 100, 1);
        $tasks = collect($state['activities'])->filter(fn (array $activity): bool => $activity['due_date']?->isToday() ?? false);
        $pendingTasks = $tasks->where('completed', false);
        $closingDeals = collect($state['deals'])->filter(fn (array $deal): bool => ! in_array($deal['stage'], ['ganada', 'perdida'], true)
            && $deal['close_date_iso'] && $deal['close_date_iso'] >= now()->toDateString() && $deal['close_date_iso'] <= $end->toDateString())->values()->all();
        $recentFeed = collect($state['activities'])->take(5)->map(fn (array $activity): array => [
            'actor' => $activity['owner'],
            'time' => $activity['created_at']->diffForHumans(),
            'tone' => $activity['completed'] ? 'emerald' : 'primary',
            'summary' => $activity['subject'],
            'meta' => $activity['company_name'],
            'quote' => $activity['notes'],
        ])->all();

        $view = view('modules.dashboard.index', [
            'period' => $period,
            'openValue' => $openDeals->sum('amount'),
            'conversionRate' => $conversionRate,
            'leadsCount' => collect($state['contacts'])->filter(fn (array $contact): bool => $contact['created_at'] >= $start && $contact['created_at'] <= now())->count(),
            'pendingTasksCount' => count($pendingTasks),
            'completedTasksCount' => $tasks->where('completed', true)->count(),
            'totalTasksCount' => $tasks->count(),
            'tasks' => $tasks->take(5)->all(),
            'recentFeed' => $recentFeed,
            'closingDeals' => $closingDeals,
            'closingTotal' => array_sum(array_column($closingDeals, 'amount')),
            'stages' => $state['stages'],
            'companies' => $state['companies'],
        ]);

        if ($request->hasHeader('X-Fragment-Request')) {
            $fragment = (string) $request->header('X-Fragment-Name', 'dashboard-closing-deals');
            $allowed = ['dashboard-kpis', 'dashboard-tasks', 'dashboard-feed', 'dashboard-closing-deals'];

            return $view->fragment(in_array($fragment, $allowed, true) ? $fragment : 'dashboard-closing-deals');
        }

        return $view;
    }
}
