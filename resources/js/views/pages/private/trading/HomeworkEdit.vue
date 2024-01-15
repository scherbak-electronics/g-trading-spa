<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <Form id="edit-homework" @submit.prevent="onSubmit">
                <DropdownDefault v-model="form.symbol" name="'symbol'" :label="trans('trading.labels.symbol')" :server="'trading/exchange/symbols'"/>
                <DropdownDefault v-model="form.timeframe" name="'timeframe'" :label="trans('trading.labels.timeframe')" :options="getTimeframeOptions()"/>
                <DropdownDefault v-model="form.strategy" name="'strategy'" :label="trans('trading.labels.strategy')" :options="getStrategyOptions()"/>
                <DropdownDefault v-model="form.direction" name="'direction'" :label="trans('trading.labels.direction')" :options="getDirectionOptions()"/>
                <TextInput class="mb-4" name="title" v-model="form.title" :label="trans('global.labels.title')" type="text" :required="false"/>
                <TextInput class="mb-4" name="description" v-model="form.description" :label="trans('global.labels.description')" type="text" :required="false"/>
            </Form>
        </Panel>
    </Page>
</template>

<script>
import {defineComponent, onBeforeMount, reactive} from "vue";
import {trans} from "@/helpers/i18n";
import {fillObject, getTimeframeOptions, getDirectionOptions, getStrategyOptions} from "@/helpers/data"
import {useRoute} from "vue-router";
import {useAuthStore} from "@/stores/auth";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import DropdownDefault from "@/views/components/trading/DropdownDefault";
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
        DropdownDefault,
        TextInput,
        Button,
        Page
    },
    setup() {
        const {user} = useAuthStore();
        const route = useRoute();
        const form = reactive({
            symbol: '',
            timeframe: '',
            strategy: '',
            direction: '',
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
                fillObject(form, response.data.model);
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
            service.handleUpdate('edit-homework', route.params.id, form);
            return false;
        }

        return {
            trans,
            user,
            form,
            onSubmit,
            onAction,
            page,
            service, getTimeframeOptions, getDirectionOptions, getStrategyOptions
        }
    }
})
</script>

<style scoped>

</style>
