<template>
    <div class="section">
        <Icon :icon="icon" height="35" />
    </div>
</template>

<script setup>
import { Icon } from "@iconify/vue";
import { computed } from "vue";

const props = defineProps({
    icon: {
        type: String,
        required: true
    },
    clickedIcon: {
        type: String,
        required: false,
        default: null
    },
    clicked: {
        type: Boolean,
        required: false,
        default: false
    }
});

const icon = computed(() => props.clicked ? props.clickedIcon : props.icon);

</script>

<style scoped>
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
</style>