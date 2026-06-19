@php
    $user = $user ?? auth()->user();
@endphp

<div class="profile_card">
    <div class="profile_card_header">
        <div class="profile_card_icon">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
            <h2>Información del perfil</h2>
            <p>Actualiza tu nombre y dirección de correo electrónico.</p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile_form">
        @csrf
        @method('patch')

        <div class="form_field">
            <label for="name">
                <i class="fa-solid fa-user"></i> Nombre
            </label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}"
                   required autofocus autocomplete="name"
                   placeholder="Tu nombre completo">
            @if($errors->get('name'))
                <span class="form_error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first('name') }}
                </span>
            @endif
        </div>

        <div class="form_field">
            <label for="email">
                <i class="fa-solid fa-envelope"></i> Correo electrónico
            </label>
            <input id="email" name="email" type="email"
                   value="{{ old('email', $user->email) }}"
                   required autocomplete="username"
                   placeholder="tu@correo.com">
            @if($errors->get('email'))
                <span class="form_error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first('email') }}
                </span>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="verify_notice">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Tu dirección de correo no está verificada.</span>
                    <button form="send-verification" type="submit" class="link_btn">
                        Reenviar correo de verificación
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <span class="verify_sent">
                            <i class="fa-solid fa-check"></i> Se envió un nuevo enlace.
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <div class="form_actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
        </div>
    </form>
</div>
