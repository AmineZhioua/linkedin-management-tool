<header class="d-flex align-items-center justify-between container p-4">
    <!-- Logo & Text -->
    <div class="d-flex align-items-center gap-2">
        <img 
            src="/build/assets/lemonade-logo.svg" 
            alt="Lemonade Logo"
            height="60"
            width="60"
        />
        <span class="text-white fs-5 fw-bold">Lemonade.</span>
    </div>
    <div class="auth-btn d-flex align-items-center gap-2">
        @guest
            @if (Route::has('login'))
            <button class="bg-white fw-semibold px-3 py-1 rounded-2xl">
                <a href="{{ route('login') }}">Se Connecter</a>
            </button>
            @endif

            @if (Route::has('register'))
            <button class="bg-white fw-semibold px-3 py-1 rounded-2xl">
                <a href="{{ route('register') }}">S'inscrire</a>
            </button>
            @endif
        @else
            <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl">
                <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item bg-pink-200" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </button>
        @endguest
    </div>
</header>