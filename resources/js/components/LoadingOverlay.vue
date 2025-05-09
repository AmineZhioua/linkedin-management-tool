<template>
    <div v-if="isLoading" class="loading-overlay">
        <div class="loader-container">
            <div class="loader"></div>
            <p class="mt-3 text-white">Traitement en cours...</p>
        </div>
    </div>
</template>

<script>
export default {
  name: 'LoadingOverlay',
  props: {
    isLoading: {
      type: Boolean,
      required: true
    },
    message: {
        type: String,
        // default: 'Traitement en cours...',
        required: false
    }
  }
}
</script>

<style scoped>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.loader-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.loader {
    border: 5px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 5px solid white;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

</style>