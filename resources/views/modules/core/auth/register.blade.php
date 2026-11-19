<x-ui.guest-layout title="Crear cuenta">
    <crm-form-validate>
        <form action="{{ route('register.store') }}" method="POST" class="crm-stack">
            @csrf
            <x-ui.form-field name="name" label="Nombre" :value="old('name')" autocomplete="name" required autofocus />
            <x-ui.form-field name="email" label="Correo electrónico" type="email" :value="old('email')" autocomplete="email" required />
            <x-ui.form-field name="password" label="Contraseña" type="password" autocomplete="new-password" minlength="8" required />
            <x-ui.form-field name="password_confirmation" label="Confirmar contraseña" type="password" autocomplete="new-password" minlength="8" required />
            <x-ui.button type="submit" variant="primary">Crear cuenta</x-ui.button>
            <a class="crm-auth-link" href="{{ route('login') }}">Ya tengo una cuenta</a>
        </form>
    </crm-form-validate>
</x-ui.guest-layout>
