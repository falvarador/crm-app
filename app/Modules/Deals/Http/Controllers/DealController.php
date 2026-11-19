<?php

namespace App\Modules\Deals\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Support\CrmStateRepository;
use App\Modules\Deals\Http\Requests\StoreDealRequest;
use App\Modules\Deals\Http\Requests\UpdateDealStageRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function __construct(private readonly CrmStateRepository $repository) {}

    public function index(Request $request): View|string
    {
        $state = $this->repository->all();
        $owner = (string) $request->query('owner', '');
        $stageFilter = (string) $request->query('stage', '');

        $filteredDeals = array_values(array_filter(
            $state['deals'],
            static function (array $deal) use ($owner, $stageFilter): bool {
                if ($owner !== '' && $deal['owner_id'] !== $owner) {
                    return false;
                }
                if ($stageFilter !== '' && $deal['stage'] !== $stageFilter) {
                    return false;
                }

                return true;
            }
        ));

        $dealsByStage = [];
        $stageTotals = [];
        foreach ($state['stages'] as $key => $meta) {
            $stageDeals = array_values(array_filter(
                $filteredDeals,
                static fn (array $d): bool => $d['stage'] === $key
            ));
            $dealsByStage[$key] = $stageDeals;
            $stageTotals[$key] = array_sum(array_column($stageDeals, 'amount'));
        }

        $weightedValue = collect($filteredDeals)
            ->whereNotIn('stage', ['ganada', 'perdida'])
            ->sum(fn (array $deal): float => $deal['amount'] * $deal['probability'] / 100);

        $companyOptions = [];
        foreach ($state['companies'] as $company) {
            $companyOptions[$company['id']] = $company['name'];
        }

        $view = view('modules.deals.index', [
            'deals' => $filteredDeals,
            'dealsByStage' => $dealsByStage,
            'stageTotals' => $stageTotals,
            'stages' => $state['stages'],
            'weightedValue' => $weightedValue,
            'totalDealsCount' => count($filteredDeals),
            'companyOptions' => $companyOptions,
            'filters' => [
                'owner' => $owner,
                'stage' => $stageFilter,
            ],
        ]);

        if ($request->hasHeader('X-Fragment-Request')) {
            $fragment = (string) $request->header('X-Fragment-Name', 'deals-board');
            $allowed = ['deals-kpi', 'deals-board'];

            return $view->fragment(in_array($fragment, $allowed, true) ? $fragment : 'deals-board');
        }

        return $view;
    }

    public function store(StoreDealRequest $request): JsonResponse|RedirectResponse
    {
        $deal = $this->repository->addDeal($request->validated());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Oportunidad registrada en el pipeline.',
                'deal' => $deal,
            ], 201);
        }

        return redirect()->route('deals.index');
    }

    public function updateStage(UpdateDealStageRequest $request, string $deal): JsonResponse
    {
        $updated = $this->repository->updateDealStage($deal, (string) $request->validated('stage'));

        if ($updated === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró la oportunidad o etapa especificada.',
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Etapa actualizada correctamente.',
            'deal' => $updated,
        ]);
    }
}
