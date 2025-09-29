<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Transaction</h2>
    <h4 class="mb-4">Current Balance (${{ balance }})</h4>

    <form @submit.prevent="addTransaction" class="mb-4 flex flex-wrap gap-2 items-center">
      <select v-model="receiver_id" class="p-2 border rounded">
        <option disabled value="">Select Receiver</option>
        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
      </select>

      <div class="relative">
        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
        <input 
          v-model.number="amount" 
          type="number" 
          placeholder="Amount" 
          class="pl-6 p-2 border rounded"
        />
      </div>

      <button class="bg-green-500 text-white p-2 rounded">Transfer</button>
    </form>

    <ul>
      <li 
        v-for="t in transactions" 
        :key="t.id" 
        class="flex justify-between items-center border p-2 mb-2 rounded bg-white"
      >
        <div>
          <span class="font-bold">{{ t.sender.name }} → {{ t.receiver.name }}</span>
          <span 
            :class="{
              'text-green-600 font-semibold ml-2': t.receiver_id === currentUser.id,
              'text-red-600 font-semibold ml-2': t.sender_id === currentUser.id
            }"
          >
            {{ t.receiver_id === currentUser.id ? `+ $${t.total_amount}` : `- $${t.total_amount}` }}
          </span>
        </div>

        <div class="text-gray-400 text-sm">
          {{ new Date(t.created_at).toLocaleString('en-UK', {month: '2-digit', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true}).replace(/am|pm/, match => match.toUpperCase()) }}
        </div>
      </li>
    </ul>


    <div class="flex gap-2 mt-4">
      <button 
        class="bg-gray-300 px-3 py-1 border rounded"
        :disabled="currentPage === 1"
        @click="goToPage(currentPage - 1)">
        Prev
      </button>

      <span>Page {{ currentPage }} / {{ lastPage }}</span>

      <button 
        class="bg-gray-300 px-3 py-1 border rounded"
        :disabled="currentPage === lastPage"
        @click="goToPage(currentPage + 1)">
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import api from '../axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { useToast } from 'vue-toastification';

const toast = useToast();

const currentUser = ref(null);
const balance = ref(0);
const transactions = ref([]);
const users = ref([]);
const receiver_id = ref('');
const amount = ref(0);

const perPage = 5;
const currentPage = ref(1);
const lastPage = ref(1);

let echo = null;

async function fetchCurrentUser() {
  try {
    const res = await api.get('/api/me');
    currentUser.value = res.data;
  } catch (err) {
    console.error('Failed to fetch current user', err);
  }
}

async function fetchUsers() {
  try {
    const res = await api.get('/api/users');
    users.value = res.data.filter(u => u.id !== currentUser.value?.id);
  } catch (err) {
    console.error('Failed to fetch users', err);
  }
}

async function fetchTransactions(perPage, page) {
  try {
    const res = await api.get(`/api/transactions?perPage=${perPage}&page=${page}`);
    balance.value = res.data.balance;
    transactions.value = res.data.transactions.data;
    currentPage.value = res.data.transactions.current_page;
    lastPage.value = res.data.transactions.last_page;
  } catch (err) {
    console.error('Failed to fetch transactions', err);
  }
}

async function goToPage(page) {
  if (page >= 1 && page <= lastPage.value) {
    await fetchTransactions(perPage, page);
  }
}

async function addTransaction() {
  if (!receiver_id.value || amount.value <= 0) {
    toast.error('Please select a receiver and enter a valid amount');
    return;
  }

  try {
    const res = await api.post('/api/transactions', {
      receiver_id: receiver_id.value,
      amount: amount.value
    });

    balance.value = res.data.data.balance;
    transactions.value.unshift(res.data.data.transaction);
    toast.success('Transfer successful!');

    receiver_id.value = '';
    amount.value = 0;
  } catch (err) {
    console.error('Failed to transfer', err);
    toast.error(err.response?.data?.error || 'Transfer failed');
  }
}

function initEcho() {
  if (!currentUser.value) return;

  window.Pusher = Pusher;

  echo = new Echo({
    broadcaster: 'pusher',
    key: '094025ee8088fa9197f1',
    cluster: 'ap1',
    forceTLS: true,
    authEndpoint: 'http://api-wallet.local.com/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    }
  });

  echo.private(`user.${currentUser.value.id}`)
    .listen('.TransactionMade', (e) => {
      balance.value = e.balance;
      transactions.value.unshift(e);
      toast.success(`New transaction: $${e.amount} received!`);
    });

  echo.connector.pusher.connection.bind('state_change', (states) => console.log('Pusher state:', states));
  echo.connector.pusher.connection.bind('error', (err) => console.error('Pusher error:', err));
}

onMounted(async () => {
  await fetchCurrentUser();
  await fetchUsers();
  await fetchTransactions(perPage, currentPage.value);
  initEcho();
});

onBeforeUnmount(() => {
  if (echo && currentUser.value) {
    echo.leave(`private-user.${currentUser.value.id}`);
  }
});
</script>
