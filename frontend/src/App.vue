<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

onMounted(() => {
  const token = localStorage.getItem('token');
  if (token && window.location.pathname === '/') {
    router.push('/dashboard');
  }
});
</script>

<template>
  <router-view />
</template>
