import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiService from '@/services/api'
import { formatPrice } from '@/utility/utility'

export const useOrdersStore = defineStore('orders', () => {
    const orders = ref([]);

    const fetchOrders = async () => {
        const response = await apiService.fetchOrders();

        if(response.data.orders) {
            let normOrders = response.data.orders.map(order => {
                return {
                    order_id: order.order_id,
                    name: order.name,
                    description: order.description,
                    date: order.order_date,
                    value: formatPrice(order.value),
                }
            });
            orders.value = normOrders;
        }

        return orders.value;
    };

    return { orders, fetchOrders };
});