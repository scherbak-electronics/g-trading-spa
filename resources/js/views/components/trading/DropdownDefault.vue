<template>
    <Dropdown class="mb-4" :name="$props.name" v-model="value" :label="$props.label" :options="$props.options" :server="$props.server" :server-per-page="15" :required="true" />
</template>

<script>
import {computed, defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import Dropdown from "@/views/components/input/Dropdown";
export default defineComponent({
    components: {
        Dropdown
    },
    props: {
        modelValue: {
            type: String
        },
        name: {
            type: String,
            required: true,
        },
        // trans('trading.labels.symbol')
        label: {
            type: String,
            default: "",
        },
        // :server="'trading/exchange/symbols'"
        server: {
            type: String,
            default: null,
        },
        options: {
            type: Array,
            default: null,
        }
    },
    emits: ['update:modelValue'],
    setup(props, {emit}) {
        const value = computed({
            get() {
                console.log('get: ',  props.modelValue);
                return {
                    id: props.modelValue,
                    title: props.modelValue
                };
            },
            set(newValue) {
                console.log('set: ', newValue.id);
                emit("update:modelValue", newValue.id);
            },
        })
        return {
            value,
            trans,
        }
    }
})
</script>

<style scoped>

</style>
