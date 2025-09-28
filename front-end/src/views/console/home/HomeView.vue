<template>
    <div class="main">
        <div class="container">
            <h1>Lista ordini</h1>
            <div class="table">
                <DataTable :value="filteredOrders" v-model:filters="filters" showGridlines scrollable selectionMode="single" @row-click="onRowClick">
                    <template #header>
                        <div class="table-header">
                            <div class="filter-section">
                                <IconField>
                                    <InputIcon>
                                        <Icon icon="solar:magnifer-linear" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cerca ordini..." />
                                </IconField>
                                <Calendar
                                    v-model="dateFilter"
                                    placeholder="Filtra per data"
                                    dateFormat="dd/mm/yy"
                                    showButtonBar
                                    @clear-click="clearDateFilter"
                                    style="margin-left: 10px;"
                                />
                            </div>
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
                <h2 style="margin-bottom: 10px;">{{ isEditMode ? 'Modifica ordine' : 'Nuovo ordine' }}</h2>
                <InputText type="text" style="margin-bottom: 10px;" v-model="orderForm.name" variant="filled" placeholder="Nome ordine" />
                <InputText type="text" style="margin-bottom: 15px;" v-model="orderForm.description" variant="filled" placeholder="Descrizione" />

                <h3 style="margin-bottom: 10px;">Seleziona prodotti</h3>
                <div class="product-list" style="max-height: 300px; overflow-y: auto; margin-bottom: 15px;">
                    <div
                        v-for="product in productsStore.products"
                        :key="product.product_id"
                        class="product-item"
                        @click="addProductToOrder(product)">
                        <div class="product-info">
                            <span class="product-name">{{ product.name }}</span>
                            <span class="product-price">{{ product.price }}</span>
                        </div>
                        <div class="product-quantity" v-if="getProductQuantity(product.product_id) > 0">
                            <Button
                                variant="text"
                                severity="danger"
                                size="small"
                                @click.stop="removeProductFromOrder(product.product_id)">
                                <Icon icon="solar:trash-bin-2-linear" height="16" />
                            </Button>
                            <span>{{ getProductQuantity(product.product_id) }}</span>
                        </div>
                    </div>
                </div>

                <div class="order-summary" style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 4px;">
                    <strong>Totale ordine: {{ formatPrice(orderTotal) }}</strong>
                </div>

                <div style="display: flex; gap: 10px;">
                    <Button
                        v-if="isEditMode"
                        @click="deleteOrder"
                        severity="danger"
                        :loading="deletingOrder"
                        style="flex: 1;">
                        Elimina ordine
                    </Button>
                    <Button
                        @click="saveOrder"
                        :loading="savingOrder"
                        :disabled="orderForm.order_items.length === 0"
                        style="flex: 1;">
                        {{ isEditMode ? 'Aggiorna ordine' : 'Salva ordine' }}
                    </Button>
                </div>
            </BottomSheet>
        </div>
    </div>
</template>

<script setup>
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { DataTable, Column, InputText, Button, IconField, InputIcon, Calendar } from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref, computed, reactive } from 'vue';
import { useOrdersStore } from '@/stores/orders';
import { useProductsStore } from '@/stores/products';
import { Icon } from "@iconify/vue";
import { formatPrice } from '@/utility/utility';   

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const dateFilter = ref(null);

const ordersStore = useOrdersStore();
const productsStore = useProductsStore();
const bottomSheet = ref(null);
const savingOrder = ref(false);
const deletingOrder = ref(false);
const isEditMode = ref(false);
const editingOrderId = ref(null);
const orderForm = reactive({
    name: '',
    description: '',
    order_items: []
});

const orderTotal = computed(() => {
    return orderForm.order_items.reduce((total, item) => {
        const product = productsStore.products.find(p => p.product_id === item.product_id);
        return total + (product ? product.price_raw * item.quantity : 0);
    }, 0);
});

