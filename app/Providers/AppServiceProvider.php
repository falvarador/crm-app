<?php

namespace App\Providers;

use App\Models\User;
use App\Modules\Companies\Models\Company;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Core\Support\CrmAccess;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'components.ui.layout',
            'modules.companies.index',
            'modules.contacts.index',
            'modules.deals.index',
            'modules.activities.index',
            'modules.dashboard.index',
        ], function ($view): void {
            $user = auth()->user();
            $companies = CrmAccess::scope(Company::query(), $user)->orderBy('name')->pluck('name', 'id')->all();
            $view->with('crmCompanies', $companies)
                ->with('crmContacts', CrmAccess::scope(Contact::query(), $user)->orderBy('first_name')->get()
                    ->mapWithKeys(fn (Contact $contact) => [
                        $contact->id => $contact->name.(isset($companies[$contact->company_id]) ? ' — '.$companies[$contact->company_id] : ''),
                    ])->all())
                ->with('crmUsers', User::query()->when($user->role !== 'admin', fn ($query) => $query->whereKey($user->id))
                    ->orderBy('name')->pluck('name', 'id')->all());
        });

        ResetPassword::createUrlUsing(function (CanResetPassword $user, string $token): string {
            return rtrim(config('app.url'), '/').route('password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ], false);
        });
    }
}
