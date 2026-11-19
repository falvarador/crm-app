<x-ui.guest-layout title="Recuperar contraseña">
    <p class="crm-card__subtitle">Introduce tu correo para recibir un enlace de recuperación.</p>
    <crm-form-validate>
        <form action="{{ route('password.email') }}" method="POST" class="crm-stack">
            @csrf
            <x-ui.form-field name="email" label="Correo electrónico" type="email" :value="old('email')" autocomplete="email" required autofocus />
            <x-ui.button type="submit" variant="primary">Enviar enlace</x-ui.button>
            <a class="crm-auth-link" href="{{ route('login') }}">Volver al inicio de sesión</a>
        </form>
    </crm-form-validate>
</x-ui.guest-layout>
