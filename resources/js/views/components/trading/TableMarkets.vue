<template>
    <div class="w-full shadow border-b border-gray-200 sm:rounded-lg">
        <table class="w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
            <tr class="w-full table">
                <th scope="col" class="align-middle px-1 py-1 text-left text-xs font-bold w-1/4 text-gray-500 tracking-wider">
                    <div class="leading-loose inline-block">Symbol</div>
                </th>
                <th scope="col" class="align-middle px-1 py-1 text-left text-xs font-bold w-1/4 text-gray-500 tracking-wider">
                    <div class="leading-loose inline-block">Price</div>
                </th>
                <th scope="col" class="align-middle px-1 py-1 text-left text-xs font-bold w-1/4 text-gray-500 tracking-wider">
                    <div class="leading-loose inline-block">Price Change %</div>
                </th>
                <th scope="col" class="align-middle px-1 py-1 text-left text-xs font-bold w-1/4 text-gray-500 tracking-wider">
                    <div class="leading-loose inline-block">Quote Volume</div>
                </th>
            </tr>
            </thead>
            <tbody v-if="records && records.length && !$props.isLoading" class="bg-white divide-y divide-gray-200 h-48 block overflow-auto">
            <tr v-for="(record, i) in records" @click="selectRecord(record)" class="w-full table hover:bg-theme-800 hover:text-white">
                <td class="px-1 py-1 whitespace-nowrap text-xs  w-1/4">
                    {{ record.symbol }}
                </td>
                <td class="px-1 py-1 whitespace-nowrap text-xs  w-1/4 ">
                    {{ parseFloat(record.last_price).toFixed(2) }}
                </td>
                <td class="px-1 py-1 whitespace-nowrap text-xs  w-1/4 ">
                    {{ parseFloat(record.price_change_percent).toFixed(1) }}
                </td>
                <td class="px-1 py-1 whitespace-nowrap text-xs  w-1/4">
                    {{ parseFloat(record.quote_volume).toFixed(2) }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import {trans} from "@/helpers/i18n";
import { defineComponent } from "vue";
import Pager from "@/views/components/Pager";
import Spinner from "@/views/components/icons/Spinner";
import { toRaw } from 'vue';

export default defineComponent({
    components: {Spinner, Pager},
    emits: ['pageChanged', 'action', 'sort'],
    props: {
        id: {
            type: String,
            default: "",
        },
        records: {
            type: [Array, Object],
            default: [],
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, {emit}) {
        return {
            trans
        }
    },
    watch: {
        records(newData) {
            console.log('new records: ', newData);
        }
    },
    methods: {
        selectRecord(record) {
            this.$emit('record-selected', toRaw(record));
        }
    }
});
</script>
<style>
.sort-arrows {
    font-size: 1.2em;
    line-height: 0.7;
    width: 30px;
}

.sort-arrows i.fa {
    line-height: 0.1;
}

.sort-arrows .fa {
    font-size: 15px;
}
</style>
