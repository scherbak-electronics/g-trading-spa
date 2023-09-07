<template>
    <Page>
        <div class="w-full h-full text-left">
            <div class="w-full h-fit text-left pt-3">
                <Button @click="loadData" class="mb-2 mr-2" :label="'Load Data'"/>
                <Button @click="findLevel" class="mb-2 mr-2" :label="'Find Level'"/>
                <Button @click="findLevel2" class="mb-2 mr-2" :label="'Find Level short'"/>
                <Button @click="highlightBars" class="mb-2 mr-2" :label="'Highlight Bars'"/>
                <Button @click="changeColors" class="mb-2 mr-2" :label="'Set Random Colors'"/>
                <Button @click="changeType" class="mb-2 mr-2" :label="'Change Chart Type'"/>
                <Button @click="updColor" class="mb-2 mr-2" :label="'color'"/>
            </div>
            <div>{{ textOut }}</div>
            <div class="chart-container">
                <LWChart
                    :type="chartType"
                    :data="stateMarkets.topChartData"
                    :autosize="true"
                    :chart-options="chartOptions"
                    :series-options="seriesOptions"
                    :price-scale-options="priceScaleOptions"
                    :time-scale-options="timeScaleOptions"
                    ref="lwChart"
                />
            </div>
        </div>
    </Page>
</template>

<script setup>
// This starter template is using Vue 3 <script setup> SFCs
// Check out https://vuejs.org/api/sfc-script-setup.html#script-setup
import { ref } from 'vue';
import LWChart from "@/views/components/trading/Chart";
import { trans } from "@/helpers/i18n";
import Page from "@/views/layouts/Page";
import Button from "@/views/components/input/Button";
import { useMarketsStateStore } from "@/stores/trading/markets";

/*
 * There are example components in both API styles: Options API, and Composition API
 *
 * Select your preferred style from the imports below:
 */
// import LWChart from './components/composition-api/LWChart.vue';
//import LWChart from './components/options-api/LWChart.vue';
// [
//     1499040000000,      // Kline open time
//     "0.01634790",       // Open price
//     "0.80000000",       // High price
//     "0.01575800",       // Low price
//     "0.01577100",       // Close price
//     "148976.11427815",  // Volume
//     1499644799999,      // Kline Close time
//     "2434.19055334",    // Quote asset volume
//     308,                // Number of trades
//     "1756.87402397",    // Taker buy base asset volume
//     "28.46694368",      // Taker buy quote asset volume
//     "0"                 // Unused field, ignore.
// ]
// ]


const stateMarkets = useMarketsStateStore();

const textOut = ref("---");
const chartOptions = ref({
    layout: {
        background: { color: '#222' },
        textColor: '#DDD',
    },
    grid: {
        vertLines: { color: '#444' },
        horzLines: { color: '#444' },
    },
});
const priceScaleOptions = ref({
    borderColor: '#71649C'
});
const timeScaleOptions = ref({
    borderColor: '#71649C'
});
//const data = ref([]);
const isLevelSelectionMode = ref(false);
const seriesOptions = ref({
    color: 'rgb(45, 77, 205)',
});
const chartType = ref('bar');
const lwChart = ref();

function randomShade() {
    return Math.round(Math.random() * 255);
}

const randomColor = (alpha = 1) => {
    return `rgba(${randomShade()}, ${randomShade()}, ${randomShade()}, ${alpha})`;
};

const colorsTypeMap = {
    area: [
        ['topColor', 0.4],
        ['bottomColor', 0],
        ['lineColor', 1],
    ],
    bar: [
        ['upColor', 1],
        ['downColor', 1],
    ],
    baseline: [
        ['topFillColor1', 0.28],
        ['topFillColor2', 0.05],
        ['topLineColor', 1],
        ['bottomFillColor1', 0.28],
        ['bottomFillColor2', 0.05],
        ['bottomLineColor', 1],
    ],
    candlestick: [
        ['upColor', 1],
        ['downColor', 1],
        ['borderUpColor', 1],
        ['borderDownColor', 1],
        ['wickUpColor', 1],
        ['wickDownColor', 1],
    ],
    histogram: [['color', 1]],
    line: [['color', 1]],
};

// Set a random colour for the series as an example of how
// to apply new options to series. A similar appraoch will work on the
// option properties.
const changeColors = () => {
    const options = {};
    const colorsToSet = colorsTypeMap[chartType.value];
    colorsToSet.forEach(c => {
        options[c[0]] = randomColor(c[1]);
    });
    seriesOptions.value = options;
};

const loadData = () => {
    stateMarkets.loadTradingData();
};

const changeType = () => {
    loadData();

    // call a method on the component.
    lwChart.value.fitContent();
};

const findLevel = () => {
    lwChart.value.createNewLevel('breakout', 'long');
};

const findLevel2 = () => {
    lwChart.value.createNewLevel('breakout', 'short');
};

const highlightBars = () => {
    const lastLevelPrice = lwChart.value.getLastLevelPrice();
    if (lastLevelPrice) {
        lwChart.value.highlightLevelBars(lastLevelPrice, '', 0.1);
    }
};

const updColor = () => {
    lwChart.value.updateBarColor('#ffffff');
};

loadData();
</script>
<style scoped>
.chart-container {
    height: calc(100% - 6.0em);
}
</style>
