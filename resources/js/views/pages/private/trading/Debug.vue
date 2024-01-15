<template>
    <Page>
        <div class="w-full h-full text-left">
            <h1 class="text-2xl mb-1 font-bold text-gray-600">{{ 'Developer Page' }}</h1>
            <div class="w-full h-fit text-left pt-3">
                <Button @click="getKlineData" :label="'Get Kline Data'"/>
                <Button @click="getExchangeInfo" class="ml-4" :label="'Get Exchange Info'"/>
                <Button @click="getAllSymbols" class="ml-4" :label="'Get All Symbols'"/>
            </div>
            <div><pre>{{ Date.now() }}</pre></div>
            <div><pre>{{ resp }}</pre></div>
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

        return {
            resp,
            trans,
        }
    }
});

</script>
<style scoped>
.chart-container {
    height: calc(100% - 6.0em);
}
</style>
