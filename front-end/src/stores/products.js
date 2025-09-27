import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiService from '@/services/api'
import { formatPrice } from '@/utility/utility'

export const useProductsStore = defineStore('products', () => {
    const products = ref([]);

    const fetchProducts = async () => {
        const response = await apiService.fetchProducts();

        if (response.products) {
            let normProducts = response.products.map(product => {
                return {
                    product_id: product.product_id,
                    name: product.name,
                    price: formatPrice(product.price),
                    price_raw: product.price
                }
            });
            products.value = normProducts;
        }

        return products.value;
    };

    const addProduct = async (productData) => {
        const response = await apiService.addProduct(productData);

        if (response.product_id) {
            // Refresh the products list after adding
            await fetchProducts();
        }

        return response;
    };

    const deleteProduct = async (productId) => {
        const response = await apiService.deleteProduct(productId);

        if (response.message) {
            // Remove the product from local state
            products.value = products.value.filter(product => product.product_id !== productId);
        }

        return response;
    };

    return {
        products,
        fetchProducts,
        addProduct,
        deleteProduct
    };
});