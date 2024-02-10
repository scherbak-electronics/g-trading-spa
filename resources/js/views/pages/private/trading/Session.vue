<template>
    <Page :is-loading="pageLoading">
        <div class="w-full h-full text-left flex flex-col">
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div>
                    <div class="flex market-summary mb-2 p-2">
                        <div class="flex-grow symbol">
                            <h4>session</h4>
                            <h1>{{ stateExchange.symbol }}</h1>
                        </div>
                        <div class="flex-grow price">
                            <h4>price</h4>
                            <p>{{ stateExchange.lastPrice }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <Button @click="onClickLoadData" class="mb-2 mr-2" :label="'Load Data'"/>
                        <Button @click="onClickFindLevel" class="mb-2 mr-2" :label="'Find Level'"/>
                        <Button @click="onClickFindLevel2" class="mb-2 mr-2" :label="'Find Level short'"/>
                        <Button @click="onClickHighlightBars" class="mb-2 mr-2" :label="'Highlight Bars'"/>
                    </div>
                </div>
            </div>
            <ButtonsTimeframe v-model="stateExchange.interval" @update:modelValue="onTimeframeSelect"/>
            <div class="chart-container flex-grow" ref="chartContainer">
                <LWChart v-if="!chartLoading" :data="stateExchange.topChartData" ref="lwChart" :initialHeight="400" :symbol="stateExchange.symbol"/>
                <template v-if="chartLoading">
                    <div class="pt-10 pb-6 text-center">
                        <Spinner/>
                    </div>
                </template>
            </div>
        </div>
    </Page>
</template>

<script setup>
import {ref, onMounted, onBeforeUnmount, watch} from 'vue';
import LWChart from "@/views/components/trading/Chart";
import Page from "@/views/layouts/Page";
import Button from "@/views/components/input/Button";

import Spinner from "@/views/components/icons/Spinner";
import { useExchangeStateStore } from "@/stores/exchange";
import ButtonsTimeframe from "@/views/components/trading/ButtonsTimeframe.vue";
import ExchangeService from "@/services/ExchangeService";
import SessionService from "@/services/SessionService";
import localStorageService from "@/services/LocalStorageService";

const stateExchange = useExchangeStateStore();
const lwChart = ref();
const chartContainer = ref(null);
const pageLoading = ref(false);
const chartLoading = ref(false);

let lastBarUpdateInterval;
const lastBarUpdateTime = 10000;

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
    let session = await sessionService.getSession(TEST_SESSION_ID);
    if (!session) {
        loadSessionError();
        return;
    }
    let result = await exchangeService.updateExchangeInfo();
    if (result !== 'ok') {
        loadSessionError();
        return;
    }

    let klineData = await exchangeService.getKlineData(session.symbol, DEFAULT_INTERVAL);
    if (!klineData) {
        loadSessionError();
        return;
    }
    stateExchange.topChartData = klineData;
    chartLoading.value = false;
    let price = await exchangeService.getLastPrice(session.symbol);
    if (!price) {
        loadSessionError();
        return;
    }
    stateExchange.lastPrice = price;
    stateExchange.symbol = session.symbol;
    chartLoading.value = false;
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

const onClickLoadData = () => {

};

const onClickFindLevel = () => {
    lwChart.value.createNewLevel('breakout', 'long');
};

const onClickFindLevel2 = () => {
    lwChart.value.createNewLevel('breakout', 'short');
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

watch(stateExchange.interval, (value) => {
    console.log('in watch stateExchange.interval', value);
});

</script>
<style scoped>
.chart-container {
    box-sizing: content-box;
}
.market-summary {
    background-color: #1f1f1fe8;
    border-radius: 10px;
}
.market-summary h4 {
    font-size: x-small;
    color: #41b883;
}
.market-summary h1 {
    font-size: xxx-large;
    color: #41b883;
}
.market-summary .price p {
    font-size: x-large;
    color: #41b883;
}
.change-24h p {
    font-size: x-large;
    color: #41b883;
}

</style>
