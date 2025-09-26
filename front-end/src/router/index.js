import { createRouter, createWebHistory } from "vue-router";
import LoginView from "@/views/login/LoginView.vue";
import ConsoleView from "@/views/console/ConsoleView.vue";
import ArchiveView from "@/views/console/archive/ArchiveView.vue";
import HomeView from "@/views/console/home/HomeView.vue";

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
      children: [
        {
          path: "archive",
          name: "archive",
          component: ArchiveView,
        },
        {
          path: "home",
          name: "home",
          component: HomeView,
        }
      ]
    }
  ],
});

export default router;
