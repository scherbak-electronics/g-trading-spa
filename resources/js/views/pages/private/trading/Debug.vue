<template>
    <Page>
        <div class="w-full h-full text-left">
            <h1 class="text-2xl mb-1 font-bold text-gray-600">{{ 'Developer Page' }}</h1>
            <div class="w-full h-fit text-left pt-3">
                <Button @click="getKlineData" :label="'Get Kline Data'"/>
                <Button @click="getExchangeInfo" class="ml-4" :label="'Get Exchange Info'"/>
                <Button @click="getAllSymbols" class="ml-4" :label="'Get All Symbols'"/>
            </div>
            <div>{{ resp }}</div>
        </div>
    </Page>
</template>

<script>

import {computed, defineComponent} from "vue";
import { ref } from 'vue';
import {trans} from "@/helpers/i18n";
import Page from "@/views/layouts/Page";
import Button from "@/views/components/input/Button";
import {toUrl} from "@/helpers/routing";

import ExchangeService from "@/services/ExchangeService";
import Alert from "@/views/components/Alert";
import Spinner from "@/views/components/icons/Spinner";
import Dropdown from "@/views/components/input/Dropdown";
import TextInput from "@/views/components/input/TextInput";
import FiltersCol from "@/views/components/filters/FiltersCol";
import FiltersRow from "@/views/components/filters/FiltersRow";
import Filters from "@/views/components/filters/Filters";
import Table from "@/views/components/Table";
import Avatar from "@/views/components/icons/Avatar";

const exchangeService = new ExchangeService();

export default defineComponent({
    name: "Dev",
    components: {Alert, Button, Spinner, Page},
    props: {
        id: {
            type: String,
            default: "",
        },
        title: {
            type: String,
            default: "",
        },
        breadcrumbs: {
            type: Array,
            default: []
        },
        actions: {
            type: Array,
            default: []
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
        response: {
            type: String,
            default: ""
        }
    },
    setup(props, {emit}) {
        const resp = ref("");
        function getKlineData() {
            const nowTimestamp = Date.now();
            // Timestamp for 90 days ago
            const daysToMilliseconds = 90 * 24 * 60 * 60 * 1000; // 90 days in milliseconds
            const pastTimestamp = nowTimestamp - daysToMilliseconds;
            // Make the request to Laravel controller
            exchangeService.getKlineData('BTCBUSD', '1d', pastTimestamp, nowTimestamp)
                .then(response => {
                    resp.value = JSON.stringify(response.data, null, 2); // Format JSON response
                })
                .catch(error => {
                    console.error(error);
                });
        }

        function getExchangeInfo() {
            exchangeService.getExchangeInfo(undefined, undefined).then(response => {
                    resp.value = JSON.stringify(response.data, null, 2); // Format JSON response
                })
                .catch(error => {
                    console.error(error);
                    resp.value = error;
                });
        }

        function getAllSymbols() {
            exchangeService.getAllSymbols('BUSD', undefined).then(response => {
                resp.value = JSON.stringify(response.data, null, 2); // Format JSON response
            })
                .catch(error => {
                    console.error(error);
                    resp.value = error;
                });
        }

        return {
            resp,
            trans,
            getKlineData,
            getExchangeInfo,
            getAllSymbols
        }
    }
});

</script>
<style scoped>
.chart-container {
    height: calc(100% - 6.0em);
}
</style>
