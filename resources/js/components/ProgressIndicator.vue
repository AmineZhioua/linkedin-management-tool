<template>
    <div class="flex items-center justify-center w-full mx-auto mb-6">
    <div class="w-full bg-gray-200 rounded-full h-2.5">
      <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-300" 
        :style="{ width: `${progressPercentage}%` }"
      ></div>
    </div>
    <span class="ml-2 text-sm text-gray-600">
      {{ currentStep }}/{{ totalSteps }}
    </span>
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