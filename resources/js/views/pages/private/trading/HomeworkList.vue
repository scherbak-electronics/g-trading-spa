<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

        <template #filters v-if="page.toggleFilters">
            <Filters @clear="onFiltersClear">
                <FiltersRow>
                    <FiltersCol>
                        <Dropdown name="symbol" v-model="mainQuery.filters.symbol.value" :label="trans('trading.labels.symbol')" :server="'exchange/symbols'"/>
                    </FiltersCol>
                    <FiltersCol>
                        <Dropdown name="timeframe" v-model="mainQuery.filters.timeframe.value" :label="trans('trading.labels.timeframe')" :server="'exchange/timeframes'"/>
                    </FiltersCol>
                    <FiltersCol>
                        <Dropdown name="strategy" v-model="mainQuery.filters.strategy.value" :label="trans('trading.labels.strategy')" :server="'trading/strategies'"/>
                    </FiltersCol>
                    <FiltersCol>
                        <Dropdown name="direction" v-model="mainQuery.filters.direction.value" :label="trans('trading.labels.direction')"/>
                    </FiltersCol>
                </FiltersRow>
            </Filters>
        </template>

        <template #default>
            <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions" :records="table.records" :pagination="table.pagination" :is-loading="table.loading" @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort">
                <template v-slot:content-id="props">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="text-sm font-medium text-gray-900">
                                {{ props.item.symbol }}
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ props.item.timeframe }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ trans('users.labels.id') + ': ' + props.item.id }}
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:content-strategy="props">
                    {{ props.item.strategy }}
                </template>
                <template v-slot:content-direction="props">
                    {{ props.item.direction }}
                </template>
            </Table>
        </template>
    </Page>
</template>

<script>

import {trans} from "@/helpers/i18n";
import UserService from "@/services/UserService";
import {watch, onMounted, defineComponent, reactive, ref} from 'vue';
import {getResponseError, prepareQuery} from "@/helpers/api";
import {toUrl} from "@/helpers/routing";
import {useAlertStore} from "@/stores";
import alertHelpers from "@/helpers/alert";
import Page from "@/views/layouts/Page";
import Table from "@/views/components/Table";
import Avatar from "@/views/components/icons/Avatar";
import Filters from "@/views/components/filters/Filters";
import FiltersRow from "@/views/components/filters/FiltersRow";
import FiltersCol from "@/views/components/filters/FiltersCol";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import HomeworkService from "@/services/HomeworkService";

export default defineComponent({
    components: {
        Dropdown,
        TextInput,
        FiltersCol,
        FiltersRow,
        Filters,
        Page,
        Table,
        Avatar
    },
    setup() {
        const service = new HomeworkService();
        const alertStore = useAlertStore();
        const mainQuery = reactive({
            page: 1,
            search: '',
            sort: '',
            filters: {
                symbol: {
                    value: '',
                    comparison: '='
                },
                timeframe: {
                    value: '',
                    comparison: '='
                },
                strategy: {
                    value: '',
                    comparison: '='
                },
                direction: {
                    value: '',
                    comparison: '='
                }
            }
        });

        const page = reactive({
            id: 'list_homework',
            title: trans('global.pages.homework'),
            breadcrumbs: [
                {
                    name: trans('global.pages.homework'),
                    to: '/page/homework',
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'filters',
                    name: trans('global.buttons.filters'),
                    icon: "fa fa-filter",
                    theme: 'outline',
                },
                {
                    id: 'new',
                    name: trans('global.buttons.add_new'),
                    icon: "fa fa-plus",
                    to: '/page/homework/create'
                }
            ],
            toggleFilters: false,
        });

        const table = reactive({
            headers: {
                id: trans('users.labels.id_pound'),
                symbol: trans('trading.labels.symbol'),
                timeframe: trans('trading.labels.timeframe'),
                strategy: trans('trading.labels.strategy'),
                direction: trans('trading.labels.direction'),
            },
            sorting: {
                symbol: true,
                strategy: true
            },
            pagination: {
                meta: null,
                links: null,
            },
            actions: {
                edit: {
                    id: 'edit',
                    name: trans('global.actions.edit'),
                    icon: "fa fa-edit",
                    showName: false,
                    to: '/page/homework/{id}/edit'
                },
                delete: {
                    id: 'delete',
                    name: trans('global.actions.delete'),
                    icon: "fa fa-trash",
                    showName: false,
                    danger: true,
                }
            },
            loading: false,
            records: null
        })

        function onTableSort(params) {
            mainQuery.sort = params;
        }

        function onTablePageChange(page) {
            mainQuery.page = page;
        }

        function onTableAction(params) {
            switch (params.action.id) {
                case 'delete':
                    alertHelpers.confirmDanger(function () {
                        service.delete(params.item.id).then(function (response) {
                            fetchPage(mainQuery);
                        });
                    })
                    break;
            }
        }

        function onPageAction(params) {
            switch (params.action.id) {
                case 'filters':
                    page.toggleFilters = !page.toggleFilters;
                    break;
            }
        }

        function onFiltersClear() {
            let clonedFilters = mainQuery.filters;
            for(let key in clonedFilters) {
                clonedFilters[key].value = '';
            }
            mainQuery.filters = clonedFilters;
        }

        function fetchPage(params) {
            table.records = [];
            table.loading = true;
            let query = prepareQuery(params);
            service
                .index(query)
                .then((response) => {
                    table.records = response.data.data;
                    table.pagination.meta = response.data.meta;
                    table.pagination.links = response.data.links;
                    table.loading = false;
                })
                .catch((error) => {
                    alertStore.error(getResponseError(error));
                    table.loading = false;
                });
        }

        watch(mainQuery, (newTableState) => {
            fetchPage(mainQuery);
        });

        onMounted(() => {
            fetchPage(mainQuery);
        });

        return {
            trans,
            page,
            table,
            onTablePageChange,
            onTableAction,
            onTableSort,
            onPageAction,
            onFiltersClear,
            mainQuery
        }

    },
});
</script>
