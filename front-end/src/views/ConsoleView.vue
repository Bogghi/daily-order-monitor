<template>
    <div class="console-view">
        <div class="side-bar">
            <div class="logo">
                <img src="/logo.png" alt="">
            </div>
            <div class="sections">
                <div class="leading">
                    <div class="section">
                        <!-- <Icon icon="solar:home-2-linear" :height="35" /> -->
                        <Icon icon="solar:home-2-bold" height="35" />
                    </div>
                    <div class="section">
                        <Icon icon="solar:archive-minimalistic-linear" height="35" />
                        <!-- <Icon icon="solar:archive-minimalistic-bold" height="35" /> -->
                    </div>
                </div>
                <div class="bottom">
                    <div class="section">
                        <Icon icon="solar:logout-2-outline" height="35" />
                    </div>
                </div>
            </div>
        </div>
        <div class="main-view">
            <router-view/>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { Icon } from "@iconify/vue";

const authStore = useAuthStore();
const router = useRouter();

onMounted(() => {
  if(!authStore.isAuthenticated) {
    authStore.logout();
    router.push('/login');
  }
})
</script>

<style scoped>
.console-view {
  display: flex;
  height: 100vh;
  width: 100vw;

  .side-bar {
    width: 80px;
    background-color: #df0000;
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

            .section {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                border-radius: 10px;
                transition: background-color 0.2s ease;

                &:hover {
                    cursor: pointer;
                    background-color: #b30000;
                }
            }
        }

        .bottom .section {
            margin-bottom: 10px;
        }
    }
  }
}
</style>