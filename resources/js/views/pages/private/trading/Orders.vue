<template>
    <Page :is-loading="isLoading">
        <div class="w-1/2 m-auto text-left pt-10">
            <h1 class="text-6xl mb-4 font-bold text-gray-600">{{ trans('global.pages.orders') }}</h1>
            <JsonTreeView :data="JSON.stringify(orders)" :maxDepth="5" />
<!--            <div v-for="order in orders" :key="order.orderId">-->
<!--                {{ order }}-->
<!--            </div>-->
        </div>
    </Page>
</template>

<script>
import { defineComponent } from 'vue';
import { JsonTreeView } from "json-tree-view-vue3";
import { trans } from "@/helpers/i18n";
import Page from "@/views/layouts/Page";
import ExchangeService from "@/services/ExchangeService";

export default defineComponent({
    components: {
        Page, JsonTreeView
    },
    data() {
        return {
            orders: [],
            isLoading: false
        }
    },
    setup() {
        return {
            trans
        }
    },
    mounted() {
        const exchangeService = new ExchangeService();
        this.isLoading = true;
        console.log('loading orders...');
        // exchangeService.getOpenOrders('BTCUSDT')
        //     .then(orders => {
        //         this.orders = orders;
        //     })
        //     .catch(error => {
        //         console.error(error);
        //     })
        //     .finally(() => {
        //         this.isLoading = false;
        //     });

        exchangeService.getAllOrders('BTCUSDT', null, null, null, null)
            .then(orders => {
                this.orders = orders;
                this.isLoading = true;
                exchangeService.getOrder('BTCUSDT', null, 'web_7eebd45063734dc7bece75d3c7d78748')
                    .then(order => {
                        this.orders.push(order);
                    })
                    .catch(error => {
                        console.error(error);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            })
            .catch(error => {
                console.error(error);
            })
            .finally(() => {
                this.isLoading = false;
            });
    },
});
</script>
