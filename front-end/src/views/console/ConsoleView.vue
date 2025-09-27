<template>
    <div class="console-view">
        <div class="side-bar">
            <div class="logo">
                <img src="/logo.png" alt="">
            </div>
            <div class="sections">
                <div class="leading">
                    <AppSidebarButton
                        v-for="(section, index) in sections"
                        :key="index"
                        :icon="section.icon"
                        :clickedIcon="section.clickedIcon"
                        :clicked="section.clicked"
                        @click="section.action" />
                </div>
                <div class="bottom">
                    <AppSidebarButton icon="solar:logout-2-outline" @click="logout" />
                </div>
            </div>
        </div>
        <div class="main-view">
            <router-view/>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import AppSidebarButton  from '@/components/console/AppSidebarButton.vue';

const authStore = useAuthStore();
const router = useRouter();

let sections = reactive([
    {
        icon: 'solar:home-2-linear',
        clickedIcon: 'solar:home-2-bold',
        action: () => {
            sections[0].clicked = true;
            sections.forEach((section, index) => {
                if(index !== 0) section.clicked = false;
            });
            router.push('/console/home');
        },
        clicked: false
    },
    {
        icon: 'solar:archive-minimalistic-linear',
        clickedIcon: 'solar:archive-minimalistic-bold',
        action: () => {
            sections[1].clicked = true;
            sections.forEach((section, index) => {
                if(index !== 1) section.clicked = false;
            });
            router.push('/console/archive');
        },
        clicked: false
    },
]);

const logout = () => {
    authStore.logout();
    router.push('/login');
}

onMounted(() => {
    if(!authStore.isAuthenticated) {
        authStore.logout();
        router.push('/login');
    }
    else {
        if(router.currentRoute.value.name === "home") {
            sections[0].clicked = true;
            sections[1].clicked = false;
        }
        else if(router.currentRoute.value.name === "archive") {
            sections[0].clicked = false;
            sections[1].clicked = true;
        }
        else {
            sections[0].clicked = true;
            sections[1].clicked = false;
            router.push('/console/home');
        }
    }
});

</script>

<style scoped>
.console-view {
  display: flex;
  height: 100vh;
  width: 100vw;

  .side-bar {
    width: 80px;
    background-color: var(--primary-color);
    color: white;

    .logo {
        width: 100%;
        margin-bottom: 20px;
        img {
            padding-top: 5px;
            width: 100%;
            height: auto;
        }
    }

    .sections {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: calc(100vh - 80px);

        .leading, .bottom {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .bottom .section {
            margin-bottom: 10px;
        }
    }
  }
}
</style>