<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-blue-600">
      Vue 3 funcionando 🎉
    </h1>

    <button
      class="mt-4 px-4 py-2 bg-blue-500 text-white rounded"
      @click="contador++"
    >
      Clicou {{ contador }} vezes
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const contador = ref(0)
</script>