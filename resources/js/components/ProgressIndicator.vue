<template>
    <div class="flex items-center justify-center w-full max-w-[700px] mx-auto">
    <div class="w-full bg-gray-200 rounded-full h-[15px]">
      <!-- Progress Bar -->
      <div class="bg-gray-600 h-[15px] rounded-full transition-all duration-300" :style="{ width: `${progressPercentage}%` }"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProgressIndicator',
  props: {
    currentStep: {
      type: Number,
      required: true
    },
    totalSteps: {
      type: Number,
      required: true
    }
  },
  computed: {
    progressPercentage() {
      return (this.currentStep / this.totalSteps) * 100;
    }
  }
}
</script>