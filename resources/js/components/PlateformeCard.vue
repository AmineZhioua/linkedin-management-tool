<template>
    <div class="bg-white wh-100 vh-100 relative mt-8 plateforme-card">
        <!-- Heart Sticker -->
        <img 
            src="/build/assets/icons/heart-sticker.svg" 
            alt="Heart Sticker" 
            class="absolute top-[-40px] left-[40px]" 
            height="90"
            width="90"
        />

        <!-- Wave Image -->
        <img 
            src="/build/assets/images/waves.svg" 
            alt="Waves Image" 
            class="waves" 
        />

        <!-- Rocket Side Image -->
        <div class="rocket-div">
            <div class="rocket-content">
                <img 
                    src="/build/assets/icons/rocket-sticker.svg" 
                    alt="Rocket Sticker" 
                    class="rocket-sticker" 
                />
                <p class="rocket-pg text-black fw-semibold">
                    Cette étape est obligatoire!
                </p>
            </div>
        </div>

        <!-- Card Main Content -->
        <div class="container flex flex-col align-items-center justify-center card-main">
            <slot></slot>
        </div>

        <!-- Icons in The Background -->
        <img 
            src="/build/assets/icons/fi-ss-palette.svg" 
            alt="Calendar Icon" 
            class="absolute top-[10%] left-[12%] bg-img"
        />

        <img 
            src="/build/assets/icons/fi-oo-palette.svg" 
            alt="Calendar Icon" 
            class="absolute bottom-[5%] left-[25%] bg-img"
        />

        <img 
            src="/build/assets/icons/fi-purple-calendar.svg" 
            alt="Calendar Icon" 
            class="absolute bottom-[15%] right-[15%] bg-img"
        />

        <img 
            src="/build/assets/icons/fi-sr-calendar.svg" 
            alt="Calendar Icon" 
            class="absolute top-[20%] right-[12%] bg-img"
        />
    </div>
</template>

<script>
    export default {
        name: "PlateformeCard",
    };
</script>

<style scoped>
    .plateforme-card {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .card-main {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .waves {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        width: 100%;
        height: auto;
    }

    .rocket-div {
        position: absolute;
        top: 5%;
        right: 0;
    }

    .rocket-content {
        display: flex;
        align-items: center;
    }

    .rocket-sticker {
        width: 100px;
        height: auto;
        margin-right: -25px;
        z-index: 10;
    }

    .rocket-pg {
        background: linear-gradient(90deg, rgba(255, 0, 204, 0.645) 0%, rgba(247, 206, 71, 0.656) 100%);
        padding: 20px 30px;
        font-weight: 400;
        margin: 0;
        white-space: nowrap;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-15px);
        }
    }

    .bg-img {
        animation: bounce 2s infinite ease-in-out;
    }
</style>