<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('crm:make-admin {email : Correo del usuario existente}', function () {
    $user = User::query()->where('email', $this->argument('email'))->first();

    if (! $user) {
        $this->error('No se encontró el usuario.');

        return 1;
    }

    $user->forceFill(['role' => 'admin'])->save();
    $this->info('Rol de administrador asignado.');

    return 0;
})->purpose('Asignar el rol de administrador a un usuario existente');
