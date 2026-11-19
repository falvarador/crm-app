<?php

use App\Models\User;
use App\Modules\Activities\Models\Activity;
use App\Modules\Companies\Models\Company;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Deals\Models\Deal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->company = Company::create([
        'name' => 'Empresa de prueba', 'owner_id' => auth()->id(),
        'sector' => 'SaaS', 'status' => 'Prospecto', 'location' => 'Madrid',
    ]);
    $this->contact = Contact::create([
        'first_name' => 'Mateo', 'last_name' => 'Silva', 'email' => 'mateo@example.test',
        'company_id' => $this->company->id, 'owner_id' => auth()->id(), 'status' => 'Nuevo Lead',
    ]);
    Deal::create([
        'title' => 'Expansión Regional Latam', 'company_id' => $this->company->id,
        'owner_id' => auth()->id(), 'value' => 1000, 'stage' => 'prospecto', 'probability' => 25,
    ]);
    $activity = new Activity([
        'type' => 'llamada', 'subject' => 'Demo comercial con VP de TechCorp',
        'user_id' => auth()->id(), 'due_date' => now(),
    ]);
    $activity->activitable()->associate($this->company);
    $activity->save();
});

test('crm modules render with light dom web components and without tailwind', function (string $uri, string $expectedTitle, array $expectedComponents) {
    $response = $this->get($uri);

    $response->assertOk();
    $response->assertSee($expectedTitle);
    $response->assertSee('<crm-toast', false);
    $response->assertSee('<crm-dialog', false);
    $response->assertDontSee('cdn.tailwindcss.com', false);

    foreach ($expectedComponents as $component) {
        $response->assertSee($component, false);
    }
})->with([
    'home dashboard' => ['/', 'Dashboard Ejecutivo', ['<crm-html-ajax', '<crm-form-ajax', '<crm-form-validate']],
    'dashboard route' => ['/dashboard', 'Dashboard Ejecutivo', ['<crm-html-ajax', '<crm-form-ajax', '<crm-form-validate']],
    'companies' => ['/companies', 'Empresas y Cuentas Comerciales', ['<crm-html-ajax', '<crm-form-ajax', '<crm-form-validate']],
    'contacts' => ['/contacts', 'Contactos y Prospectos', ['<crm-html-ajax', '<crm-form-ajax', '<crm-form-validate']],
    'deals' => ['/deals', 'Pipeline de Ventas', ['<crm-html-ajax', '<crm-kanban', '<crm-form-ajax']],
    'activities' => ['/activities', 'Actividades y Tareas Comerciales', ['<crm-html-ajax', '<crm-form-ajax', '<crm-form-validate']],
]);

test('hybrid ssr endpoints return only requested blade fragment when X-Fragment-Request header is present', function (string $uri, string $fragment, string $expectedContent) {
    $response = $this->withHeaders([
        'X-Fragment-Request' => 'true',
        'X-Fragment-Name' => $fragment,
    ])->get($uri);

    $response->assertOk();
    $response->assertDontSee('<!DOCTYPE html>', false);
    $response->assertSee($expectedContent);
})->with([
    'dashboard kpis' => ['/dashboard', 'dashboard-kpis', 'Pipeline abierto creado en el período'],
    'dashboard closing deals' => ['/dashboard', 'dashboard-closing-deals', 'Oportunidades Próximas a Cerrar'],
    'companies list' => ['/companies', 'companies-list', 'Empresa de prueba'],
    'contacts table' => ['/contacts', 'contacts-table', 'Mateo Silva'],
    'deals board' => ['/deals', 'deals-board', 'Expansión Regional Latam'],
    'activities feed' => ['/activities', 'activities-feed', 'Demo comercial con VP de TechCorp'],
]);

test('companies store validates and persists new company', function () {
    $invalidResponse = $this->postJson('/companies', []);
    $invalidResponse->assertStatus(422)->assertJsonValidationErrors(['name', 'sector', 'status', 'location', 'owner']);

    $validResponse = $this->postJson('/companies', [
        'name' => 'Aether Cloud S.L.',
        'tax_id' => 'B-99112233',
        'sector' => 'SaaS',
        'status' => 'Activa',
        'location' => 'Madrid, España',
        'website' => 'aethercloud.io',
        'phone' => '+34 911 222 333',
        'owner' => auth()->id(),
        'mrr' => 2500,
    ]);

    $validResponse->assertCreated()->assertJsonPath('status', 'ok');
    $this->get('/companies')->assertSee('Aether Cloud S.L.');
});

test('contacts store validates email format and persists new contact', function () {
    $invalidResponse = $this->postJson('/contacts', [
        'first_name' => 'Alejandro',
        'last_name' => 'Gómez',
        'email' => 'correo-invalido',
        'role' => 'Head of Growth',
        'company_name' => $this->company->id,
        'source' => 'Web Inbound',
        'status' => 'Nuevo Lead',
    ]);

    $invalidResponse->assertStatus(422)->assertJsonValidationErrors(['email']);

    $validResponse = $this->postJson('/contacts', [
        'first_name' => 'Alejandro',
        'last_name' => 'Gómez',
        'email' => 'alejandro.gomez@novatech-solutions.io',
        'phone' => '+34 612 345 678',
        'role' => 'Head of Growth',
        'company_name' => $this->company->id,
        'source' => 'Web Inbound',
        'status' => 'Calificado',
    ]);

    $validResponse->assertCreated()->assertJsonPath('status', 'ok');
    $this->get('/contacts')->assertSee('Alejandro Gómez');
});

