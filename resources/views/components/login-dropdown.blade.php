<div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
    <a class="dropdown-item bg-pink-200 d-flex align-items-center gap-1" href="{{ route('dashboard') }}">
        <img 
            src="/build/assets/icons/linkedin-blue.svg" 
            alt="Linkedin Icon" 
            height="20"
            width="20"
            class="mb-1"
        />
        <p class="pr-4 mt-2">LinkedIn Dashboard</p>
    </a>
    <a class="dropdown-item bg-pink-200 d-flex align-items-center gap-1" href="{{ route('dashboard') }}">
        <img 
            src="/build/assets/icons/whatsapp-green.svg" 
            alt="WhatsApp Icon" 
            height="20"
            width="20"
            class="mb-1"
        />
        <p class="pr-4 mt-2">WhatsApp Dashboard</p>
    </a>
    <a class="dropdown-item bg-pink-200" href="{{ route('logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        {{ __('Logout') }}
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>