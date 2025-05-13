<div 
    class="dropdown-menu dropdown-menu-end absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 hidden"
    aria-labelledby="navbarDropdown"
>
    <div class="py-1">
        <!-- LinkedIn Dashboard -->
        <a class="dropdown-item block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 border-b border-gray-100" 
            href="{{ route('dashboard') }}">
            <div class="flex items-center gap-2">
                <img src="/build/assets/icons/linkedin-blue.svg" alt="LinkedIn Icon" height="20" width="20" />
                <span>LinkedIn Dashboard</span>
            </div>
        </a>

        <!-- WhatsApp Dashboard -->
        <a class="dropdown-item block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 border-b border-gray-100" 
            href="{{ route('welcome') }}">
            <div class="flex items-center gap-2">
                <img src="/build/assets/icons/whatsapp-green.svg" alt="WhatsApp Icon" height="20" width="20" />
                <span>WhatsApp Dashboard</span>
            </div>
        </a>

        <!-- Logout -->
        <a class="dropdown-item block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" 
            href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>{{ __('Logout') }}</span>
            </div>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
