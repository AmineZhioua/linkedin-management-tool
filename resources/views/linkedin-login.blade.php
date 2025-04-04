<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Connexion au LinkedIn</title>
    <link rel="icon" type="image/x-icon" href="/build/assets/lemonade-logo.svg">

    <!-- FontAwesome Kit -->
    <script src="https://kit.fontawesome.com/33f8496b80.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="vh-100 overflow-hidden d-flex login linkedin-login" id="app">
        <!-- Auth Dropdown Menu -->
        <div class="absolute top-4 right-4 text-black text-sm fw-semibold" style="z-index: 1000;">
            <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl">
                <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>
                <x-login-dropdown />
            </button>
        </div>

        <!-- Lemonade Logo -->
        <div class="logo-container">
            <a href="{{ route('welcome') }}" class="d-flex align-items-center gap-2">
                <img src="/build/assets/lemonade-logo.svg" alt="Lemonade Logo" height="40" width="40"/>
                <img src="/build/assets/icons/lemonade-black.svg" alt="Lemonade Text" class="lemonade-text" height="40" width="100"/>
            </a>
        </div>

        <!-- Image Side -->
        <div class="left-side">
            <img src="/build/assets/mockup-linkedin.png" alt="LinkedIn Image" class="homepage-img"/>
        </div>

        <!-- Form Side -->
        <div class="d-flex flex-grow-1 flex-col align-items-center justify-content-center">
            <div class="text-center text-black gap-4">
                <h1 class="fw-bold text-4xl">Tu es maintenant connecté en tant <br>que {{ Auth::user()->name }}</h1>
                <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>

            @if($linkedinUserList->isEmpty())
                <div class="d-flex align-items-center flex-sm-column gap-2 w-100" style="max-width: 400px;">
                    <button class="social-btn">
                        <img src="/build/assets/icons/linkedin-blue.svg" alt="linkedin-icon" height="30" width="30"/>
                        <a href="{{ route('linkedin.auth') }}" class="text-decoration-none text-black">
                            Se Connecter avec LinkedIn
                        </a>
                    </button>
                </div>
                <p class="text-black mt-2 text-center">
                    <u>Cette étape est obligatoire pour la création de ton dashboard</u>
                </p>
            @else
                <div class="d-flex flex-col align-items-start mx-auto">
                    <div class="text-center text-black">
                        <h3 class="fw-semibold text-xl">Vos comptes LinkedIn :</h3>

                        <!-- ALL USER LINKEDIN ACCOUNTS -->
                        @foreach($linkedinUserList as $linkedinUser)
                            <div class="flex px-2 py-2 rounded-full border justify-content-between cursor-pointer mt-2 linkedin-account">
                                <div class="flex align-items-center justify-content-center gap-3">
                                    <img 
                                        src="{{ $linkedinUser->linkedin_picture ?? '/build/assets/images/default-profile.png' }}"
                                        alt="profile-picture" 
                                        height="50" 
                                        width="50" 
                                        class="rounded-full"
                                    />
                                    <p class="mb-0 text-lg fw-semibold flex-grow-1">
                                        {{ $linkedinUser->linkedin_firstname }} {{ $linkedinUser->linkedin_lastname }}
                                    </p>

                                    <!-- Delete Button -->
                                    <button 
                                        onclick="showDeletePopup('{{ $linkedinUser->linkedin_id }}')"
                                        class="bg-transparent border-0 p-0"
                                    >
                                        <i class="fa-solid fa-trash ml-2 text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <!-- "Ajouter un compte" Button -->
                        <button 
                            class="flex align-items-center px-3 py-2 rounded-full border cursor-pointer gap-2 mt-2 ajout-btn"
                            onclick="showAddAccountPopup()"
                        >
                            <span class="text-decoration-none text-black flex align-items-center gap-1">
                                <img src="/build/assets/icons/add.svg" alt="add-icon" height="25" width="25"/>
                                Ajouter un compte LinkedIn
                            </span>
                        </button>
                    </div>
                </div>
            @endif
        </div>


        <!-- Popup for Adding Account -->
        <x-add-account-popup />


        <!-- Popup for LinkedIn Logout -->
        <x-logout-popup />

        <!-- Popup for Deleting Account -->
        <div id="linkedin-delete-popup" class="hidden">
            <div class="popup-overlay" onclick="document.getElementById('linkedin-delete-popup').classList.add('hidden')"></div>
            <div class="custom-popup">
                <button 
                    class="absolute top-4 right-4"
                    onclick="document.getElementById('linkedin-delete-popup').classList.add('hidden')"
                >
                    <img src="/build/assets/icons/close.svg" alt="close-icon" />
                </button>
                <img src="/build/assets/popups/sad-face.svg" alt="Sad Face" height="120" width="120"/>
                <p class="mt-2 fw-semibold text-xl">
                    Êtes-vous sûr de vouloir supprimer ce compte LinkedIn ?
                </p>
                <div class="flex align-items-center gap-2">
                    <button 
                        id="confirm-delete-btn"
                        class="bg-red-600 rounded-full text-white decoration-none px-4 py-2"
                        style="text-decoration: none;"
                    >
                        Confirmer
                    </button>
                    <button 
                        class="bg-transparent rounded-full text-black decoration-none px-4 py-2" 
                        onclick="document.getElementById('linkedin-delete-popup').classList.add('hidden')"
                        style="border: 1px solid black;"
                    >
                        Annuler
                    </button>
                </div>
            </div>
        </div>



        <!-- Display this Popup whenever a LinkedIn Error Occurs (Popup.vue) -->
        @if(session('linkedin_error'))
            <popup path="/build/assets/popups/sad-face.svg">
                <p>{{ session('linkedin_error') }}</p>
            </popup>
        @endif

        <!-- Display this Popup whenever a LinkedIn account is Successfully Linked (Popup.vue) -->
        @if(session('linkedin_success'))
            <popup path="/build/assets/popups/like-popup.svg">
                <p>{{ session('linkedin_success') }}</p>
            </popup>
        @endif

        @if(session('success_payment'))
            <popup path="/build/assets/popups/like-popup.svg">
                <p>{{ session('success_payment') }}</p>
            </popup>
        @endif
    </div>

    <script>
        function showAddAccountPopup() {
            const popup = document.getElementById('linkedin-add-account-popup');
            const countdownElement = document.getElementById('countdown');
            const logoutButton = document.getElementById('logout-button');
            let timeLeft = 15;

            popup.classList.remove('hidden');

            const countdown = setInterval(() => {
                timeLeft--;
                countdownElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    popup.classList.add('hidden');
                    // Proceed to LinkedIn auth after 15 seconds
                    window.location.href = "{{ route('linkedin.auth') }}";
                }
            }, 1000);

            logoutButton.addEventListener('click', function(event) {
                event.preventDefault();
                clearInterval(countdown);
            });

            popup.querySelector('.annuler-btn').addEventListener('click', () => {
                clearInterval(countdown);
            });

            popup.querySelector('.close').addEventListener('click', () => {
                clearInterval(countdown);
            });

        }

        function showDeletePopup(linkedinId) {
            const popup = document.getElementById('linkedin-delete-popup');
            popup.classList.remove('hidden');

            // Set the confirm button action
            const confirmBtn = document.getElementById('confirm-delete-btn');
            confirmBtn.onclick = function() {
                window.location.href = "{{ route('linkedin.delete') }}?linkedin_id=" + linkedinId;
            };
        }
    </script>
</body>
</html>