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
                            <p class="price oswald-normal-400">{{ toFixed(stateSession.total_investment) }}</p>
                        </div>
                        <div class="flex-grow">
                            <h4>profit</h4>
                            <p class="price oswald-normal-400">{{ toFixed(stateSession.total_profit) }}</p>
                        </div>
                        <div class="flex-grow">
                            <h4>state</h4>
                            <p class="price oswald-normal-400">{{ stateSession.state }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <Button @click="onClickStartStopSession" class="mb-2 mr-2" :label="stateSession.isRunning ? 'Stop' : 'Run'"/>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-4">
                <div class="col-span-3">
                    <ButtonsTimeframe v-model="stateExchange.interval" @update:modelValue="onTimeframeSelect"/>
                    <div class="chart-container flex-grow" ref="chartContainer">
                        <LWChart v-if="!chartLoading" @onLevelChange="onLevelChange" @onLevelSelected="onLevelSelected" @onChartReady="onChartReady" :data="stateExchange.topChartData" ref="lwChart" :initialHeight="500" :symbol="stateExchange.symbol"/>
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
                            <li class="flex items-center space-x-4 mb-2 hoverable" :class="{ 'highlight': activeEditFieldName === 'main_level_price' }">
                                <div class="space-y-2">
                                    <h2>main level</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(stateSession.main_level_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeEditFieldName !== 'main_level_price'" @click="onClickEditLevel('main_level_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeEditFieldName === 'main_level_price'">
                                    Move cursor to the chart and click to select price level or <button @click="onClickCancelEdit()" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                    Cancel
                                </button>
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable" :class="{ 'highlight': activeEditFieldName === 'entry_point_price' }">
                                <div class="space-y-2">
                                    <h2>entry point</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(stateSession.entry_point_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeEditFieldName !== 'entry_point_price'" @click="onClickEditLevel('entry_point_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeEditFieldName === 'entry_point_price'">
                                    Move cursor to the chart and click to select price level or <button @click="onClickCancelEdit()" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                    Cancel
                                </button>
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>take profit</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(stateSession.take_profit_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeEditFieldName !== 'take_profit_price'" @click="onClickEditLevel('take_profit_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeEditFieldName === 'take_profit_price'">
                                    Move cursor to the chart and click to select price level or <button @click="onClickCancelEdit()" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                    Cancel
                                </button>
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>stop loss</h2>
                                    <p class="price oswald-normal-400">{{ toFixed(stateSession.stop_loss_price) || 0.00 }}</p>
                                </div>
                                <button v-if="activeEditFieldName !== 'stop_loss_price'" @click="onClickEditLevel('stop_loss_price')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeEditFieldName === 'stop_loss_price'">
                                    Move cursor to the chart and click to select price level or <button @click="onClickCancelEdit()" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                    Cancel
                                </button>
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>stop loss timeout</h2>
                                    <p class="time oswald-normal-400">{{ stateSession.stop_loss_safe_time || 0 }}</p>
                                </div>
                                <button v-if="activeEditFieldName !== 'stop_loss_safe_time'" @click="onClickEditField('stop_loss_safe_time')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeEditFieldName === 'stop_loss_safe_time'">
                                    <TextInput class="mb-4" v-model="stateSession.stop_loss_safe_time" type="text" :required="false" name="stop_loss_safe_time"/>
                                    <button @click="onClickConfirmFieldEdit()" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                        Confirm
                                    </button>
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>take profit timeout</h2>
                                    <p class="time oswald-normal-400">{{ stateSession.take_profit_timeout || 0 }}</p>
                                </div>
                                <button v-if="activeEditFieldName !== 'take_profit_timeout'" @click="onClickEditField('take_profit_timeout')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeEditFieldName === 'take_profit_timeout'">
                                    <TextInput class="mb-4" v-model="stateSession.take_profit_timeout" type="text" :required="false" name="take_profit_timeout"/>
                                    <button @click="onClickConfirmFieldEdit()" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                        Confirm
                                    </button>
                                </div>
                            </li>
                            <li class="flex items-center space-x-4 mb-2 hoverable">
                                <div class="space-y-2">
                                    <h2>amount USDT</h2>
                                    <p class="time oswald-normal-400">{{ toFixed(stateSession.total_investment) || 0.00 }}</p>
                                </div>
                                <button v-if="activeEditFieldName !== 'total_investment'" @click="onClickEditField('total_investment')" class="edit-btn bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Edit
                                </button>
                                <div class="tooltip-text" v-if="activeEditFieldName === 'total_investment'">
                                    <TextInput class="mb-4" v-model="stateSession.total_investment" type="text" :required="false" name="total_investment"/>
                                    <button @click="onClickConfirmFieldEdit()" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                        Confirm
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </Page>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import LWChart from "@/views/components/trading/Chart";
import Page from "@/views/layouts/Page";
import Button from "@/views/components/input/Button";
import Spinner from "@/views/components/icons/Spinner";
import { useExchangeStateStore } from "@/stores/exchange";
import { useSessionStateStore } from "@/stores/trading/session";
import ButtonsTimeframe from "@/views/components/trading/ButtonsTimeframe.vue";
import ExchangeService from "@/services/ExchangeService";
import SessionService from "@/services/SessionService";
import localStorageService from "@/services/LocalStorageService";
import { useRoute } from 'vue-router';
import { getMinPriceDecimalPlaces } from "@/helpers/chart";
import TextInput from "@/views/components/input/TextInput.vue";

let route = useRoute();
let sessionId = ref(route.params.id);
let session;
const stateExchange = useExchangeStateStore();
const stateSession = useSessionStateStore();
const lwChart = ref();
const chartContainer = ref(null);
const pageLoading = ref(true);
const chartLoading = ref(true);
const sessionLoading = ref(true);
let lastBarUpdateInterval;
const lastBarUpdateTime = 10000;
let roundDecimalPlaces = undefined;
const activeEditFieldName = ref('');
let activeEditFieldTmpValue;

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
    loadSession()
        .then(() => {
            lastBarUpdateInterval = setInterval(() => {
                updateLastBar();
            }, lastBarUpdateTime);
        });
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
    if (lastBarUpdateInterval) {
        clearInterval(lastBarUpdateInterval);
    }
});

const onChartReady = () => {
    setChartSessionLevels();
};

const onTimeframeSelect = (value) => {
    console.log('onTimeframeSelect value ', value);
    stateExchange.interval = value;
    let localStorageMarkets = localStorageService.getItem('markets');
    localStorageMarkets.interval = stateExchange.interval;
    localStorageService.setItem('markets', localStorageMarkets);
    reloadChartData();
};

const onClickStartStopSession = () => {
    if (stateSession.isRunning === true) {
        stateSession.state = 'stopped';
        stateSession.isRunning = false;
    } else {
        stateSession.state = 'running';
        stateSession.isRunning = true;
    }
    updateAndReloadSession();
};

const onClickEditLevel = (levelName) => {
    if (!activeEditFieldName.value) {
        activeEditFieldTmpValue = stateSession[levelName];
        lwChart.value.beginLevelSelection(levelName);
        activeEditFieldName.value = levelName;
    }
};

const onClickEditField = (fieldName) => {
    if (!activeEditFieldName.value) {
        activeEditFieldTmpValue = stateSession[fieldName];
        activeEditFieldName.value = fieldName;
    }
};

const onClickConfirmFieldEdit = () => {
    updateAndReloadSession();
    activeEditFieldName.value = '';
};

const onLevelChange = (level) => {
    stateSession[level.name] = level.price;
};

const onLevelSelected = (level) => {
    stateSession[level.name] = level.price;
    updateAndReloadSession();
    activeEditFieldName.value = '';
};

const onClickCancelEdit  = () => {
    lwChart.value.cancelSelection();
    if (activeEditFieldName.value) {
        stateSession[activeEditFieldName.value] = activeEditFieldTmpValue;
        activeEditFieldName.value = '';
    }
};

const onKeydown = (event) => {
    if (event.key === 'Escape' || event.keyCode === 27) {
        console.log('keydown event: ', event);
        onClickCancelEdit();
    }
};

const updateAndReloadSession = async () => {
    const sessionService = new SessionService();
    let result = await sessionService.updateSession(Number(sessionId.value), stateSession);
    if (!result) {
        loadingError('updateSession');
        return null;
    }
    result = await reloadSession();
    if (!result) {
        loadingError('reloadSession');
        return null;
    }
    result = await reloadChartData();
    if (!result) {
        loadingError('reloadChartData');
        return null;
    }
    return true;
};

const reloadChartData = async () => {
    chartLoading.value = true;
    const exchangeService = new ExchangeService();
    let klineData = await exchangeService.getKlineData(stateExchange.symbol, stateExchange.interval, true);
    if (!klineData) {
        loadingError();
        return null;
    }
    stateExchange.topChartData = klineData;
    chartLoading.value = false;
    return true;
};

const reloadSession = async () => {
    const sessionService = new SessionService();
    const TEST_SESSION_ID = 1;
    sessionLoading.value = true;
    let loadedSession = await sessionService.getSession(sessionId.value ?? TEST_SESSION_ID);
    if (!loadedSession) {
        loadingError('getSession');
        return null;
    }
    stateSession.setData(loadedSession);
    sessionLoading.value = false;
    return true;
};
const loadSession = async () => {
    const sessionService = new SessionService();
    const exchangeService = new ExchangeService();
    const TEST_SESSION_ID = 1;
    const DEFAULT_INTERVAL = '1d';
    pageLoading.value = true;
    chartLoading.value = true;

    let loadedSession = await sessionService.getSession(sessionId.value ?? TEST_SESSION_ID);
    if (!loadedSession) {
        loadingError('getSession');
        return;
    }
    let result = await exchangeService.updateExchangeInfo();
    if (result !== 'ok') {
        loadingError('updateExchangeInfo');
        return;
    }

    let klineData = await exchangeService.getKlineData(loadedSession.symbol, DEFAULT_INTERVAL);
    if (!klineData) {
        loadingError('getKlineData');
        return;
    }
    stateExchange.topChartData = klineData;
    chartLoading.value = false;
    let price = await exchangeService.getLastPrice(loadedSession.symbol);
    if (!price) {
        loadingError('getLastPrice');
        return;
    }
    let minPrice = await exchangeService.getSymbolMinPrice(loadedSession.symbol);
    if (!minPrice) {
        loadingError('getSymbolMinPrice');
        return;
    }
    roundDecimalPlaces = getMinPriceDecimalPlaces(minPrice);
    stateExchange.lastPrice = price;
    stateExchange.symbol = loadedSession.symbol;
    stateSession.setData(loadedSession);
    chartLoading.value = false;
    sessionLoading.value = false;
    pageLoading.value = false;
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

const loadingError = (msg) => {
    console.log('loading error: ' + msg);
    chartLoading.value = false;
    pageLoading.value = false;
};


function toFixed(value) {
    if (value) {
        const number = parseFloat(value);
        return isNaN(number) ? '' : number.toFixed(roundDecimalPlaces);
    }
    return '';
}

function setChartSessionLevels() {
    lwChart.value.setLevels(stateSession.getLevelsArray());
}

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
