<?php

namespace App\Modules\Companies\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Companies\Http\Requests\StoreCompanyRequest;
use App\Modules\Core\Support\CrmStateRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(private readonly CrmStateRepository $repository) {}

    public function index(Request $request): View|string
    {
        $state = $this->repository->all();
        $sector = (string) $request->query('sector', '');
        $status = (string) $request->query('status', '');
        $owner = (string) $request->query('owner', '');
        $selectedId = (string) $request->query('company_id', '');
        $viewMode = (string) $request->query('view', 'split');

        $filteredCompanies = array_values(array_filter(
            $state['companies'],
            static function (array $company) use ($sector, $status, $owner): bool {
                if ($sector !== '' && $company['sector'] !== $sector) {
                    return false;
                }
                if ($status !== '' && ! str_contains($company['status'], $status)) {
                    return false;
                }
                if ($owner !== '' && $company['owner_id'] !== $owner) {
                    return false;
                }

                return true;
            }
        ));

        $activeCompany = $filteredCompanies[0] ?? null;
        if ($selectedId !== '') {
            foreach ($filteredCompanies as $candidate) {
                if ($candidate['id'] === $selectedId) {
                    $activeCompany = $candidate;
                    break;
                }
            }
        }

        $linkedContacts = $activeCompany
            ? array_values(array_filter(
                $state['contacts'],
                static fn (array $contact): bool => $contact['company_id'] === $activeCompany['id']
            ))
            : [];

        $companyActivities = $activeCompany
            ? array_values(array_filter(
                $state['activities'],
                static fn (array $act): bool => $act['company_id'] === $activeCompany['id']
            ))
            : [];

        $view = view('modules.companies.index', [
            'companies' => $filteredCompanies,
            'totalCompanies' => count($state['companies']),
            'activeCompany' => $activeCompany,
            'linkedContacts' => $linkedContacts,
            'companyActivities' => $companyActivities,
            'filters' => [
                'sector' => $sector,
                'status' => $status,
                'owner' => $owner,
                'view' => $viewMode,
            ],
        ]);

        if ($request->hasHeader('X-Fragment-Request')) {
            $fragment = (string) $request->header('X-Fragment-Name', 'companies-list');
            $allowed = ['companies-kpi', 'companies-list'];

            return $view->fragment(in_array($fragment, $allowed, true) ? $fragment : 'companies-list');
        }

        return $view;
    }

    public function store(StoreCompanyRequest $request): JsonResponse|RedirectResponse
    {
        $company = $this->repository->addCompany($request->validated());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Empresa creada correctamente.',
                'company' => $company,
            ], 201);
        }

        return redirect()->route('companies.index');
    }
}
