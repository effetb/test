<template>
  <div class="main container">
    <router-view></router-view>
  </div>
</template>

<script>
import '@/assets/style/app.scss'
import {apiConnection} from "@/services/api-connection.js";

export default {
  name: 'App',
}
</script>

<style lang="scss">
@use 'assets/style/app';
</style>
