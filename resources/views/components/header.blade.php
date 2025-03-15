<header class="d-flex align-items-center justify-content-between container p-2">
    <!-- Logo & Text -->
    <div class="d-flex align-items-center gap-2 flex-grow-1">
        <img 
            src="/build/assets/lemonade-logo.svg" 
            alt="Lemonade Logo"
            height="40"
            width="40"
        />
        <span class="text-white fs-5 fw-bold">Lemonade.</span>
    </div>


    <!-- Auth Buttons -->
    <div class="auth-btn d-flex align-items-center gap-2">
        @guest
            <!-- Sidebar Toggle Button -->
            <div class="sidebar-toggle" id="toggleSidebar" onclick="toggleSidebar()">
                <img 
                    src="/build/assets/icons/bars-solid.svg" 
                    alt="Bars Icon"
                    height="25"
                    width="25" 
                />
            </div>
            <script>
                const toggleSidebar = () => {
                    const sidebar = document.getElementById('sidebar');
                    sidebar.classList.toggle('show');
                };
            </script>
            
            @if (Route::has('login'))
                <button class="bg-white fw-semibold px-3 py-1 rounded-2xl d-none d-md-block">
                    <a href="{{ route('login') }}">Se Connecter</a>
                </button>
            @endif

            @if (Route::has('register'))
                <button class="bg-white fw-semibold px-3 py-1 rounded-2xl d-none d-md-block">
                    <a href="{{ route('register') }}">S'inscrire</a>
                </button>
            @endif
        @else
            <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl">
                <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>

                <x-login-dropdown />
            </button>
        @endguest
    </div>
</header>
