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
        $stats = $this->repository->computePipelineStats();
        $period = (string) $request->query('period', '30d');

        $multiplier = match ($period) {
            'month' => 0.85,
            'quarter' => 1.35,
            default => 1.0,
        };

        $openValue = (int) round($stats['open_value'] * $multiplier);
        $pendingTasks = array_values(array_filter(
            $state['activities'],
            static fn (array $act): bool => ! $act['completed']
        ));
        $completedCount = count(array_filter(
            $state['activities'],
            static fn (array $act): bool => (bool) $act['completed']
        ));
        $totalTasks = count($state['activities']);
        $closingDeals = array_values(array_filter(
            $state['deals'],
            static fn (array $deal): bool => (bool) ($deal['closing_soon'] ?? false)
        ));

        $closingTotal = array_sum(array_column($closingDeals, 'amount'));

        $recentFeed = [
            [
                'actor' => 'Sofía Méndez',
                'time' => 'Hace 15m',
                'tone' => 'primary',
                'summary' => 'Cambió de etapa Contrato Cloud Enterprise a Negociación',
                'meta' => '$34,000 USD · Soluciones Alpha',
                'quote' => null,
            ],
            [
                'actor' => 'Carlos Vega',
                'time' => 'Hace 1h',
                'tone' => 'neutral',
                'summary' => 'Registró llamada con Elena Ramos (FinanSmart)',
                'meta' => null,
                'quote' => '“Feedback positivo sobre la cotización personalizada”',
            ],
            [
                'actor' => 'Nuevo Prospecto',
                'time' => 'Hace 3h',
                'tone' => 'emerald',
                'summary' => 'Asignado automáticamente: DataDrive Latam',
                'meta' => 'Origen: Formulario Web Inbound',
                'quote' => null,
            ],
            [
                'actor' => 'Diego Cruz',
                'time' => 'Hace 4h',
                'tone' => 'primary',
                'summary' => 'Agendó reunión técnica “Revisión de API y Seguridad”',
                'meta' => 'Mañana a las 11:00 AM',
                'quote' => null,
            ],
        ];

        $view = view('modules.dashboard.index', [
            'period' => $period,
            'openValue' => $openValue,
            'conversionRate' => $stats['conversion_rate'],
            'leadsCount' => count($state['contacts']),
            'pendingTasksCount' => count($pendingTasks),
            'completedTasksCount' => $completedCount,
            'totalTasksCount' => $totalTasks,
            'tasks' => array_slice($state['activities'], 0, 5),
            'recentFeed' => $recentFeed,
            'closingDeals' => $closingDeals,
            'closingTotal' => $closingTotal,
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
