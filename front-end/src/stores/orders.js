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
                    value_raw: order.value,
                    order_items: order.order_items || []
                }
            });
            orders.value = normOrders;
        }

        return orders.value;
    };

    const addOrder = async (orderData) => {
        const response = await apiService.addOrder(orderData);

        if (response.order_id) {
            // Refresh the orders list after adding
            await fetchOrders();
        }

        return response;
    };

    const deleteOrder = async (orderId) => {
        const response = await apiService.deleteOrder(orderId);

        if (response.message) {
            // Remove the order from local state
            orders.value = orders.value.filter(order => order.order_id !== orderId);
        }

        return response;
    };

    const updateOrder = async (orderId, orderData) => {
        const response = await apiService.updateOrder(orderId, orderData);

        if (response.message) {
            // Refresh the orders list after updating
            await fetchOrders();
        }

        return response;
    };

    return {
        orders,
        fetchOrders,
        addOrder,
        deleteOrder,
        updateOrder
    };
});