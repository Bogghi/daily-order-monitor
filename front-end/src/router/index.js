import { createRouter, createWebHistory } from "vue-router";
import LoginView from "@/views/LoginView.vue";
import ConsoleView from "@/views/ConsoleView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/login",
      name: "login",
      component: LoginView,
    },
    {
      path: "/",
      name: "home",
      redirect: "/login",
    },
    {
      path: "/console",
      name: "console",
      component: ConsoleView,
    }
  ],
});

export default router;
