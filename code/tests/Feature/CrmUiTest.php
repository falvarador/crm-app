<?php

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
    'dashboard kpis' => ['/dashboard', 'dashboard-kpis', 'Valor Pipeline Abierto'],
    'dashboard closing deals' => ['/dashboard', 'dashboard-closing-deals', 'Oportunidades Próximas a Cerrar'],
    'companies list' => ['/companies', 'companies-list', 'NovaTech Solutions S.A.'],
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
        'owner' => 'Sofía Méndez',
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
        'company_name' => 'NovaTech Solutions S.A.',
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
        'company_name' => 'NovaTech Solutions S.A.',
        'source' => 'Web Inbound',
        'status' => 'Calificado',
    ]);

    $validResponse->assertCreated()->assertJsonPath('status', 'ok');
    $this->get('/contacts')->assertSee('Alejandro Gómez');
});

test('deals store and kanban stage patch update pipeline state', function () {
    $createResponse = $this->postJson('/deals', [
        'title' => 'Expansión CRM Global',
        'company_name' => 'NovaTech Solutions S.A.',
        'amount' => 45000,
        'stage' => 'prospecto',
        'close_date' => '15 Dic 2025',
        'owner_name' => 'Sofía Méndez',
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
        'company_name' => 'FinanSmart Corp',
        'contact_name' => 'Javier Roldán',
        'schedule_label' => 'Hoy, 18:00',
        'notes' => 'Confirmar términos de pago.',
    ]);

    $createResponse->assertCreated()->assertJsonPath('status', 'ok');
    $activityId = $createResponse->json('activity.id');

    $toggleResponse = $this->patchJson("/activities/{$activityId}/toggle");
    $toggleResponse->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('activity.completed', true);
});
