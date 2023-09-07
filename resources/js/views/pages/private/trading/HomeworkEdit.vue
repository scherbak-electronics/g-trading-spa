<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <Form id="edit-homework" @submit.prevent="onSubmit">
                <Dropdown class="mb-4" name="symbol" v-model="form.symbol" :label="trans('trading.labels.symbol')" :server="'trading/exchange/symbols'" :server-per-page="15" :required="true" />
                <Dropdown class="mb-4" name="timeframe" v-model="form.timeframe" :label="trans('trading.labels.timeframe')" :options="timeframeSelectOptions" :required="true" />
                <Dropdown class="mb-4" name="strategy" v-model="form.strategy" :label="trans('trading.labels.strategy')" :options="strategySelectOptions" :required="true" />
                <Dropdown class="mb-4" name="direction" v-model="form.direction" :label="trans('trading.labels.direction')" :options="directionSelectOptions" :required="true" />
                <TextInput class="mb-4" name="title" v-model="form.title" :label="trans('global.labels.title')" type="text" :required="false"/>
                <TextInput class="mb-4" name="description" v-model="form.description" :label="trans('global.labels.description')" type="text" :required="false"/>
            </Form>
        </Panel>
    </Page>
</template>

<script>
import {computed, defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {fillObject, reduceProperties, fillFormAndDropdownValues} from "@/helpers/data"
import {useRoute} from "vue-router";
import {useAuthStore} from "@/stores/auth";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import HomeworkService from "@/services/HomeworkService";

export default defineComponent({
    components: {
        Form,
        FileInput,
        Panel,
        Alert,
        Dropdown,
        TextInput,
        Button,
        Page
    },
    setup() {
        const {user} = useAuthStore();
        const route = useRoute();
        const form = reactive({
            symbol: {},
            timeframe: {},
            strategy: {},
            direction: {},
            title: '',
            description: '',
        });

        const page = reactive({
            id: 'edit_homework',
            title: trans('global.pages.users_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.users'),
                    to: '/page/homework',
                },
                {
                    name: trans('global.pages.users_edit'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: '/page/homework',
                    theme: 'outline',
                },
                {
                    id: 'submit',
                    name: trans('global.buttons.update'),
                    icon: "fa fa-save",
                    type: 'submit'
                }
            ]
        });

        const service = new HomeworkService();

        onBeforeMount(() => {
            service.edit(route.params.id).then((response) => {
                fillFormAndDropdownValues(form, response.data.model, ['symbol', 'timeframe', 'strategy', 'direction'], ['symbol']);
                page.loading = false;
            })
        });

        function onAction(data) {
            switch(data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            let propsToReduce = ['symbol', 'timeframe', 'strategy', 'direction'];
            service.handleUpdate('edit-homework', route.params.id, reduceProperties(form, propsToReduce, 'id'));
            return false;
        }

        const timeframeSelectOptions = computed(() => {
            let val = [];
            window.AppConfig.trading.timeframes.forEach((item) => {
                val.push({id: item, title: trans('trading.labels.options.timeframe.' + item)});
            });
            return val;
        });

        const strategySelectOptions = computed(() => {
            let val = [];
            window.AppConfig.trading.strategies.forEach((item) => {
                val.push({id: item, title: trans('trading.labels.options.strategy.' + item)});
            });
            return val;
        });

        const directionSelectOptions = computed(() => {
            let val = [];
            val.push({id: 'long', title: trans('trading.labels.options.direction.long')});
            val.push({id: 'short', title: trans('trading.labels.options.direction.short')});
            return val;
        });

        return {
            trans,
            user,
            form,
            onSubmit,
            onAction,
            page,
            service,
            timeframeSelectOptions,
            strategySelectOptions,
            directionSelectOptions
        }
    }
})
</script>

<style scoped>

</style>
