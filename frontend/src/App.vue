<template>
  <div id="app">
    <router-view :key="$route.fullPath" />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const $route = useRoute()
</script>

<style scoped>
#app {
  font-family: 'Montserrat', sans-serif;
}
</style>