<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Http\Requests\PasswordLinkRequest;
use App\Modules\Core\Http\Requests\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function requestForm(): View
    {
        return view('modules.core.auth.forgot-password');
    }

    public function sendLink(PasswordLinkRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->safe()->only('email'));

        if (! in_array($status, [Password::RESET_LINK_SENT, Password::INVALID_USER, Password::RESET_THROTTLED], true)) {
            return back()->withErrors(['email' => 'No se pudo enviar el enlace. Inténtalo de nuevo.']);
        }

        return back()->with('status', 'Si existe una cuenta con ese correo, recibirás un enlace para restablecer tu contraseña.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('modules.core.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->safe()->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'El enlace no es válido o ha caducado. Solicita uno nuevo.']);
        }

        return redirect()->route('login')->with('status', 'Contraseña restablecida. Ya puedes iniciar sesión.');
    }
}
