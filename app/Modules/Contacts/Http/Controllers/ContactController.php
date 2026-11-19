<?php

namespace App\Modules\Contacts\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Contacts\Http\Requests\StoreContactRequest;
use App\Modules\Core\Support\CrmStateRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(private readonly CrmStateRepository $repository) {}

    public function index(Request $request): View|string
    {
        $state = $this->repository->all();
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $source = (string) $request->query('source', '');
        $owner = (string) $request->query('owner', '');

        $filteredContacts = array_values(array_filter(
            $state['contacts'],
            static function (array $contact) use ($search, $status, $source, $owner): bool {
                if ($search !== '') {
                    $haystack = mb_strtolower($contact['name'].' '.$contact['company_name'].' '.$contact['email'].' '.$contact['role']);
                    if (! str_contains($haystack, mb_strtolower($search))) {
                        return false;
                    }
                }
                if ($status !== '' && $contact['status'] !== $status) {
                    return false;
                }
                if ($source !== '' && $contact['source'] !== $source) {
                    return false;
                }
                if ($owner !== '' && $contact['owner_id'] !== $owner) {
                    return false;
                }

                return true;
            }
        ));

        $qualifiedCount = count(array_filter(
            $state['contacts'],
            static fn (array $c): bool => $c['status'] === 'Calificado'
        ));
        $newCount = count(array_filter(
            $state['contacts'],
            static fn (array $c): bool => $c['status'] === 'Nuevo Lead'
        ));

        $companyOptions = [];
        foreach ($state['companies'] as $company) {
            $companyOptions[$company['id']] = $company['name'];
        }

        $view = view('modules.contacts.index', [
            'contacts' => $filteredContacts,
            'totalContacts' => count($state['contacts']),
            'qualifiedCount' => $qualifiedCount,
            'newCount' => $newCount,
            'companyOptions' => $companyOptions,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'source' => $source,
                'owner' => $owner,
            ],
        ]);

        if ($request->hasHeader('X-Fragment-Request')) {
            $fragment = (string) $request->header('X-Fragment-Name', 'contacts-table');
            $allowed = ['contacts-kpi', 'contacts-table'];

            return $view->fragment(in_array($fragment, $allowed, true) ? $fragment : 'contacts-table');
        }

        return $view;
    }

    public function store(StoreContactRequest $request): JsonResponse|RedirectResponse
    {
        $contact = $this->repository->addContact($request->validated());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Contacto guardado exitosamente.',
                'contact' => $contact,
            ], 201);
        }

        return redirect()->route('contacts.index');
    }
}