const filteredOrders = computed(() => {
    let orders = ordersStore.orders;

    if (dateFilter.value) {
        // Get the selected date as YYYY-MM-DD string
        const selectedDate = new Date(dateFilter.value);
        const selectedDateString = selectedDate.getFullYear() + '-' +
            String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' +
            String(selectedDate.getDate()).padStart(2, '0');

        orders = orders.filter(order => {
            // Parse order date and extract just the date part
            const orderDate = new Date(order.date);
            const orderDateString = orderDate.getFullYear() + '-' +
                String(orderDate.getMonth() + 1).padStart(2, '0') + '-' +
                String(orderDate.getDate()).padStart(2, '0');

            return orderDateString === selectedDateString;
        });
    }

    return orders;
});

const clearDateFilter = () => {
    dateFilter.value = null;
};

const deleteOrder = async () => {
    if (!isEditMode.value || !editingOrderId.value) return;

    try {
        deletingOrder.value = true;

        await ordersStore.deleteOrder(editingOrderId.value);

        // Reset form and close bottom sheet
        resetForm();
        isEditMode.value = false;
        editingOrderId.value = null;

        if (bottomSheet.value) {
            bottomSheet.value.close();
        }
    } catch (error) {
        console.error('Error deleting order:', error);
    } finally {
        deletingOrder.value = false;
    }
};

const openBottomSheet = () => {
    resetForm();
    isEditMode.value = false;
    editingOrderId.value = null;
    if (bottomSheet.value) {
        bottomSheet.value.open();
    }
};

const onRowClick = (event) => {
    const order = event.data;
    populateFormWithOrder(order);
    if (bottomSheet.value) {
        bottomSheet.value.open();
    }
};

const populateFormWithOrder = (order) => {
    isEditMode.value = true;
    editingOrderId.value = order.order_id;
    orderForm.name = order.name;
    orderForm.description = order.description;
    orderForm.order_items = order.order_items || [];
};

const resetForm = () => {
    orderForm.name = '';
    orderForm.description = '';
    orderForm.order_items = [];
};

const addProductToOrder = (product) => {
    const existingItem = orderForm.order_items.find(item => item.product_id === product.product_id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        orderForm.order_items.push({
            product_id: product.product_id,
            quantity: 1
        });
    }
};

const removeProductFromOrder = (productId) => {
    const itemIndex = orderForm.order_items.findIndex(item => item.product_id === productId);

    if (itemIndex !== -1) {
        const item = orderForm.order_items[itemIndex];
        if (item.quantity > 1) {
            item.quantity -= 1;
        } else {
            orderForm.order_items.splice(itemIndex, 1);
        }
    }
};

const getProductQuantity = (productId) => {
    const item = orderForm.order_items.find(item => item.product_id === productId);
    return item ? item.quantity : 0;
};

const saveOrder = async () => {
    try {
        savingOrder.value = true;

        const orderData = {
            name: orderForm.name,
            description: orderForm.description,
            value: orderTotal.value,
            order_items: orderForm.order_items
        };

        if (isEditMode.value && editingOrderId.value) {
            // Update existing order
            await ordersStore.updateOrder(editingOrderId.value, orderData);
        } else {
            // Create new order
            await ordersStore.addOrder(orderData);
        }

        // Reset form and close bottom sheet
        resetForm();
        isEditMode.value = false;
        editingOrderId.value = null;

        if (bottomSheet.value) {
            bottomSheet.value.close();
        }
    } catch (error) {
        console.error('Error saving order:', error);
    } finally {
        savingOrder.value = false;
    }
};

onMounted(() => {
    (async () => {
        await ordersStore.fetchOrders();
        await productsStore.fetchProducts();
    })();
});
</script>

<style scoped>
.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.filter-section {
    display: flex;
    align-items: center;
}

.product-list {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.product-item:hover {
    background-color: #f8f9fa;
}

.product-info {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-name {
    font-weight: 500;
    margin-bottom: 2px;
}

.product-price {
    font-size: 0.875rem;
    color: #666;
}

.product-quantity {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}
</style>