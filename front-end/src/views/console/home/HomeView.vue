<template>
    <div class="main">
        <div class="container">
            <h1>Lista ordini</h1>
            <div class="table">
                <DataTable :value="ordersStore.orders" v-model:filters="filters" showGridlines tableStyle="min-width: var(--container-width)">
                    <template #header>
                        <div class="table-header">
                            <IconField>
                                <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                            </IconField>
                            <Button variant="text">
                                <Icon icon="solar:add-square-linear" height="25" />
                            </Button>
                        </div>
                    </template>
                    <Column field="order_id" header="#id"></Column>
                    <Column field="name" header="Nome"></Column>
                    <Column field="description" header="Descrizione"></Column>
                    <Column field="value" header="Valore"></Column>
                    <Column field="date" header="Data"></Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { DataTable, Column, InputText, Button, IconField } from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref, computed } from 'vue';
import { useOrdersStore } from '@/stores/orders';
import { Icon } from "@iconify/vue";   

let filters = {
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
};

const ordersStore = useOrdersStore();

onMounted(() => {
    (async () => {
        await ordersStore.fetchOrders();
    })();
});
</script>