<?php

namespace App\Modules\Core\Support;

use Illuminate\Support\Str;

class CrmStateRepository
{
    private const SESSION_KEY = 'crm_state';

    /**
     * @return array{
     *     companies: array<int, array<string, mixed>>,
     *     contacts: array<int, array<string, mixed>>,
     *     deals: array<int, array<string, mixed>>,
     *     activities: array<int, array<string, mixed>>,
     *     stages: array<string, array{label: string, color: string, default_prob: int}>
     * }
     */
    public function all(): array
    {
        $state = session()->get(self::SESSION_KEY);

        if (! is_array($state) || ! isset($state['companies'], $state['contacts'], $state['deals'], $state['activities'])) {
            $state = $this->seedState();
            session()->put(self::SESSION_KEY, $state);
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function addCompany(array $data): array
    {
        $state = $this->all();
        $name = (string) $data['name'];

        $company = [
            'id' => 'comp-'.Str::random(6),
            'name' => $name,
            'tax_id' => (string) ($data['tax_id'] ?: 'B-'.random_int(10000000, 99999999)),
            'tier' => (string) ($data['tier'] ?: 'Cuenta Nueva'),
            'status' => (string) ($data['status'] ?: 'Activa'),
            'status_variant' => match ($data['status'] ?? 'Activa') {
                'Activa - Tier 1', 'Activa' => 'emerald',
                'Prospecto' => 'amber',
                'Inactiva' => 'coral',
                default => 'sky',
            },
            'sector' => (string) ($data['sector'] ?: 'SaaS'),
            'industry' => (string) ($data['industry'] ?: ($data['sector'] ?: 'Tecnología B2B')),
            'location' => (string) ($data['location'] ?: 'Madrid, España'),
            'website' => (string) ($data['website'] ?: Str::slug($name).'.io'),
            'phone' => (string) ($data['phone'] ?: '+34 910 000 000'),
            'owner' => (string) ($data['owner'] ?: 'Sofía Méndez'),
            'deals_total' => 0,
            'deals_count' => 0,
            'mrr' => (int) ($data['mrr'] ?? 0),
            'mrr_plan' => 'Plan Inicial',
            'weighted_prob' => 50,
            'initial' => Str::upper(Str::substr($name, 0, 1)),
        ];

        array_unshift($state['companies'], $company);
        session()->put(self::SESSION_KEY, $state);

        return $company;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function addContact(array $data): array
    {
        $state = $this->all();
        $firstName = trim((string) $data['first_name']);
        $lastName = trim((string) $data['last_name']);
        $fullName = trim("{$firstName} {$lastName}");
        $initials = Str::upper(Str::substr($firstName, 0, 1).Str::substr($lastName !== '' ? $lastName : $firstName, 0, 1));

        $status = (string) ($data['status'] ?: 'Nuevo Lead');
        $statusVariant = match ($status) {
            'Calificado' => 'emerald',
            'Nuevo Lead' => 'sky',
            'Perdido' => 'coral',
            default => 'neutral',
        };

        $contact = [
            'id' => 'cont-'.Str::random(6),
            'name' => $fullName,
            'role' => (string) ($data['role'] ?: 'Contacto Comercial'),
            'company_name' => (string) ($data['company_name'] ?: 'NovaTech Solutions S.A.'),
            'email' => (string) $data['email'],
            'phone' => (string) ($data['phone'] ?: '+34 600 000 000'),
            'source' => (string) ($data['source'] ?: 'Web Inbound'),
            'status' => $status,
            'status_variant' => $statusVariant,
            'owner' => (string) ($data['owner'] ?: 'Sofía Méndez'),
            'initials' => $initials,
            'role_tag' => null,
            'notes' => (string) ($data['notes'] ?? ''),
        ];

        array_unshift($state['contacts'], $contact);
        session()->put(self::SESSION_KEY, $state);

        return $contact;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function addDeal(array $data): array
    {
        $state = $this->all();
        $stage = (string) ($data['stage'] ?: 'prospecto');
        $stageMeta = $state['stages'][$stage] ?? $state['stages']['prospecto'];
        $ownerName = (string) ($data['owner_name'] ?: 'Sofía Méndez');
        $parts = preg_split('/\s+/', trim($ownerName)) ?: ['S', 'M'];
        $ownerInitials = Str::upper(Str::substr($parts[0], 0, 1).Str::substr($parts[1] ?? $parts[0], 0, 1));

        $deal = [
            'id' => 'deal-'.Str::random(6),
            'title' => (string) $data['title'],
            'company_name' => (string) $data['company_name'],
            'amount' => (int) $data['amount'],
            'stage' => $stage,
            'probability' => (int) ($data['probability'] ?? $stageMeta['default_prob']),
            'close_date' => (string) ($data['close_date'] ?: '30 Jun 2025'),
            'owner' => $ownerInitials,
            'owner_name' => $ownerName,
            'badge' => null,
            'badge_variant' => null,
            'alert' => null,
            'critical' => false,
            'lost_reason' => null,
            'closing_soon' => in_array($stage, ['propuesta', 'negociacion'], true),
        ];

        array_unshift($state['deals'], $deal);
        session()->put(self::SESSION_KEY, $state);

        return $deal;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function updateDealStage(string $dealId, string $newStage): ?array
    {
        $state = $this->all();
        if (! isset($state['stages'][$newStage])) {
            return null;
        }

        $updatedDeal = null;
        foreach ($state['deals'] as &$deal) {
            if ($deal['id'] === $dealId) {
                $deal['stage'] = $newStage;
                $deal['probability'] = $state['stages'][$newStage]['default_prob'];
                $deal['closing_soon'] = in_array($newStage, ['propuesta', 'negociacion'], true);
                $updatedDeal = $deal;
                break;
            }
        }
        unset($deal);

        if ($updatedDeal !== null) {
            array_unshift($state['activities'], [
                'id' => 'act-'.Str::random(6),
                'type' => 'nota',
                'subject' => 'Oportunidad movida a '.$state['stages'][$newStage]['label'],
                'company_name' => $updatedDeal['company_name'],
                'contact_name' => $updatedDeal['title'],
                'owner' => 'Sofía Méndez',
                'schedule_label' => 'Ahora',
                'notes' => 'Cambio de etapa en el tablero Kanban ($'.number_format((int) $updatedDeal['amount']).' USD).',
                'completed' => true,
                'priority' => 'Normal',
                'time_group' => 'hoy',
                'amount_label' => '$'.number_format((int) $updatedDeal['amount']).' USD',
            ]);
            session()->put(self::SESSION_KEY, $state);
        }

        return $updatedDeal;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function addActivity(array $data): array
    {
        $state = $this->all();
        $completed = filter_var($data['completed'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $activity = [
            'id' => 'act-'.Str::random(6),
            'type' => (string) ($data['type'] ?: 'llamada'),
            'subject' => (string) $data['subject'],
            'company_name' => (string) ($data['company_name'] ?: 'NovaTech Solutions S.A.'),
            'contact_name' => (string) ($data['contact_name'] ?: 'Elena Sanz'),
            'owner' => (string) ($data['owner'] ?: 'Sofía Méndez'),
            'schedule_label' => (string) ($data['schedule_label'] ?: 'Hoy, 17:30'),
            'notes' => (string) ($data['notes'] ?? ''),
            'completed' => $completed,
            'priority' => (string) ($data['priority'] ?: 'Seguimiento'),
            'time_group' => $completed ? 'completada' : 'hoy',
            'amount_label' => null,
        ];

        array_unshift($state['activities'], $activity);
        session()->put(self::SESSION_KEY, $state);

        return $activity;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function toggleActivity(string $activityId): ?array
    {
        $state = $this->all();
        $updated = null;

        foreach ($state['activities'] as &$activity) {
            if ($activity['id'] === $activityId) {
                $activity['completed'] = ! $activity['completed'];
                $activity['time_group'] = $activity['completed'] ? 'completada' : 'hoy';
                $updated = $activity;
                break;
            }
        }
        unset($activity);

        if ($updated !== null) {
            session()->put(self::SESSION_KEY, $state);
        }

        return $updated;
    }

    /**
     * @return array<string, mixed>
     */
    public function computePipelineStats(): array
    {
        $state = $this->all();
        $deals = $state['deals'];

        $openDeals = array_filter($deals, static fn (array $d): bool => ! in_array($d['stage'], ['ganada', 'perdida'], true));
        $wonDeals = array_filter($deals, static fn (array $d): bool => $d['stage'] === 'ganada');
        $closedDeals = array_filter($deals, static fn (array $d): bool => in_array($d['stage'], ['ganada', 'perdida'], true));

        $openValue = array_sum(array_column($openDeals, 'amount'));
        $weightedValue = 0;
        foreach ($openDeals as $deal) {
            $weightedValue += (int) round(((int) $deal['amount']) * (((int) $deal['probability']) / 100));
        }

        $conversionRate = count($closedDeals) > 0
            ? round((count($wonDeals) / count($deals)) * 100, 1)
            : 28.5;

        return [
            'open_value' => $openValue,
            'weighted_value' => $weightedValue,
            'total_deals' => count($deals),
            'open_deals_count' => count($openDeals),
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seedState(): array
    {
        return [
            'stages' => [
                'prospecto' => ['label' => 'Prospecto', 'color' => 'sky', 'default_prob' => 25],
                'propuesta' => ['label' => 'Propuesta Enviada', 'color' => 'amber', 'default_prob' => 55],
                'negociacion' => ['label' => 'Negociación', 'color' => 'primary', 'default_prob' => 75],
                'ganada' => ['label' => 'Ganada', 'color' => 'emerald', 'default_prob' => 100],
                'perdida' => ['label' => 'Perdida', 'color' => 'coral', 'default_prob' => 0],
            ],
            'companies' => [
                [
                    'id' => 'comp-1',
                    'name' => 'NovaTech Solutions S.A.',
                    'tax_id' => 'B-87654321',
                    'tier' => 'Cuenta Clave',
                    'status' => 'Activa - Tier 1',
                    'status_variant' => 'emerald',
                    'sector' => 'SaaS',
                    'industry' => 'SaaS B2B Cloud',
                    'location' => 'Madrid, España',
                    'website' => 'novatech-solutions.io',
                    'phone' => '+34 910 234 567',
                    'owner' => 'Sofía Méndez',
                    'deals_total' => 86500,
                    'deals_count' => 2,
                    'mrr' => 4200,
                    'mrr_plan' => 'Suscripción Enterprise L3',
                    'weighted_prob' => 70,
                    'initial' => 'N',
                ],
                [
                    'id' => 'comp-2',
                    'name' => 'FinanSmart Corp',
                    'tax_id' => 'B-54321987',
                    'tier' => 'Enterprise',
                    'status' => 'Activa - Tier 1',
                    'status_variant' => 'emerald',
                    'sector' => 'FinTech',
                    'industry' => 'Banca & FinOps',
                    'location' => 'Barcelona, España',
                    'website' => 'finansmart.es',
                    'phone' => '+34 934 881 290',
                    'owner' => 'Sofía Méndez',
                    'deals_total' => 83500,
                    'deals_count' => 2,
                    'mrr' => 5800,
                    'mrr_plan' => 'Licencia Corporativa',
                    'weighted_prob' => 80,
                    'initial' => 'F',
                ],
                [
                    'id' => 'comp-3',
                    'name' => 'TechCorp Latam',
                    'tax_id' => 'B-99887766',
                    'tier' => 'Estratégica',
                    'status' => 'Activa',
                    'status_variant' => 'emerald',
                    'sector' => 'SaaS',
                    'industry' => 'Ciberseguridad Cloud',
                    'location' => 'Ciudad de México',
                    'website' => 'techcorp.lat',
                    'phone' => '+52 55 4123 8900',
                    'owner' => 'Sofía Méndez',
                    'deals_total' => 28000,
                    'deals_count' => 1,
                    'mrr' => 2400,
                    'mrr_plan' => 'Suite Endpoint',
                    'weighted_prob' => 90,
                    'initial' => 'T',
                ],
                [
                    'id' => 'comp-4',
                    'name' => 'ACME Industrial',
                    'tax_id' => 'B-11223344',
                    'tier' => 'Crecimiento',
                    'status' => 'Activa',
                    'status_variant' => 'sky',
                    'sector' => 'Logística',
                    'industry' => 'ERP & Logística',
                    'location' => 'Valencia, España',
                    'website' => 'acme-ind.es',
                    'phone' => '+34 960 112 334',
                    'owner' => 'Diego Cruz',
                    'deals_total' => 29500,
                    'deals_count' => 1,
                    'mrr' => 1900,
                    'mrr_plan' => 'Plan Growth',
                    'weighted_prob' => 65,
                    'initial' => 'A',
                ],
                [
                    'id' => 'comp-5',
                    'name' => 'DataDrive Latam',
                    'tax_id' => 'B-66778899',
                    'tier' => 'Prospecto',
                    'status' => 'Prospecto',
                    'status_variant' => 'amber',
                    'sector' => 'SaaS',
                    'industry' => 'Analytics & BI',
                    'location' => 'Bogotá, Colombia',
                    'website' => 'datadrive.co',
                    'phone' => '+57 1 555 0192',
                    'owner' => 'Carlos Vega',
                    'deals_total' => 12500,
                    'deals_count' => 1,
                    'mrr' => 0,
                    'mrr_plan' => 'Evaluación PoC',
                    'weighted_prob' => 45,
                    'initial' => 'D',
                ],
                [
                    'id' => 'comp-6',
                    'name' => 'CloudScale Systems',
                    'tax_id' => 'B-33445566',
                    'tier' => 'Cuenta Clave',
                    'status' => 'Activa',
                    'status_variant' => 'emerald',
                    'sector' => 'SaaS',
                    'industry' => 'Infraestructura Cloud',
                    'location' => 'Madrid, España',
                    'website' => 'cloudscale.io',
                    'phone' => '+34 910 223 881',
                    'owner' => 'Carlos Vega',
                    'deals_total' => 34000,
                    'deals_count' => 1,
                    'mrr' => 3100,
                    'mrr_plan' => 'Plan Scale',
                    'weighted_prob' => 75,
                    'initial' => 'C',
                ],
            ],
            'contacts' => [
                [
                    'id' => 'cont-1',
                    'name' => 'Mateo Silva',
                    'role' => 'VP de Operaciones Globales',
                    'company_name' => 'CloudScale Systems',
                    'email' => 'mateo.silva@cloudscale.io',
                    'phone' => '+34 910 223 881',
                    'source' => 'Web Orgánico',
                    'status' => 'Calificado',
                    'status_variant' => 'emerald',
                    'owner' => 'Carlos Vega',
                    'initials' => 'MS',
                    'role_tag' => 'Decisor',
                    'notes' => 'Interesado en despliegue multi-región.',
                ],
                [
                    'id' => 'cont-2',
                    'name' => 'Camila Ortiz',
                    'role' => 'Directora de Infraestructura TI',
                    'company_name' => 'Nexus Logistics',
                    'email' => 'c.ortiz@nexuslog.com',
                    'phone' => '+34 602 119 402',
                    'source' => 'Referido Socio',
                    'status' => 'Nuevo Lead',
                    'status_variant' => 'sky',
                    'owner' => 'Sofía Méndez',
                    'initials' => 'CO',
                    'role_tag' => null,
                    'notes' => 'Solicitó demo de trazabilidad.',
                ],
                [
                    'id' => 'cont-3',
                    'name' => 'Javier Roldán',
                    'role' => 'CFO & Head de Compras',
                    'company_name' => 'FinanSmart Corp',
                    'email' => 'roldan@finansmart.es',
                    'phone' => '+34 934 881 290',
                    'source' => 'Evento Tech Summit',
                    'status' => 'Contactado',
                    'status_variant' => 'neutral',
                    'owner' => 'Sofía Méndez',
                    'initials' => 'JR',
                    'role_tag' => 'Compras',
                    'notes' => 'Revisando propuesta FinOps.',
                ],
                [
                    'id' => 'cont-4',
                    'name' => 'Elena Valdés',
                    'role' => 'Chief Revenue Officer',
                    'company_name' => 'HyperGrowth SaaS',
                    'email' => 'evaldes@hypergrowth.co',
                    'phone' => '+34 689 332 119',
                    'source' => 'Llamada en Frío',
                    'status' => 'Perdido',
                    'status_variant' => 'coral',
                    'owner' => 'Diego Cruz',
                    'initials' => 'EV',
                    'role_tag' => null,
                    'notes' => 'Pospuesto hasta Q1.',
                ],
                [
                    'id' => 'cont-5',
                    'name' => 'Diego Pardo',
                    'role' => 'Lead Product Manager',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'email' => 'dpardo@novatech-solutions.io',
                    'phone' => '+34 914 901 024',
                    'source' => 'Web Inbound',
                    'status' => 'Calificado',
                    'status_variant' => 'emerald',
                    'owner' => 'Carlos Vega',
                    'initials' => 'DP',
                    'role_tag' => 'Producto',
                    'notes' => 'Evaluando API y webhooks.',
                ],
                [
                    'id' => 'cont-6',
                    'name' => 'Sara Garmendia',
                    'role' => 'Directora de Expansión LATAM',
                    'company_name' => 'TransGlobal Corp',
                    'email' => 's.garmendia@transglobal.net',
                    'phone' => '+34 655 491 802',
                    'source' => 'Evento Mobile Expo',
                    'status' => 'Contactado',
                    'status_variant' => 'neutral',
                    'owner' => 'Sofía Méndez',
                    'initials' => 'SG',
                    'role_tag' => null,
                    'notes' => 'Pendiente agendar llamada técnica.',
                ],
                [
                    'id' => 'cont-7',
                    'name' => 'Elena Sanz',
                    'role' => 'Directora de Operaciones (COO)',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'email' => 'elena.sanz@novatech-solutions.io',
                    'phone' => '+34 910 234 567',
                    'source' => 'Referido Socio',
                    'status' => 'Calificado',
                    'status_variant' => 'emerald',
                    'owner' => 'Sofía Méndez',
                    'initials' => 'ES',
                    'role_tag' => 'Decisor',
                    'notes' => 'Interlocutor principal en NovaTech.',
                ],
                [
                    'id' => 'cont-8',
                    'name' => 'Carlos Morales',
                    'role' => 'VP de Infraestructura Cloud',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'email' => 'carlos.morales@novatech-solutions.io',
                    'phone' => '+34 910 234 568',
                    'source' => 'Web Inbound',
                    'status' => 'Calificado',
                    'status_variant' => 'emerald',
                    'owner' => 'Sofía Méndez',
                    'initials' => 'CM',
                    'role_tag' => 'Técnico',
                    'notes' => 'Responsable de validación ISO 27001.',
                ],
                [
                    'id' => 'cont-9',
                    'name' => 'Laura Gallego',
                    'role' => 'Responsable de Compras & Legal',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'email' => 'laura.gallego@novatech-solutions.io',
                    'phone' => '+34 910 234 569',
                    'source' => 'Web Inbound',
                    'status' => 'Contactado',
                    'status_variant' => 'neutral',
                    'owner' => 'Sofía Méndez',
                    'initials' => 'LG',
                    'role_tag' => 'Legal',
                    'notes' => 'Validación jurídica de DPA y SLA.',
                ],
            ],
            'deals' => [
                [
                    'id' => 'deal-1',
                    'title' => 'Licencias Aura Starter',
                    'company_name' => 'Kroma Design Lab',
                    'amount' => 6000,
                    'stage' => 'prospecto',
                    'probability' => 20,
                    'close_date' => '15 Jun 2025',
                    'owner' => 'SM',
                    'owner_name' => 'Sofía Méndez',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-2',
                    'title' => 'Consultoría CRM inicial',
                    'company_name' => 'LogistiX Express',
                    'amount' => 14000,
                    'stage' => 'prospecto',
                    'probability' => 30,
                    'close_date' => '22 Jun 2025',
                    'owner' => 'CV',
                    'owner_name' => 'Carlos Vega',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-3',
                    'title' => 'Licenciamiento BI & Dashboards',
                    'company_name' => 'DataDrive Latam',
                    'amount' => 12500,
                    'stage' => 'prospecto',
                    'probability' => 45,
                    'close_date' => '08 Nov 2025',
                    'owner' => 'CV',
                    'owner_name' => 'Carlos Vega',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => true,
                ],
                [
                    'id' => 'deal-4',
                    'title' => 'Implementación Enterprise Cloud',
                    'company_name' => 'FinanSmart Corp',
                    'amount' => 38500,
                    'stage' => 'propuesta',
                    'probability' => 50,
                    'close_date' => '28 Jun 2025',
                    'owner' => 'SM',
                    'owner_name' => 'Sofía Méndez',
                    'badge' => 'Alta Prioridad',
                    'badge_variant' => 'coral',
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-5',
                    'title' => 'Soporte 24/7 SLA Oro',
                    'company_name' => 'BioHealth Spain',
                    'amount' => 18000,
                    'stage' => 'propuesta',
                    'probability' => 60,
                    'close_date' => '30 Jun 2025',
                    'owner' => 'SM',
                    'owner_name' => 'Sofía Méndez',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-6',
                    'title' => 'Migración Cloud Data Lake',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'amount' => 52000,
                    'stage' => 'propuesta',
                    'probability' => 70,
                    'close_date' => '30 Oct 2025',
                    'owner' => 'CV',
                    'owner_name' => 'Carlos Vega',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => true,
                ],
                [
                    'id' => 'deal-7',
                    'title' => 'Expansión Regional Latam',
                    'company_name' => 'RetailNext Corp',
                    'amount' => 65000,
                    'stage' => 'negociacion',
                    'probability' => 75,
                    'close_date' => '18 Jun 2025',
                    'owner' => 'DC',
                    'owner_name' => 'Diego Cruz',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => 'Revisión legal pendiente',
                    'critical' => true,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-8',
                    'title' => 'Plataforma Omnicanal',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'amount' => 47000,
                    'stage' => 'negociacion',
                    'probability' => 80,
                    'close_date' => '20 Jun 2025',
                    'owner' => 'SM',
                    'owner_name' => 'Sofía Méndez',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-9',
                    'title' => 'Expansión Servidores FinOps',
                    'company_name' => 'FinanSmart Corp',
                    'amount' => 45000,
                    'stage' => 'negociacion',
                    'probability' => 85,
                    'close_date' => '28 Oct 2025',
                    'owner' => 'SM',
                    'owner_name' => 'Sofía Méndez',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => true,
                ],
                [
                    'id' => 'deal-10',
                    'title' => 'Renovación Anual ERP Logística',
                    'company_name' => 'ACME Industrial',
                    'amount' => 29500,
                    'stage' => 'negociacion',
                    'probability' => 65,
                    'close_date' => '03 Nov 2025',
                    'owner' => 'DC',
                    'owner_name' => 'Diego Cruz',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => true,
                ],
                [
                    'id' => 'deal-11',
                    'title' => 'Suite Ciberseguridad Endpoint',
                    'company_name' => 'TechCorp Latam',
                    'amount' => 28000,
                    'stage' => 'negociacion',
                    'probability' => 90,
                    'close_date' => '31 Oct 2025',
                    'owner' => 'SM',
                    'owner_name' => 'Sofía Méndez',
                    'badge' => null,
                    'badge_variant' => null,
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => true,
                ],
                [
                    'id' => 'deal-12',
                    'title' => 'Renovación Anual Q2',
                    'company_name' => 'AeroLogistics',
                    'amount' => 52500,
                    'stage' => 'ganada',
                    'probability' => 100,
                    'close_date' => '02 Jun 2025',
                    'owner' => 'CV',
                    'owner_name' => 'Carlos Vega',
                    'badge' => '100% Cerrado',
                    'badge_variant' => 'emerald',
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-13',
                    'title' => 'Upgrade Plan Growth',
                    'company_name' => 'UrbanDelivery',
                    'amount' => 40000,
                    'stage' => 'ganada',
                    'probability' => 100,
                    'close_date' => '26 May 2025',
                    'owner' => 'SM',
                    'owner_name' => 'Sofía Méndez',
                    'badge' => '100% Ganado',
                    'badge_variant' => 'emerald',
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => null,
                    'closing_soon' => false,
                ],
                [
                    'id' => 'deal-14',
                    'title' => 'Migración Legacy',
                    'company_name' => 'GlobalRetail Group',
                    'amount' => 15000,
                    'stage' => 'perdida',
                    'probability' => 0,
                    'close_date' => '28 May 2025',
                    'owner' => 'CV',
                    'owner_name' => 'Carlos Vega',
                    'badge' => 'Perdida',
                    'badge_variant' => 'coral',
                    'alert' => null,
                    'critical' => false,
                    'lost_reason' => 'Presupuesto congelado',
                    'closing_soon' => false,
                ],
            ],
            'activities' => [
                [
                    'id' => 'act-1',
                    'type' => 'reunion',
                    'subject' => 'Demo comercial con VP de TechCorp',
                    'company_name' => 'TechCorp Latam',
                    'contact_name' => 'Martín Henao (VP de IT)',
                    'owner' => 'Sofía Méndez',
                    'schedule_label' => '10:30 AM',
                    'notes' => 'Presentación de arquitectura y roadmap de seguridad.',
                    'completed' => false,
                    'priority' => 'Alta Prioridad',
                    'time_group' => 'hoy',
                    'amount_label' => '$28,000 USD',
                ],
                [
                    'id' => 'act-2',
                    'type' => 'llamada',
                    'subject' => 'Llamada de seguimiento propuesta ACME',
                    'company_name' => 'ACME Industrial',
                    'contact_name' => 'Lucía Valdés',
                    'owner' => 'Diego Cruz',
                    'schedule_label' => '14:00 PM',
                    'notes' => 'Revisar requerimientos de integración logística.',
                    'completed' => false,
                    'priority' => 'Seguimiento',
                    'time_group' => 'hoy',
                    'amount_label' => '$29,500 USD',
                ],
                [
                    'id' => 'act-3',
                    'type' => 'correo',
                    'subject' => 'Enviar contrato revisado a NovaLab',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'contact_name' => 'Elena Sanz',
                    'owner' => 'Sofía Méndez',
                    'schedule_label' => '16:30 PM',
                    'notes' => 'Acuerdo sobre términos ISO 27001 y anexo DPA.',
                    'completed' => false,
                    'priority' => 'Cierre',
                    'time_group' => 'hoy',
                    'amount_label' => '$47,000 USD',
                ],
                [
                    'id' => 'act-4',
                    'type' => 'llamada',
                    'subject' => 'Llamada de prospección inicial con CTO de FinanSmart',
                    'company_name' => 'FinanSmart Corp',
                    'contact_name' => 'David Ruiz (CTO)',
                    'owner' => 'Carlos Vega',
                    'schedule_label' => '11:30 AM',
                    'notes' => 'Revisar requerimientos de API bancaria.',
                    'completed' => false,
                    'priority' => 'Prospección',
                    'time_group' => 'hoy',
                    'amount_label' => '$38,500 USD',
                ],
                [
                    'id' => 'act-5',
                    'type' => 'tarea',
                    'subject' => 'Revisión de pipeline trimestral con Sofía',
                    'company_name' => 'Aura Workspace',
                    'contact_name' => 'Equipo Comercial',
                    'owner' => 'Sofía Méndez',
                    'schedule_label' => '09:00 AM',
                    'notes' => 'Finalizado a las 09:42 AM.',
                    'completed' => true,
                    'priority' => 'Interna',
                    'time_group' => 'completada',
                    'amount_label' => null,
                ],
                [
                    'id' => 'act-6',
                    'type' => 'correo',
                    'subject' => 'Envío de acuerdo de confidencialidad bilateral (NDA)',
                    'company_name' => 'NovaTech Solutions S.A.',
                    'contact_name' => 'Laura Gallego',
                    'owner' => 'Sofía Méndez',
                    'schedule_label' => 'Ayer, 18:20',
                    'notes' => 'Documento firmado en DocuSign.',
                    'completed' => true,
                    'priority' => 'Legal',
                    'time_group' => 'completada',
                    'amount_label' => null,
                ],
            ],
        ];
    }
}
