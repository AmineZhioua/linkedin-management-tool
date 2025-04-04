<!-- Popup for LinkedIn Logout -->
<div id="linkedin-logout-popup" class="hidden">
    <div class="popup-overlay" onclick="document.getElementById('linkedin-logout-popup').classList.add('hidden')"></div>
    <div class="custom-popup">
        <button 
            class="absolute top-4 right-4"
            onclick="document.getElementById('linkedin-logout-popup').classList.add('hidden')"
        >
            <img src="/build/assets/icons/close.svg" alt="close-icon" />
        </button>
        <img src="/build/assets/popups/sad-face.svg" alt="Sad Face" height="120" width="120"/>
        <p class="mt-2 fw-semibold text-xl">
            Veuillez vous déconnecter de votre compte LinkedIn actuel avant d'ajouter un nouveau compte.
        </p>
        <div class="flex align-items-center gap-2">
            <a 
                href="{{ route('linkedin.logout') }}" 
                class="bg-black rounded-full text-white decoration-none px-4 py-2" 
                target="_blank"
                style="text-decoration: none;"
            >
                Déconnexion
            </a>
            <button 
                class="bg-transparent rounded-full text-black decoration-none px-4 py-2" 
                onclick="document.getElementById('linkedin-logout-popup').classList.add('hidden')"
                style="border: 1px solid black;"
            >
                Annuler
            </button>
        </div>
    </div>
</div>