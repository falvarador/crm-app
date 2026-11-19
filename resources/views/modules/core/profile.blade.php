<x-ui.layout title="Mi perfil" active="profile" breadcrumb="Mi perfil">
    <section class="crm-page-header">
        <div class="crm-page-header__title-group">
            <h1 class="crm-page-header__title">Mi perfil</h1>
            <p class="crm-page-header__subtitle">Gestiona tus datos y tu contraseña.</p>
        </div>
    </section>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    <div class="crm-split-layout crm-split-layout--8-4">
        <x-ui.card title="Datos personales">
            <crm-form-validate>
                <form action="{{ route('profile.update') }}" method="POST" class="crm-stack">
                    @csrf
                    @method('PATCH')
                    <x-ui.form-field name="name" label="Nombre" :value="old('name', $user->name)" autocomplete="name" required />
                    <x-ui.form-field name="email" label="Correo electrónico" type="email" :value="old('email', $user->email)" autocomplete="email" required />
                    <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                </form>
            </crm-form-validate>
        </x-ui.card>

        <x-ui.card title="Cambiar contraseña">
            <crm-form-validate>
                <form action="{{ route('profile.password') }}" method="POST" class="crm-stack">
                    @csrf
                    @method('PUT')
                    <x-ui.form-field name="current_password" label="Contraseña actual" type="password" autocomplete="current-password" required />
                    <x-ui.form-field name="password" label="Nueva contraseña" type="password" autocomplete="new-password" minlength="8" required />
                    <x-ui.form-field name="password_confirmation" label="Confirmar contraseña" type="password" autocomplete="new-password" minlength="8" required />
                    <x-ui.button type="submit" variant="secondary">Actualizar contraseña</x-ui.button>
                </form>
            </crm-form-validate>
        </x-ui.card>
    </div>
</x-ui.layout>
