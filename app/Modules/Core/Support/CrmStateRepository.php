<?php

namespace App\Modules\Core\Support;

use App\Modules\Activities\Models\Activity;
use App\Modules\Companies\Models\Company;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Deals\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmStateRepository
{
    private const STAGES = [
        'prospecto' => ['label' => 'Prospecto', 'color' => 'sky', 'default_prob' => 25],
        'propuesta' => ['label' => 'Propuesta Enviada', 'color' => 'amber', 'default_prob' => 55],
        'negociacion' => ['label' => 'Negociación', 'color' => 'primary', 'default_prob' => 75],
        'ganada' => ['label' => 'Ganada', 'color' => 'emerald', 'default_prob' => 100],
        'perdida' => ['label' => 'Perdida', 'color' => 'coral', 'default_prob' => 0],
    ];

    public function all(): array
    {
        $user = auth()->user();
        $companies = CrmAccess::scope(Company::query()->with('owner'), $user)->get();
        $contacts = CrmAccess::scope(Contact::query()->with('owner'), $user)->get();
        $deals = CrmAccess::scope(Deal::query()->with('owner'), $user)->get();
        $activities = CrmAccess::scope(Activity::query()->with('user'), $user, 'user_id')->latest()->get();
        $companiesById = $companies->keyBy('id');
        $contactsById = $contacts->keyBy('id');
        $dealsById = $deals->keyBy('id');
        $openDealsByCompany = $deals->whereNotIn('stage', ['ganada', 'perdida'])->groupBy('company_id');

        return [
            'stages' => self::STAGES,
            'companies' => $companies->map(function (Company $company) use ($openDealsByCompany): array {
                $openDeals = $openDealsByCompany->get($company->id, collect());
                $total = $openDeals->sum('value');
                $weighted = $openDeals->sum(fn (Deal $deal) => (float) $deal->value * $deal->probability / 100);

                return [
                    'id' => (string) $company->id,
                    'name' => $company->name,
                    'tax_id' => $company->tax_id ?? '',
                    'tier' => $company->tier ?? '',
                    'status' => $company->status,
                    'status_variant' => match ($company->status) {
                        'Activa', 'Activa - Tier 1' => 'emerald',
                        'Prospecto' => 'amber',
                        default => 'neutral',
                    },
                    'sector' => $company->sector ?? '',
                    'industry' => $company->industry ?? $company->sector ?? '',
                    'location' => $company->location ?? '',
                    'website' => $company->website ?? '',
                    'phone' => $company->phone ?? '',
                    'owner' => $company->owner?->name ?? '',
                    'owner_id' => (string) $company->owner_id,
                    'deals_total' => $total,
                    'deals_count' => $openDeals->count(),
                    'mrr' => $company->mrr,
                    'mrr_plan' => $company->mrr_plan ?? '',
                    'weighted_prob' => $total > 0 ? round($weighted / $total * 100) : 0,
                    'initial' => Str::upper(Str::substr($company->name, 0, 1)),
                ];
            })->all(),
            'contacts' => $contacts->map(function (Contact $contact) use ($companiesById): array {
                return [
                    'id' => (string) $contact->id,
                    'name' => $contact->name,
                    'role' => $contact->position ?? '',
                    'company_id' => (string) $contact->company_id,
                    'company_name' => $companiesById->get($contact->company_id)?->name ?? '',
                    'email' => $contact->email,
                    'phone' => $contact->phone ?? '',
                    'source' => $contact->lead_source ?? '',
                    'status' => $contact->status,
                    'status_variant' => match ($contact->status) {
                        'Calificado' => 'emerald', 'Nuevo Lead' => 'sky', 'Perdido' => 'coral', default => 'neutral',
                    },
                    'owner' => $contact->owner?->name ?? '',
                    'owner_id' => (string) $contact->owner_id,
                    'initials' => Str::upper(Str::substr($contact->first_name, 0, 1).Str::substr($contact->last_name, 0, 1)),
                    'role_tag' => $contact->role_tag,
                    'notes' => $contact->notes ?? '',
                    'created_at' => $contact->created_at,
                ];
            })->all(),
            'deals' => $deals->map(function (Deal $deal) use ($companiesById): array {
                $ownerName = $deal->owner?->name ?? '';

                return [
                    'id' => (string) $deal->id,
                    'title' => $deal->title,
                    'company_id' => (string) $deal->company_id,
                    'company_name' => $companiesById->get($deal->company_id)?->name ?? '',
                    'amount' => (float) $deal->value,
                    'stage' => $deal->stage,
                    'probability' => $deal->probability,
                    'close_date' => $deal->expected_close_date?->format('d/m/Y') ?? '',
                    'close_date_iso' => $deal->expected_close_date?->format('Y-m-d'),
                    'owner' => Str::upper(Str::substr($ownerName, 0, 1)),
                    'owner_name' => $ownerName,
                    'owner_id' => (string) $deal->owner_id,
                    'badge' => $deal->badge,
                    'badge_variant' => $deal->badge_variant,
                    'alert' => $deal->alert,
                    'critical' => $deal->critical,
                    'lost_reason' => $deal->lost_reason,
                    'created_at' => $deal->created_at,
                ];
            })->all(),
            'activities' => $activities->map(function (Activity $activity) use ($companiesById, $contactsById, $dealsById): array {
                $target = match ($activity->activitable_type) {
                    Company::class => $companiesById->get($activity->activitable_id),
                    Contact::class => $contactsById->get($activity->activitable_id),
                    Deal::class => $dealsById->get($activity->activitable_id),
                    default => null,
                };
                $companyId = $target instanceof Company ? $target->id : $target?->company_id;
                $company = $companiesById->get($companyId);
                $contact = $target instanceof Contact ? $target : ($target instanceof Deal ? $contactsById->get($target->contact_id) : null);

                return [
                    'id' => (string) $activity->id,
                    'type' => $activity->type,
                    'subject' => $activity->subject,
                    'company_id' => (string) ($company?->id ?? ''),
                    'company_name' => $company?->name ?? '',
                    'contact_name' => $contact?->name ?? '',
                    'owner' => $activity->user?->name ?? '',
                    'schedule_label' => $activity->due_date?->format('d/m/Y H:i') ?? '',
                    'due_date' => $activity->due_date,
                    'created_at' => $activity->created_at,
                    'notes' => $activity->description ?? '',
                    'completed' => $activity->completed_at !== null,
                    'priority' => $activity->priority,
                    'amount_label' => $target instanceof Deal ? '$'.number_format((float) $target->value, 2).' USD' : null,
                ];
            })->all(),
        ];
    }

    public function addCompany(array $data): array
    {
        $company = Company::create([
            'name' => $data['name'], 'tax_id' => $data['tax_id'] ?? null, 'sector' => $data['sector'],
            'status' => $data['status'], 'location' => $data['location'], 'website' => $data['website'] ?? null,
            'phone' => $data['phone'] ?? null, 'owner_id' => $data['owner'], 'mrr' => $data['mrr'] ?? 0,
        ]);

        return $this->find($this->all()['companies'], $company->id);
    }

    public function addContact(array $data): array
    {
        $contact = Contact::create([
            'first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'email' => $data['email'],
            'phone' => $data['phone'] ?? null, 'position' => $data['role'], 'company_id' => $data['company_name'],
            'lead_source' => $data['source'], 'status' => $data['status'], 'notes' => $data['notes'] ?? null,
            'owner_id' => $data['owner'] ?? auth()->id(),
        ]);

        return $this->find($this->all()['contacts'], $contact->id);
    }

    public function addDeal(array $data): array
    {
        $deal = Deal::create([
            'title' => $data['title'], 'company_id' => $data['company_name'], 'value' => $data['amount'],
            'stage' => $data['stage'], 'probability' => self::STAGES[$data['stage']]['default_prob'],
            'expected_close_date' => $data['close_date'], 'owner_id' => $data['owner_name'],
        ]);

        return $this->find($this->all()['deals'], $deal->id);
    }

    public function addActivity(array $data): array
    {
        $target = empty($data['contact_name'])
            ? Company::findOrFail($data['company_name'])
            : Contact::findOrFail($data['contact_name']);
        $activity = new Activity([
            'type' => $data['type'], 'subject' => $data['subject'], 'description' => $data['notes'] ?? null,
            'due_date' => $data['schedule_label'] ?? now(), 'completed_at' => ! empty($data['completed']) ? now() : null,
            'user_id' => $data['owner'] ?? auth()->id(),
        ]);
        $activity->activitable()->associate($target);
        $activity->save();

        return $this->find($this->all()['activities'], $activity->id);
    }

    public function updateDealStage(string $id, string $stage): ?array
    {
        $deal = CrmAccess::scope(Deal::query(), auth()->user())->find($id);
        if (! $deal || ! isset(self::STAGES[$stage])) {
            return null;
        }
        if ($deal->stage !== $stage) {
            DB::transaction(function () use ($deal, $stage): void {
                $deal->update(['stage' => $stage, 'probability' => self::STAGES[$stage]['default_prob']]);
                $activity = new Activity([
                    'type' => 'nota',
                    'subject' => 'Oportunidad movida a '.self::STAGES[$stage]['label'],
                    'description' => $deal->title,
                    'due_date' => now(),
                    'completed_at' => now(),
                    'user_id' => auth()->id(),
                ]);
                $activity->activitable()->associate($deal);
                $activity->save();
            });
        }

        return $this->find($this->all()['deals'], $deal->id);
    }

    public function toggleActivity(string $id): ?array
    {
        $activity = CrmAccess::scope(Activity::query(), auth()->user(), 'user_id')->find($id);
        if (! $activity) {
            return null;
        }
        $activity->update(['completed_at' => $activity->completed_at ? null : now()]);

        return $this->find($this->all()['activities'], $activity->id);
    }

    private function find(array $records, int $id): array
    {
        return collect($records)->firstOrFail(fn (array $record) => $record['id'] === (string) $id);
    }
}
