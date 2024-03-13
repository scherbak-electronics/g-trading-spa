<template>
    <Page :is-loading="pageLoading">
        <div class="w-full h-full text-left flex flex-col">
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div>
                    <div class="flex front-panel top-summary mb-2 p-2">
                        <div class="flex-grow symbol">
                            <h4>session {{ sessionId }}</h4>
                            <h1>{{ stateExchange.symbol }}</h1>
                        </div>
                        <div class="flex-grow">
                            <h4>price</h4>
                            <p class="price oswald-normal-400">{{ toFixed(stateExchange.lastPrice) }}</p>
                        </div>
                        <div class="flex-grow">
                            <h4>amount</h4>
                            <p class="price oswald-normal-400">{{ toFixed(session?.total_investment || 0) }}</p>
                        </div>
                        <div class="flex-grow">
                            <h4>profit</h4>
                            <p class="price oswald-normal-400">{{ toFixed(session?.total_profit || 0) }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <Button @click="onClickSetEntryPoint" class="mb-2 mr-2" :label="'Set Entry Point'"/>
                        <Button @click="onClickFindLevel" class="mb-2 mr-2" :label="'Find Level'"/>
                        <Button @click="onClickFindLevel2" class="mb-2 mr-2" :label="'Find Level short'"/>
                        <Button @click="onClickHighlightBars" class="mb-2 mr-2" :label="'Highlight Bars'"/>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-4">
                <div class="col-span-3">
                    <ButtonsTimeframe v-model="stateExchange.interval" @update:modelValue="onTimeframeSelect"/>
                    <div class="chart-container flex-grow" ref="chartContainer">
                        <LWChart v-if="!chartLoading" @onLevelChange="onLevelChange" @onLevelSelected="onLevelSelected" :data="stateExchange.topChartData" ref="lwChart" :initialHeight="400" :symbol="stateExchange.symbol"/>
                        <template v-if="chartLoading">
                            <div class="pt-10 pb-6 text-center">
                                <Spinner/>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="ml-2 mb-2 p-2 front-panel session-info">
                        <ul v-if="sessionLoading" class="list-none space-y-4">
                            <li class="flex items-center space-x-4">
                                <Spinner/>
                            </li>
                        </ul>
                        <ul v-if="!sessionLoading" class="list-none space-y-4">
                            <li class="flex items-center space-x-4 mb-2 hoverable" :class="{ 'highlight': activeLevelName === 'main_level_price' }">
                                <div class="space-y-2">
                                    <h2>main level</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(session.main_level_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeLevelName !== 'main_level_price'" @click="onClickEditLevel('main_level_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeLevelName === 'main_level_price'">
                                    Move cursor to the chart and click to select price level.
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable" :class="{ 'highlight': activeLevelName === 'entry_point_price' }">
                                <div class="space-y-2">
                                    <h2>entry point</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(session.entry_point_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeLevelName !== 'entry_point_price'" @click="onClickEditLevel('entry_point_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeLevelName === 'entry_point_price'">
                                    Move cursor to the chart and click to select price level.
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>take profit</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(session.take_profit_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeLevelName !== 'take_profit_price'" @click="onClickEditLevel('take_profit_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeLevelName === 'take_profit_price'">
                                    Move cursor to the chart and click to select price level.
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>stop loss</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(session.stop_loss_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeLevelName !== 'stop_loss_price'" @click="onClickEditLevel('stop_loss_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeLevelName === 'stop_loss_price'">
                                    Move cursor to the chart and click to select price level.
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>stop loss timeout</h2>
                                    <p class="time oswald-normal-400">{{ session.stop_loss_safe_time || 0 }}</p>
                                </div>
                                <button class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>take profit timeout</h2>
                                    <p class="time oswald-normal-400">{{ session.take_profit_timeout || 0 }}</p>
                                </div>
                                <button class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </Page>
</template>

<script setup>
import {ref, onMounted, onBeforeUnmount, reactive} from 'vue';
import LWChart from "@/views/components/trading/Chart";
import Page from "@/views/layouts/Page";
import Button from "@/views/components/input/Button";

import Spinner from "@/views/components/icons/Spinner";
import { useExchangeStateStore } from "@/stores/exchange";
import ButtonsTimeframe from "@/views/components/trading/ButtonsTimeframe.vue";
import ExchangeService from "@/services/ExchangeService";
import SessionService from "@/services/SessionService";
import localStorageService from "@/services/LocalStorageService";
import { useRoute } from 'vue-router';
import {LineStyle} from "lightweight-charts";
import {getMinPriceDecimalPlaces} from "@/helpers/chart";

let route = useRoute();
let sessionId = ref(route.params.id);
let session;
const stateExchange = useExchangeStateStore();
const lwChart = ref();
const chartContainer = ref(null);
const pageLoading = ref(false);
const chartLoading = ref(false);
const sessionLoading = ref(true);
let test;

let lastBarUpdateInterval;
const lastBarUpdateTime = 10000;
let roundDecimalPlaces = undefined;
const activeLevelName = ref('');

function toFixed(value) {
    if (value) {
        const number = parseFloat(value);
        return isNaN(number) ? '' : number.toFixed(roundDecimalPlaces);
    }
    return '';
}

onMounted(() => {
    loadSession()
        .then(() => {
            lastBarUpdateInterval = setInterval(() => {
                updateLastBar();
            }, lastBarUpdateTime);
        });
});

onBeforeUnmount(() => {
    if (lastBarUpdateInterval) {
        clearInterval(lastBarUpdateInterval);
    }
});

const loadSession = async () => {
    const sessionService = new SessionService();
    const exchangeService = new ExchangeService();
    const TEST_SESSION_ID = 1;
    const DEFAULT_INTERVAL = '1d';
    pageLoading.value = true;
    chartLoading.value = true;
    const loadSessionError = () => {
        console.log('loadSession error');
        chartLoading.value = false;
        pageLoading.value = false;
    };
    let loadedSession = await sessionService.getSession(sessionId.value ?? TEST_SESSION_ID);
    if (!loadedSession) {
        loadSessionError();
        return;
    }
    let result = await exchangeService.updateExchangeInfo();
    if (result !== 'ok') {
        loadSessionError();
        return;
    }

    let klineData = await exchangeService.getKlineData(loadedSession.symbol, DEFAULT_INTERVAL);
    if (!klineData) {
        loadSessionError();
        return;
    }
    stateExchange.topChartData = klineData;
    chartLoading.value = false;
    let price = await exchangeService.getLastPrice(loadedSession.symbol);
    if (!price) {
        loadSessionError();
        return;
    }
    let minPrice = await exchangeService.getSymbolMinPrice(loadedSession.symbol);
    if (!minPrice) {
        loadSessionError();
        return;
    }
    roundDecimalPlaces = getMinPriceDecimalPlaces(minPrice);
    stateExchange.lastPrice = price;
    stateExchange.symbol = loadedSession.symbol;
    chartLoading.value = false;
    session = reactive(loadedSession);
    sessionLoading.value = false;
    pageLoading.value = false;
};


const onTimeframeSelect = (value) => {
    console.log('onTimeframeSelect value ', value);
    stateExchange.interval = value;
    let localStorageMarkets = localStorageService.getItem('markets');
    localStorageMarkets.interval = stateExchange.interval;
    localStorageService.setItem('markets', localStorageMarkets);
    reloadKlineData();
};

const reloadKlineData = () => {
    chartLoading.value = true;
    const exchangeService = new ExchangeService();
    exchangeService.getKlineData(stateExchange.symbol, stateExchange.interval)
        .then((klineData) => {
            stateExchange.topChartData = klineData;
            chartLoading.value = false;
        })
        .catch(() => {
            console.log('loadTradingData error')
        })
        .finally(() => {
            chartLoading.value = false;
        });
};

const onClickSetEntryPoint = () => {
    lwChart.value.beginLevelSelection('entry_point_price', {
        color: '#009900'
    });
};

const onClickEditLevel = (levelName) => {
    lwChart.value.beginLevelSelection(levelName);
    activeLevelName.value = levelName;
};

const onLevelChange = (level) => {
    session[level.name] = level.price;
};

const onLevelSelected = (level) => {
    activeLevelName.value = '';
};

const onClickFindLevel = () => {
    lwChart.value.beginLevelSelection('find_level', {
        color: '#00eebb',
        lineStyle: LineStyle.LargeDashed
    });
};

const onClickFindLevel2 = () => {

};

const onClickHighlightBars = () => {
    updateLastBar();
};


const updateLastBar = () => {
    const exchangeService = new ExchangeService();
    exchangeService.updateLastBar(stateExchange.symbol, stateExchange.interval)
        .then(lastBar => {
            if (lwChart?.value) {
                lwChart.value.updateLastBar(lastBar);
            }
            stateExchange.lastPrice = lastBar.close;
        })
        .finally(() => {});
};



</script>
<style scoped>
.chart-container {
    box-sizing: content-box;
}
.front-panel {
    background-color: #1f1f1fe8;
    border-radius: 10px;
}
.front-panel h2,
.front-panel h1,
.front-panel h4 {
    color: #808386;
}

.front-panel .price,
.front-panel .time {
    font-size: x-large;
    color: #5188bd;
    margin: 0;
}
.top-summary h4 {
    font-size: x-small;
}
.top-summary h1 {
    font-size: xxx-large;
}
.change-24h p {
    font-size: x-large;
}
.session-info h2 {
    font-size: small;
}
.session-info .price {
    font-size: x-large;
}
.hoverable:hover {
    background-color: #3f3f3f; /* Change to the desired hover color */
}
.hoverable:hover .edit-btn {
    opacity: 1;
}
.edit-btn {
    opacity: 0;
    transition: opacity .2s ease-in-out;
}
.highlight h2 {
    color: #84c0f6;
}
.highlight .price {
    color: #7dbffc;
}
.tooltip-text {
    color: #84c0f6;
}
</style>
