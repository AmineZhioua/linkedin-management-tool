<template>
    <div v-if="isVisible" class="overlay" @click.self="closePopup">
        <div class="popup">
            <button class="close-btn" @click="closePopup">
                <img src="/build/assets/icons/close.svg" alt="close-icon" />
            </button>
            <img 
                :src="path" 
                alt="like-icon" 
                class="icon"
            />
            <h2 class="text-center text-black">
                <slot></slot>
            </h2>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        path: {
            type: String,
            required: false,
        },
    },

    data() {
        return {
            isVisible: true,
        };
    },

    methods: {
        closePopup() {
            this.isVisible = false;
        }
    }
};
</script>

<style scoped>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s ease-in-out;
        z-index: 1000;
    }

    .popup {
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: white;
        padding: 80px 30px;
        border-radius: 12px;
        text-align: center;
        position: relative;
        max-width: 90%;
        width: 400px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
        transform: scale(0.9);
        animation: scaleUp 0.3s ease-in-out forwards;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #555;
        transition: color 0.2s;
    }

    .close-btn:hover {
        color: #000;
    }

    .icon {
        width: 120px;
        height: 120px;
        margin-bottom: 15px;
    }

    .popup h2 {
        font-size: 24px;
        margin-top: 10px;
        line-height: 1.5;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes scaleUp {
        from { transform: scale(0.9); }
        to { transform: scale(1); }
    }
</style>