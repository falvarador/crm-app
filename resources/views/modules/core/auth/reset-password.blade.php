<x-ui.guest-layout title="Restablecer contraseña">
    <crm-form-validate>
        <form action="{{ route('password.update') }}" method="POST" class="crm-stack">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <x-ui.form-field name="email" label="Correo electrónico" type="email" :value="old('email', $email)" autocomplete="email" required />
            <x-ui.form-field name="password" label="Nueva contraseña" type="password" autocomplete="new-password" minlength="8" required autofocus />
            <x-ui.form-field name="password_confirmation" label="Confirmar contraseña" type="password" autocomplete="new-password" minlength="8" required />
            <x-ui.button type="submit" variant="primary">Restablecer contraseña</x-ui.button>
        </form>
    </crm-form-validate>
</x-ui.guest-layout>