test('deals store and kanban stage patch update pipeline state', function () {
    $createResponse = $this->postJson('/deals', [
        'title' => 'Expansión CRM Global',
        'company_name' => $this->company->id,
        'amount' => 45000,
        'stage' => 'prospecto',
        'close_date' => '2025-12-15',
        'owner_name' => auth()->id(),
    ]);

    $createResponse->assertCreated()->assertJsonPath('status', 'ok');
    $dealId = $createResponse->json('deal.id');

    $patchResponse = $this->patchJson("/deals/{$dealId}/stage", [
        'stage' => 'negociacion',
    ]);

    $patchResponse->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('deal.stage', 'negociacion')
        ->assertJsonPath('deal.probability', 75);
});

test('activities store and toggle update task completion', function () {
    $createResponse = $this->postJson('/activities', [
        'type' => 'llamada',
        'subject' => 'Llamada de revisión contractual',
        'company_name' => $this->company->id,
        'contact_name' => $this->contact->id,
        'schedule_label' => '2025-12-15T18:00',
        'notes' => 'Confirmar términos de pago.',
    ]);

    $createResponse->assertCreated()->assertJsonPath('status', 'ok');
    $activityId = $createResponse->json('activity.id');

    $toggleResponse = $this->patchJson("/activities/{$activityId}/toggle");
    $toggleResponse->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('activity.completed', true);
});

test('records persist across sessions and are isolated by assignee', function () {
    $seller = auth()->user();
    $other = User::factory()->create(['role' => 'vendedor']);
    $admin = User::factory()->create(['role' => 'admin']);
    $otherCompany = Company::create(['name' => 'Cuenta privada', 'owner_id' => $other->id]);
    $otherContact = Contact::create([
        'first_name' => 'Contacto', 'last_name' => 'Privado', 'email' => 'privado@example.test',
        'company_id' => $otherCompany->id, 'owner_id' => $other->id,
    ]);
    $otherDeal = Deal::create([
        'title' => 'Trato privado', 'company_id' => $otherCompany->id, 'owner_id' => $other->id,
        'value' => 999, 'stage' => 'prospecto', 'probability' => 25,
    ]);
    $otherActivity = new Activity(['type' => 'tarea', 'subject' => 'Tarea privada', 'user_id' => $other->id]);
    $otherActivity->activitable()->associate($otherCompany);
    $otherActivity->save();

    $this->get('/companies')->assertDontSee('Cuenta privada');
    $this->get('/contacts')->assertDontSee('Contacto Privado');
    $this->get('/deals')->assertDontSee('Trato privado');
    $this->get('/activities')->assertDontSee('Tarea privada');
    $this->get('/dashboard')->assertDontSee('Tarea privada')->assertDontSee('Trato privado');
    $this->patchJson("/deals/{$otherDeal->id}/stage", ['stage' => 'ganada'])->assertNotFound();
    $this->patchJson("/activities/{$otherActivity->id}/toggle")->assertNotFound();

    $this->postJson('/contacts', [
        'first_name' => 'Otro', 'last_name' => 'Contacto', 'email' => 'nuevo@example.test',
        'role' => 'Directora', 'company_name' => $otherCompany->id, 'source' => 'Web Inbound', 'status' => 'Nuevo Lead',
    ])->assertUnprocessable()->assertJsonValidationErrors('company_name');
    $this->postJson('/activities', [
        'type' => 'llamada', 'subject' => 'Seguimiento', 'company_name' => $this->company->id,
        'contact_name' => $otherContact->id, 'schedule_label' => '2025-12-15T18:00',
    ])->assertUnprocessable()->assertJsonValidationErrors('contact_name');
    $this->postJson('/companies', [
        'name' => 'Intento', 'sector' => 'SaaS', 'status' => 'Prospecto', 'location' => 'Madrid', 'owner' => $other->id,
    ])->assertUnprocessable()->assertJsonValidationErrors('owner');
    $this->postJson('/deals', [
        'title' => 'Intento', 'company_name' => $otherCompany->id, 'amount' => 100,
        'stage' => 'prospecto', 'close_date' => '2026-12-15', 'owner_name' => $other->id,
    ])->assertUnprocessable()->assertJsonValidationErrors(['company_name', 'owner_name']);
    $this->postJson('/activities', [
        'type' => 'tarea', 'subject' => 'Intento', 'company_name' => $this->company->id,
        'owner' => $other->id, 'schedule_label' => '2026-12-15T18:00',
    ])->assertUnprocessable()->assertJsonValidationErrors('owner');

    $this->actingAs($admin)->get('/companies')->assertSee('Cuenta privada')->assertSee('Empresa de prueba');
    $this->actingAs($admin)->patchJson("/deals/{$otherDeal->id}/stage", ['stage' => 'ganada'])->assertOk();
    $this->actingAs($admin)->patchJson("/activities/{$otherActivity->id}/toggle")->assertOk();
    $this->flushSession();
    $this->actingAs($other)->get('/deals')->assertSee('Trato privado')->assertDontSee('Expansión Regional Latam');
    $this->assertDatabaseHas('deals', ['id' => $otherDeal->id, 'stage' => 'ganada', 'probability' => 100]);
    $this->assertDatabaseHas('activities', ['id' => $otherActivity->id, 'user_id' => $other->id]);
    expect($otherActivity->fresh()->completed_at)->not->toBeNull();
    $this->actingAs($seller)->get('/companies')->assertSee('Empresa de prueba')->assertDontSee('Cuenta privada');
});

