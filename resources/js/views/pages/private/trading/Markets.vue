<template>
    <Page :is-loading="pageLoading">
        <div class="w-full h-full text-left flex flex-col">
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div>
                    <div class="flex market-summary mb-2 p-2">
                        <div class="flex-grow symbol">
                            <h4>market</h4>
                            <h1>{{ stateExchange.symbol }}</h1>
                        </div>
                        <div class="flex-grow price">
                            <h4>price</h4>
                            <p>{{ stateExchange.lastPrice }}</p>
                        </div>
                        <div class="flex-grow change-24h">
                            <h4>24h change</h4>
                            <p>{{ stateExchange.priceChange }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <Button @click="onClickLoadData" class="mb-2 mr-2" :label="'Load Data'"/>
                        <Button @click="onClickFindLevel" class="mb-2 mr-2" :label="'Find Level'"/>
                        <Button @click="onClickFindLevel2" class="mb-2 mr-2" :label="'Find Level short'"/>
                        <Button @click="onClickHighlightBars" class="mb-2 mr-2" :label="'Highlight Bars'"/>
                    </div>
                    <div class="text-left">
                        <Button @click="onClickCreateSession" class="mb-2 mr-2" :label="'Create Session'"/>
                    </div>
                </div>
                <div>
                    <div class="text-right">
                        <Button @click="onClickUSDT" class="mb-2 mr-2 " :class="[{ 'sorting-selected': stateExchange.tickersQuoteAssetFilter === 'USDT' }]"  :label="'USDT'"/>
                        <Button @click="onClickByVolume" class="mb-2 mr-2 " :class="[{ 'sorting-selected': stateExchange.tickersSortBy === 'quote_volume' }]" :label="'by volume'"/>
                        <Button @click="onClickByChange" class="mb-2 mr-2 " :class="[{ 'sorting-selected': stateExchange.tickersSortBy === 'price_change_percent' }]" :label="'by change'"/>
                        <Button @click="onClickSortDir" class="mb-2 mr-2 " :label="''"  :icon="stateExchange.tickersSortDir === 'desc' ? 'fa fa-arrow-circle-down' : (stateExchange.tickersSortDir === 'asc' ? 'fa fa-arrow-circle-up' : '')"/>
                    </div>
                    <div>
                        <template v-if="marketsLoading">
                            <div class="pt-10 pb-6 text-center">
                                <Spinner/>
                            </div>
                        </template>
                        <TableMarkets v-if="!marketsLoading" :records="stateExchange.tickers" @record-selected="onRecordSelectedChangeMarket"/>
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
import TableMarkets from "@/views/components/trading/TableMarkets";
import Spinner from "@/views/components/icons/Spinner";
import { useExchangeStateStore } from "@/stores/exchange";
import ButtonsTimeframe from "@/views/components/trading/ButtonsTimeframe.vue";
import ExchangeService from "@/services/ExchangeService";
import SessionService from "@/services/SessionService";
import localStorageService from "@/services/LocalStorageService";
import { useRouter } from 'vue-router';


const router = useRouter();
const stateExchange = useExchangeStateStore();
const lwChart = ref();
const chartContainer = ref(null);
const pageLoading = ref(false);
const chartLoading = ref(false);
const marketsLoading = ref(false);
let lastBarUpdateInterval;
const lastBarUpdateTime = 10000;

onMounted(() => {
    const exchangeService = new ExchangeService();
    const localStorageMarkets = localStorageService.getItem('markets');
    if (localStorageMarkets?.symbol && localStorageMarkets?.interval) {
        stateExchange.symbol = localStorageMarkets.symbol;
        stateExchange.interval = localStorageMarkets.interval;
    } else {
        localStorageService.setItem('markets', {
            symbol: stateExchange.symbol,
            interval: stateExchange.interval
        });
    }

    pageLoading.value = true;
    marketsLoading.value = true;
    chartLoading.value = true;
    exchangeService.updateExchangeInfo()
        .then(result => {
            if (result === 'ok') {
                exchangeService.getTicker24h(
                    stateExchange.tickersQuoteAssetFilter,
                    stateExchange.tickersSortBy,
                    stateExchange.tickersSortDir
                )
                    .then((tickers) => {
                        stateExchange.tickers = tickers;
                        marketsLoading.value = false;
                        exchangeService.getKlineData(stateExchange.symbol, stateExchange.interval)
                            .then((klineData) => {
                                stateExchange.topChartData = klineData;
                                chartLoading.value = false;
                                exchangeService.getLastPrice(stateExchange.symbol)
                                    .then(price => {
                                        stateExchange.lastPrice = price;
                                        lastBarUpdateInterval = setInterval(() => {
                                            updateLastBar();
                                        }, lastBarUpdateTime);
                                    })
                                    .finally(() => {
                                    });
                            })
                            .catch(() => {
                                console.log('loadTradingData error')
                            })
                            .finally(() => {
                                chartLoading.value = false;
                                pageLoading.value = false;
                            });
                    })
                    .catch(() => {
                        console.log('getTicker24h error')
                    })
                    .finally(() => {
                        marketsLoading.value = false;
                    });
            }
        });

    lastBarUpdateInterval = setInterval(() => {
        updateLastBar();
    }, lastBarUpdateTime);
});

onBeforeUnmount(() => {
    clearInterval(lastBarUpdateInterval);
});

const onRecordSelectedChangeMarket = (record) => {
    console.log('change market: ', record);
    stateExchange.symbol = record.symbol;
    let localStorageMarkets = localStorageService.getItem('markets');
    localStorageMarkets.symbol = stateExchange.symbol;
    localStorageService.setItem('markets', localStorageMarkets);
    reloadKlineData();
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

const onClickCreateSession = () => {
    const sessionService = new SessionService();
    sessionService.createSession(stateExchange.symbol).then((session) => {
        console.log('session: ', session);
        router.push(`/page/session/${session.id}`);
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

const onClickUSDT = () => {
    if (stateExchange.tickersQuoteAssetFilter === 'USDT') {
        stateExchange.tickersQuoteAssetFilter = '';
    } else {
        stateExchange.tickersQuoteAssetFilter = 'USDT';
    }
    reloadTickers();
};
const onClickByVolume = () => {
    stateExchange.setSortByVolume();
    reloadTickers();
};

const onClickByChange = () => {
    stateExchange.setSortByChange();
    reloadTickers();
};

const onClickSortDir = () => {};

const reloadTickers = () => {
    const exchangeService = new ExchangeService();
    marketsLoading.value = true;
    exchangeService.getTicker24h(
        stateExchange.tickersQuoteAssetFilter,
        stateExchange.tickersSortBy,
        stateExchange.tickersSortDir
    )
        .then((tickers) => {
            stateExchange.tickers = tickers;
            marketsLoading.value = false;
        });
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

.sorting-selected {
    background-color: #3b82f6 !important;
}
</style>
