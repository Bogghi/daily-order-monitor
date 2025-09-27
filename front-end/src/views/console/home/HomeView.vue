<template>
    <div class="main">
        <div class="container">
            <h1>Lista ordini</h1>
            <div class="table">
                <DataTable :value="ordersStore.orders" v-model:filters="filters" showGridlines scrollable>
                    <template #header>
                        <div class="table-header">
                            <IconField>
                                <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                            </IconField>
                            <Button variant="text" @click="openBottomSheet">
                                <Icon icon="solar:add-square-linear" height="25" />
                            </Button>
                        </div>
                    </template>
                    <Column style="min-width: 100px" field="order_id" header="#id"></Column>
                    <Column style="min-width: 100px" field="name" header="Nome"></Column>
                    <Column style="min-width: 100px" field="description" header="Descrizione"></Column>
                    <Column style="min-width: 100px" field="value" header="Valore"></Column>
                    <Column style="min-width: 100px" field="date" header="Data"></Column>
                </DataTable>
            </div>

            <BottomSheet ref="bottomSheet">
                <h2 style="margin-bottom: 10px;">Nuovo ordine</h2>
                <InputText type="text" style="margin-bottom: 10px;" v-model="orderForm.name" variant="filled" placeholder="Nome" />
                <InputText type="text" style="margin-bottom: 10px;" v-model="orderForm.description" variant="filled" placeholder="Descrizione" />
            </BottomSheet>
        </div>
    </div>
</template>

<script setup>
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { DataTable, Column, InputText, Button, IconField } from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref, computed, reactive } from 'vue';
import { useOrdersStore } from '@/stores/orders';
import { Icon } from "@iconify/vue";   

let filters = {
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
};

const ordersStore = useOrdersStore();
const bottomSheet = ref(null);
const orderForm = reactive({
    name: '',
    description: '',
    order_items: []
});

const openBottomSheet = () => {
    if (bottomSheet.value) {
        bottomSheet.value.open();
    }
};

onMounted(() => {
    (async () => {
        await ordersStore.fetchOrders();
    })();
});
</script>