test('validates related records and structured dates and saves the real relationships', function () {
    $this->postJson('/deals', [
        'title' => 'Trato inválido', 'company_name' => $this->company->id, 'amount' => 100,
        'stage' => 'prospecto', 'close_date' => 'mañana', 'owner_name' => auth()->id(),
    ])->assertUnprocessable()->assertJsonValidationErrors('close_date');

    $deal = $this->postJson('/deals', [
        'title' => 'Trato real', 'company_name' => $this->company->id, 'amount' => 1200.50,
        'stage' => 'propuesta', 'close_date' => '2026-12-15', 'owner_name' => auth()->id(),
    ])->assertCreated();
    $this->assertDatabaseHas('deals', [
        'id' => $deal->json('deal.id'), 'company_id' => $this->company->id,
        'owner_id' => auth()->id(), 'value' => 1200.50, 'stage' => 'propuesta',
    ]);

    $activity = $this->postJson('/activities', [
        'type' => 'reunion', 'subject' => 'Reunión real', 'company_name' => $this->company->id,
        'contact_name' => $this->contact->id, 'schedule_label' => '2026-12-15T18:00',
    ])->assertCreated();
    $this->assertDatabaseHas('activities', [
        'id' => $activity->json('activity.id'), 'activitable_type' => Contact::class,
        'activitable_id' => $this->contact->id, 'user_id' => auth()->id(),
    ]);
    $this->get('/companies?company_id='.$this->company->id)->assertSee('Reunión real');
});

test('does not disclose linked records assigned to another user', function () {
    $other = User::factory()->create(['role' => 'vendedor']);
    $company = Company::create(['name' => 'Empresa ajena', 'owner_id' => $other->id]);
    Contact::create([
        'first_name' => 'Cliente', 'last_name' => 'Propio', 'email' => 'propio@example.test',
        'company_id' => $company->id, 'owner_id' => auth()->id(),
    ]);
    Deal::create([
        'title' => 'Oportunidad propia', 'company_id' => $company->id, 'owner_id' => auth()->id(),
        'value' => 300, 'stage' => 'prospecto',
    ]);

    $this->get('/contacts')->assertSee('Cliente Propio')->assertDontSee('Empresa ajena');
    $this->get('/deals')->assertSee('Oportunidad propia')->assertDontSee('Empresa ajena');
});

test('optional fields can be omitted or null without inventing contact details', function (array $optionalFields) {
    $company = $this->postJson('/companies', [
        'name' => 'Empresa Nueva',
        'sector' => 'SaaS',
        'status' => 'Prospecto',
        'location' => 'Madrid',
        'owner' => auth()->id(),
        ...$optionalFields['company'],
    ]);

    $company->assertCreated()
        ->assertJsonPath('company.tax_id', '')
        ->assertJsonPath('company.website', '')
        ->assertJsonPath('company.phone', '')
        ->assertJsonPath('company.industry', 'SaaS');

    $contact = $this->postJson('/contacts', [
        'first_name' => 'Ana',
        'last_name' => 'García',
        'email' => 'ana@example.test',
        'role' => 'Directora',
        'company_name' => $company->json('company.id'),
        'source' => 'Web Inbound',
        'status' => 'Nuevo Lead',
        ...$optionalFields['contact'],
    ]);

    $contact->assertCreated()
        ->assertJsonPath('contact.phone', '')
        ->assertJsonPath('contact.owner', auth()->user()->name);

    $activity = $this->postJson('/activities', [
        'type' => 'nota',
        'subject' => 'Primera reunión',
        'company_name' => $company->json('company.id'),
        'schedule_label' => '2025-12-15T18:00',
        ...$optionalFields['activity'],
    ]);

    $activity->assertCreated()
        ->assertJsonPath('activity.contact_name', '')
        ->assertJsonPath('activity.owner', auth()->user()->name);
})->with([
    'omitted' => [['company' => [], 'contact' => [], 'activity' => []]],
    'null' => [[
        'company' => ['tax_id' => null, 'website' => null, 'phone' => null],
        'contact' => ['phone' => null],
        'activity' => ['contact_name' => null, 'owner' => null],
    ]],
]);
