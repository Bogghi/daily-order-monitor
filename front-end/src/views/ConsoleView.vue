<template>
    this is the console
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const router = useRouter();

onMounted(() => {
  if(!authStore.isAuthenticated) {
    authStore.logout();
    router.push('/login');
  }
})
</script>