<div class="profile_card profile_card_danger">
    <div class="profile_card_header">
        <div class="profile_card_icon profile_card_icon_danger">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h2>Eliminar cuenta</h2>
            <p>Una vez que tu cuenta sea eliminada, todos sus recursos y datos se borrarán permanentemente. Antes de continuar, descarga cualquier información que desees conservar.</p>
        </div>
    </div>

    <button type="button" class="btn btn-danger" onclick="openDeleteModal()">
        <i class="fa-regular fa-trash-can"></i> Eliminar cuenta
    </button>
</div>

<div class="modal_overlay hidden" id="deleteAccountModal">
    <div class="task_modal">
        <div class="modal_header">
            <h2>
                <i class="fa-solid fa-circle-exclamation" style="color: #e63946; margin-right: 6px;"></i>
                ¿Eliminar tu cuenta?
            </h2>
            <button class="close_modal" onclick="closeDeleteModal()">&times;</button>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <p style="color: #475569; font-size: 0.95rem; margin: 0 0 20px 0;">
                Esta acción es <strong>permanente e irreversible</strong>. Por favor, ingresa tu contraseña para confirmar.
            </p>

            <div class="form_field">
                <label for="delete_password">
                    <i class="fa-solid fa-key"></i> Contraseña
                </label>
                <input id="delete_password" name="password" type="password"
                       placeholder="Ingresa tu contraseña"
                       style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px; width: 100%; font-size: 0.95rem; outline: none; color: #1e293b;">
                @if($errors->userDeletion->get('password'))
                    <span class="form_error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $errors->userDeletion->first('password') }}
                    </span>
                @endif
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fa-regular fa-trash-can"></i> Sí, eliminar cuenta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal() {
        const m = document.getElementById('deleteAccountModal');
        m.classList.remove('hidden');
        setTimeout(() => document.getElementById('delete_password')?.focus(), 100);
    }

    function closeDeleteModal() {
        const m = document.getElementById('deleteAccountModal');
        m.classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDeleteModal();
    });

    @if($errors->userDeletion->isNotEmpty())
        document.addEventListener('DOMContentLoaded', () => openDeleteModal());
    @endif
</script>
