<template>
  <div class="flex items-center justify-center h-screen">
    <div class="w-96 p-6 bg-white rounded shadow">
      <img src="../assets/logo.png" alt="Mini Wallet Logo" class="mx-auto mb-4 w-32 h-32 object-contain" />
      <form @submit.prevent="login">
        <input v-model="email" type="email" placeholder="Email" class="w-full p-2 mb-2 border rounded"/>
        <input v-model="password" type="password" placeholder="Password" class="w-full p-2 mb-2 border rounded"/>
        <button class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const router = useRouter();

const email = ref('');
const password = ref('');

const login = async () => {
  try {
    const res = await api.post('/api/login', { email: email.value, password: password.value });
    localStorage.setItem('token', res.data.token);
    toast.success('Login successful!');
    router.push('/dashboard');
  } catch (err) {
    toast.error('Login failed');
    console.error(err);
  }
};
</script>
