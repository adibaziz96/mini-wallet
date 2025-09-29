import { createRouter, createWebHistory } from "vue-router";
import Layout from "../components/Layout.vue";
import Home from "../views/Home.vue";
import Transactions from "../views/Transactions.vue";
import Login from "../views/Login.vue";

const routes = [
  { path: "/login", component: Login },
  {
    path: "/dashboard",
    component: Layout,
    children: [
      { path: "", component: Home },
      { path: "transactions", component: Transactions },
    ],
  },
  { path: "/:pathMatch(.*)*", redirect: "/login" },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
