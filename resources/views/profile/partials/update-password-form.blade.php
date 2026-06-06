<div class="profile_card">
    <div class="profile_card_header">
        <div class="profile_card_icon">
            <i class="fa-solid fa-lock"></i>
        </div>
        <div>
            <h2>Actualizar contraseña</h2>
            <p>Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.</p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="profile_form">
        @csrf
        @method('put')

        <div class="form_field">
            <label for="update_password_current_password">
                <i class="fa-solid fa-key"></i> Contraseña actual
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                   autocomplete="current-password"
                   placeholder="Tu contraseña actual">
            @if($errors->updatePassword->get('current_password'))
                <span class="form_error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->updatePassword->first('current_password') }}
                </span>
            @endif
        </div>

        <div class="form_field">
            <label for="update_password_password">
                <i class="fa-solid fa-lock"></i> Nueva contraseña
            </label>
            <input id="update_password_password" name="password" type="password"
                   autocomplete="new-password"
                   placeholder="Tu nueva contraseña">
            @if($errors->updatePassword->get('password'))
                <span class="form_error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->updatePassword->first('password') }}
                </span>
            @endif
        </div>

        <div class="form_field">
            <label for="update_password_password_confirmation">
                <i class="fa-solid fa-shield-halved"></i> Confirmar contraseña
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password"
                   placeholder="Repite tu nueva contraseña">
            @if($errors->updatePassword->get('password_confirmation'))
                <span class="form_error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </span>
            @endif
        </div>

        <div class="form_actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Actualizar contraseña
            </button>
        </div>
    </form>
</div>
