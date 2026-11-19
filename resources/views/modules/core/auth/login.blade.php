<x-ui.guest-layout title="Iniciar sesión">
    <crm-form-validate>
        <form action="{{ route('login.store') }}" method="POST" class="crm-stack">
            @csrf
            <x-ui.form-field name="email" label="Correo electrónico" type="email" :value="old('email')" autocomplete="email" required autofocus />
            <x-ui.form-field name="password" label="Contraseña" type="password" autocomplete="current-password" required />
            <label class="crm-row crm-row--nowrap">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span>Recordarme</span>
            </label>
            <x-ui.button type="submit" variant="primary">Iniciar sesión</x-ui.button>
            <div class="crm-auth-links">
                <a class="crm-auth-link" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                <a class="crm-auth-link" href="{{ route('register') }}">Crear cuenta</a>
            </div>
        </form>
    </crm-form-validate>
</x-ui.guest-layout>
