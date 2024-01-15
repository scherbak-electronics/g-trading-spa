<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
        <div class="w-full sm:w-80 ml-auto mr-auto">
        <Panel>
            <Form id="create-homework" @submit.prevent="onSubmit">
                <DropdownDefault name="symbol" v-model="form.symbol" :label="trans('trading.labels.symbol')" :server="'trading/exchange/symbols'" :required="true"/>
                <DropdownDefault name="timeframe" v-model="form.timeframe" :label="trans('trading.labels.timeframe')" :options="getTimeframeOptions()" :required="true" />
                <DropdownDefault name="strategy" v-model="form.strategy" :label="trans('trading.labels.strategy')" :options="getStrategyOptions()" :required="true" />
                <DropdownDefault name="direction" v-model="form.direction" :label="trans('trading.labels.direction')" :options="getDirectionOptions()" :required="true"/>
                <TextInput class="mb-4" name="title" v-model="form.title" :label="trans('global.labels.title')" type="text" :required="false"/>
                <TextInput class="mb-4" name="description" v-model="form.description" :label="trans('global.labels.description')" type="text" :required="false"/>
            </Form>
        </Panel>
        </div>
    </Page>
</template>

<script>
import {defineComponent, reactive} from "vue";
import {trans} from "@/helpers/i18n";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import DropdownDefault from "@/views/components/trading/DropdownDefault";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import {clearObject, getTimeframeOptions, getDirectionOptions, getStrategyOptions} from "@/helpers/data"
import Form from "@/views/components/Form";
import HomeworkService from "@/services/HomeworkService";
import router from "@/router";

export default defineComponent({
    components: {Form, Panel, Alert, DropdownDefault, TextInput, Button, Page},
    setup() {
        const form = reactive({
            symbol: '',
            timeframe: '',
            strategy: '',
            direction: '',
            title: '',
            description: '',
        });

        const service = new HomeworkService();

        const page = reactive({
            id: 'create_homework',
            title: trans('global.pages.homework_create'),
            filters: false,
            breadcrumbs: [
                {
                    name: trans('global.pages.homework_list'),
                    to: '/page/homework',

                },
                {
                    name: trans('global.pages.homework_create'),
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
                    name: trans('global.buttons.save'),
                    icon: "fa fa-save",
                    type: 'submit',
                }
            ]
        });

        function onAction(data) {
            switch(data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            service.handleCreate('create-homework', form).then(() => {
                clearObject(form);
                router.push("/page/homework");
            })
            return false;
        }

        return {
            trans,
            form,
            page,
            onSubmit,
            onAction,
            getTimeframeOptions, getDirectionOptions, getStrategyOptions,
            service
        }
    }
})
</script>

<style scoped>

</style>
