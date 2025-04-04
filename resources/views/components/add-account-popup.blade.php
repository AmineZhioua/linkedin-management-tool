<!-- Popup for Adding Account -->
<div id="linkedin-add-account-popup" class="hidden">
    <div 
        class="popup-overlay" 
        onclick="document.getElementById('linkedin-add-account-popup').classList.add('hidden')">
    </div>
    <div class="custom-popup">
        <!-- Close X Button -->
        <button 
            class="absolute top-4 right-4 close"
            onclick="document.getElementById('linkedin-add-account-popup').classList.add('hidden')"
        >
            <img src="/build/assets/icons/close.svg" alt="close-icon" />
        </button>
        <img 
            src="/build/assets/popups/stars.svg" 
            alt="Stars Icon" 
            height="120" 
            width="120"
        />
        <p class="mt-2 fw-semibold text-lg">
            Si vous êtes connecté à un compte LinkedIn, Cliquez sur <br><u>Se déconnecter d'un compte</u><br>
            avant d'ajouter un nouveau compte.<br>
            Sinon, cette action se poursuivra dans 
            <span id="countdown">15</span> secondes.
        </p>
        <div class="flex align-items-center gap-2">
            <button 
                class="flex align-items-center px-3 py-2 rounded-full border cursor-pointer gap-2 mt-2 ajout-btn"
                onclick="
                    document.getElementById('linkedin-logout-popup').classList.remove('hidden');
                    document.getElementById('linkedin-add-account-popup').classList.add('hidden');"
                id="logout-button"
            >
                <span class="text-decoration-none text-black flex align-items-center gap-1">
                    <img src="/build/assets/icons/logout.svg" alt="logout-icon" height="25" width="25"/>
                    Se déconnecter
                </span>
            </button>
            <button 
                class="bg-black rounded-full text-white decoration-none px-4 py-2 mt-2 annuler-btn" 
                onclick="document.getElementById('linkedin-add-account-popup').classList.add('hidden')"
            >
                Annuler
            </button>
        </div>
    </div>
</div>