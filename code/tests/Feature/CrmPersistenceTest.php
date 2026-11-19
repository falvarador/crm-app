<?php

use App\Models\User;
use App\Modules\Activities\Models\Activity;
use App\Modules\Companies\Models\Company;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Deals\Models\Deal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('crm schema is migrated without seeding demo records', function () {
    expect(Schema::hasColumns('companies', ['name', 'industry', 'status', 'owner_id', 'deleted_at']))->toBeTrue()
        ->and(Schema::hasColumns('contacts', ['company_id', 'first_name', 'last_name', 'email', 'owner_id', 'deleted_at']))->toBeTrue()
        ->and(Schema::hasColumns('deals', ['company_id', 'contact_id', 'owner_id', 'value', 'stage', 'expected_close_date', 'deleted_at']))->toBeTrue()
        ->and(Schema::hasColumns('activities', ['activitable_type', 'activitable_id', 'user_id', 'due_date', 'completed_at', 'deleted_at']))->toBeTrue();

    $this->seed();

    expect(User::count())->toBe(0)
        ->and(Company::count())->toBe(0)
        ->and(Contact::count())->toBe(0)
        ->and(Deal::count())->toBe(0)
        ->and(Activity::count())->toBe(0);
});

test('empty crm pages do not display fabricated records or metrics', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/companies')->assertOk()->assertSee('No se encontraron empresas')->assertDontSee('NovaTech');
    $this->get('/contacts')->assertOk()->assertSee('Sin contactos que coincidan');
    $this->get('/activities')->assertOk()->assertSee('Sin actividades en esta vista')->assertDontSee('Elena Sanz');
    $this->get('/dashboard')->assertOk()->assertSee('Sin tratos próximos a cerrar')->assertDontSee('28.5%')->assertDontSee('Sofía Méndez');
});

test('dashboard computes period metrics and upcoming deals from persisted records', function () {
    $this->travelTo(now()->setDate(2026, 11, 18)->setTime(12, 0));
    $user = User::factory()->create(['role' => 'vendedor']);
    $this->actingAs($user);
    $company = Company::create(['name' => 'Cuenta', 'owner_id' => $user->id]);
    $base = ['company_id' => $company->id, 'owner_id' => $user->id];
    Deal::create($base + ['title' => 'Cierre próximo', 'value' => 10000, 'stage' => 'propuesta', 'probability' => 55, 'expected_close_date' => '2026-11-25']);
    Deal::create($base + ['title' => 'Ganado', 'value' => 1000, 'stage' => 'ganada', 'probability' => 100]);
    Deal::create($base + ['title' => 'Perdido', 'value' => 2000, 'stage' => 'perdida', 'probability' => 0]);
    $old = Deal::create($base + ['title' => 'Antiguo', 'value' => 7000, 'stage' => 'prospecto', 'probability' => 25]);
    $old->created_at = now()->subMonths(3);
    $old->save();
    $activity = new Activity(['type' => 'tarea', 'subject' => 'Tarea de hoy', 'user_id' => $user->id, 'due_date' => now()]);
    $activity->activitable()->associate($company);
    $activity->save();

    $this->get('/dashboard?period=month')->assertOk()
        ->assertSee('$10,000')->assertSee('50%')->assertSee('Cierre próximo')->assertSee('Tarea de hoy')
        ->assertDontSee('$17,000');
    $this->get('/deals?owner='.$user->id)->assertSee('$7,250');
});
