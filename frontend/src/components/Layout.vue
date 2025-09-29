<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside
      class="w-64 bg-white shadow flex flex-col fixed md:static top-0 left-0 h-full z-20 transform -translate-x-full md:translate-x-0 transition-transform"
      :class="{ 'translate-x-0': showSidebar }"
    >
      <img
        src="../assets/logo.png"
        alt="Mini Wallet Logo"
        class="mx-auto mb-4 w-32 h-32 object-contain"
      />

      <nav class="flex-1">
        <ul>
          <li>
            <router-link
              to="/dashboard"
              class="block py-2 px-6 hover:bg-gray-200"
              :class="{ 'bg-gray-200 font-semibold': $route.path === '/dashboard' }"
            >
              Home
            </router-link>
          </li>
          <li>
            <router-link
              to="/dashboard/transactions"
              class="block py-2 px-6 hover:bg-gray-200"
              :class="{ 'bg-gray-200 font-semibold': $route.path === '/dashboard/transactions' }"
            >
              Transaction
            </router-link>
          </li>
        </ul>
      </nav>

      <button
        @click="logout"
        class="m-6 bg-red-500 text-white p-2 rounded"
      >
        Logout
      </button>
    </aside>

    <!-- Overlay for mobile -->
    <div
      v-if="showSidebar"
      @click="showSidebar = false"
      class="fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden"
    ></div>

    <!-- Main content -->
    <main class="flex-1 p-6 overflow-auto">
      <!-- Header with Burger (mobile only) -->
      <header class="flex items-center justify-between bg-white p-4 shadow md:hidden">
        <h1 class="text-xl font-bold">Dashboard</h1>
        <button @click="toggleSidebar" class="p-2 rounded bg-gray-200">
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            ></path>
          </svg>
        </button>
      </header>

      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const showSidebar = ref(false);

function toggleSidebar() {
  showSidebar.value = !showSidebar.value;
}

function logout() {
  localStorage.removeItem("token");
  router.push("/login");
}
</script>

<style>
aside {
  transition: transform 0.3s ease-in-out;
}
</style>
