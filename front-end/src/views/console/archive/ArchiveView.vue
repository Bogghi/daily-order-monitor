<template>
    <div class="main">
        <div class="container">
            <h1>Archivio prodotti</h1>
            <div class="table">
                <DataTable :value="productsStore.products" showGridlines scrollable>
                    <template #header>
                        <div class="table-header">
                            <div></div>
                            <Button variant="text" @click="openBottomSheet">
                                <Icon icon="solar:add-square-linear" height="25" />
                            </Button>
                        </div>
                    </template>
                    <Column style="min-width: 200px" field="name" header="Nome"></Column>
                    <Column style="min-width: 150px" field="price" header="Prezzo"></Column>
                    <Column style="min-width: 100px" header="Cancella">
                        <template #body="slotProps">
                            <Button
                                variant="text"
                                severity="danger"
                                @click="deleteProduct(slotProps.data.product_id)"
                                :loading="deletingProductId === slotProps.data.product_id">
                                <Icon icon="solar:trash-bin-2-linear" height="20" />
                            </Button>
                        </template>
                    </Column>
                </DataTable>
            </div>

            <BottomSheet ref="bottomSheet">
                <h2 style="margin-bottom: 10px;">Nuovo prodotto</h2>
                <InputText type="text" style="margin-bottom: 10px;" v-model="productForm.name" variant="filled" placeholder="Nome prodotto" />
                <InputText type="number" style="margin-bottom: 10px;" v-model="productForm.price" variant="filled" placeholder="Prezzo (€)" step="0.01" />
                <Button @click="saveProduct" :loading="saving" style="width: 100%;">
                    Salva prodotto
                </Button>
            </BottomSheet>
        </div>
    </div>
</template>

<script setup>
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { DataTable, Column, Button, InputText } from 'primevue';
import { onMounted, ref, reactive } from 'vue';
import { useProductsStore } from '@/stores/products';
import { Icon } from "@iconify/vue";

const productsStore = useProductsStore();
const deletingProductId = ref(null);
const bottomSheet = ref(null);
const saving = ref(false);
const productForm = reactive({
    name: '',
    price: ''
});

const openBottomSheet = () => {
    if (bottomSheet.value) {
        bottomSheet.value.open();
    }
};

const saveProduct = async () => {
    try {
        saving.value = true;

        // Convert price from euros to cents (multiply by 100)
        const priceInCents = Math.round(parseFloat(productForm.price) * 100);

        const productData = {
            name: productForm.name,
            price: priceInCents
        };

        await productsStore.addProduct(productData);

        // Reset form and close bottom sheet
        productForm.name = '';
        productForm.price = '';
        if (bottomSheet.value) {
            bottomSheet.value.close();
        }
    } catch (error) {
        console.error('Error saving product:', error);
    } finally {
        saving.value = false;
    }
};

const deleteProduct = async (productId) => {
    deletingProductId.value = productId;
    await productsStore.deleteProduct(productId);
    deletingProductId.value = null;
};

onMounted(() => {
    (async () => {
        await productsStore.fetchProducts();
    })();
});
</script>