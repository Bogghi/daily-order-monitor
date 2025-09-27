<template>
    <div class="main">
        <div class="container">
            <h1>Lista ordini</h1>
            <div class="table">
                <DataTable :value="ordersStore.orders" v-model:filters="filters" showGridlines scrollable>
                    <template #header>
                        <div class="table-header">
                            <IconField>
                                <InputIcon>
                                    <Icon icon="solar:magnifer-linear" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Cerca ordini..." />
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

                <Button
                    @click="saveOrder"
                    :loading="savingOrder"
                    :disabled="orderForm.order_items.length === 0"
                    style="width: 100%;">
                    Salva ordine
                </Button>
            </BottomSheet>
        </div>
    </div>
</template>

<script setup>
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { DataTable, Column, InputText, Button, IconField, InputIcon } from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref, computed, reactive } from 'vue';
import { useOrdersStore } from '@/stores/orders';
import { useProductsStore } from '@/stores/products';
import { Icon } from "@iconify/vue";
import { formatPrice } from '@/utility/utility';   

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const ordersStore = useOrdersStore();
const productsStore = useProductsStore();
const bottomSheet = ref(null);
const savingOrder = ref(false);
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

const openBottomSheet = () => {
    if (bottomSheet.value) {
        bottomSheet.value.open();
    }
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

        await ordersStore.addOrder(orderData);

        // Reset form and close bottom sheet
        orderForm.name = '';
        orderForm.description = '';
        orderForm.order_items = [];

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