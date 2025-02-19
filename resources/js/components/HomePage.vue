<template>
    <div class="min-h-screen bg-black">
        <!-- Header with white bottom border -->
        <header class="bg-black relative border-bottom custom-header">
            <div
                class="container d-flex justify-content-between align-items-center"
            >
                <!-- Logo & Brand Name -->
                <div class="d-flex align-items-center gap-2">
                    <img
                        src="/build/assets/logo.png"
                        alt="Lemonade Logo"
                        class="logo"
                    />
                    <span class="text-white fs-3 fw-bold">Lemonade.</span>
                </div>

                <div>
                    <!-- Access the URLs from data-* attributes -->
                    <a :href="loginUrl">
                        <button class="btn btn-light mx-2">Se connecter</button>
                    </a>
                    <a :href="registerUrl">
                        <button class="btn btn-light">S'inscrire</button>
                    </a>
                </div>
            </div>
        </header>
        <!-- Make sure the header is properly closed -->

        <!-- Main Content -->
        <main class="container text-white py-5">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Text Content -->
                <div class="flex-grow-1">
                    <h1 class="display-4 fw-bold">Créer et organiser</h1>
                    <h2 class="highlight-text">votre communication</h2>
                    <h1 class="display-4 fw-bold">digitale.</h1>
                    <p class="lead">
                        Devenez un pro en programmant et personnalisant vos
                        prochains posts.
                    </p>
                </div>

                <!-- Rocket Image -->
                <img
                    src="/build/assets/rocket.png"
                    alt="Rocket Image"
                    class="rocket-img"
                />
            </div>
        </main>
    </div>
</template>

<style>
/* Make the header taller */
.custom-header {
    padding: 40px 0; /* Increased padding for taller header */
    min-height: 140px; /* Set a taller minimum height */
}

/* Make the text bold */
.lead {
    font-weight: bold;
}
.display-4 fw-bold {
    margin-top: 10px;
}

/* Keep logo at 100px */
.logo {
    height: 100px; /* Keep the logo large */
    width: auto; /* Maintain aspect ratio */
}

/* Rocket Image - Increase Size */
.rocket-img {
    height: 500px; /* Adjust height */
    width: auto; /* Keep aspect ratio */
    margin-top: 40px; /* Add space between text and image */
}

/* Pink background with slant effect */
.highlight-text {
    display: inline-block;
    background: linear-gradient(to right, #ff6ec4, #ff92a5);
    color: white;
    padding: 10px 20px;
    font-size: 3.5rem;
    font-weight: bold;
    border-radius: 25px;
    transform: rotate(-3deg);
    margin-left: 30px; /* Moves the text & background to the right */
}
</style>

<script>
export default {
    props: {
        loginUrl: String,
        registerUrl: String,
    },
};
</script>
