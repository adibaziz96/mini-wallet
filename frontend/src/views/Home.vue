<template>
  <div>
    <h1 v-if="currentUser" class="text-3xl font-bold mb-4">
      Welcome, {{ currentUser.name }}
    </h1>
    <h1 v-else class="text-3xl font-bold mb-4">Loading...</h1>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../axios';

const currentUser = ref(null);

async function fetchCurrentUser() {
  try {
    const res = await api.get('/api/me');
    currentUser.value = res.data;
  } catch (err) {
    console.error('Failed to fetch current user', err);
  }
}

onMounted(async () => {
  await fetchCurrentUser();
});
</script>
