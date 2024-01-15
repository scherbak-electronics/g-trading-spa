<script>
import { createChart } from 'lightweight-charts';
import * as LightweightCharts from "lightweight-charts";
import stylesConfig from "@/stub/trading/styles";
import ExchangeService from "@/services/ExchangeService";


const tradingStylesConfig = stylesConfig();

let logic;
let state;
let series;
let chart;
let firstLeftBarTime;
let markers = [];
let highlightMarkers = [];
let isCursorActive = false;
let timeUnderCursor = 0;
let levelTradingStyleName = '';
let levelSide = '';
let levels = [];
let cursorPriceLine = undefined;
let stateMarkets = undefined;
let stateLevels = undefined;

const resizeHandler = container => {
    if (!chart || !container) {
        return;
    }
    let dimensions = container.getBoundingClientRect();
    //console.log(dimensions.width, dimensions.height);
    //console.log(chart.options().width, chart.options().height);
    chart.resize(dimensions.width, dimensions.height);
    //console.log('after resize');
    dimensions = container.getBoundingClientRect();
    //console.log(dimensions.width, dimensions.height);
};

function saveLevel(ohlcData) {
    isCursorActive = false;
    saveLevelPriceLine(ohlcData);
    updateMarkers(timeUnderCursor, (newMarkers) => {
        series.setMarkers(newMarkers);
    });
    chart.applyOptions({
        crosshair: {
            horzLine: {
                labelVisible: true,
                visible: true
            }
        }
    });
}

function updateLevelPriceLine(ohlcData) {
    if (cursorPriceLine) {
        series.removePriceLine(cursorPriceLine);
        cursorPriceLine = undefined;
    }
    cursorPriceLine = series.createPriceLine(
        getPriceLineOptions(ohlcData)
    );
}

function getPriceLineOptions(ohlcData) {
    return {
        price: ohlcData[tradingStylesConfig[levelTradingStyleName][levelSide].levelPriceLineOptions.pricePropertyName],
        color: '#3179F5',
        lineWidth: 1,
        lineStyle: 2, // LineStyle.Dashed
        axisLabelVisible: true,
        title: levelTradingStyleName + ' - ' + levelSide
    };
}

function getLevelMarker(time) {
    return {
        time: time,
        ...tradingStylesConfig[levelTradingStyleName][levelSide].levelMarker
    };
}

function getCursorMarker(time) {
    return {
        time: time,
        ...tradingStylesConfig[levelTradingStyleName][levelSide].cursorMarker
    };
}

function highlightLevelBars(price, side, accuracy) {
    highlightMarkers = [];
    stateMarkets.topChartData.forEach(item => {
        let maxDiff = 100;
        let itemPriceDiff = Math.abs(price - item.high);
        if (itemPriceDiff < maxDiff) {
            highlightMarkers.push({
                time: item.time,
                ...tradingStylesConfig[levelTradingStyleName][levelSide].levelMarker
            });
        }
    });
}

function updateMarkers(time, callback) {
    if (!markers) {
        markers = [];
        if (isCursorActive) {
            markers.push(getCursorMarker(time));
        }
        markers.push(...getAllLevelsMarkers());
        markers.push(...highlightMarkers);
        callback(markers);
    }
    if (markers) {
        markers = undefined;
    }
}

function addLevel(level) {
    levels.push(level);
}

function getAllLevelsMarkers() {
    let res = [];
    levels.forEach(level => {
        res.push(...level.markers);
    });
    return res;
}

function getLastAddedLevelPrice() {
    if (levels.length) {
        return levels[levels.length - 1].price;
    }
}

const saveLevelPriceLine = (ohlcData) => {
    if (cursorPriceLine) {
        series.removePriceLine(cursorPriceLine);
        cursorPriceLine = undefined;
    }
    let priceLineData = getPriceLineOptions(ohlcData);
    addLevel({
        price: priceLineData.price,
        priceLine: series.createPriceLine(priceLineData),
        markers: [getLevelMarker(ohlcData.time)]
    });
}

const createNewLevel = (tradingStyle, side) => {
    isCursorActive = true;
    levelTradingStyleName = tradingStyle;
    levelSide = side;
    chart.applyOptions({
        crosshair: {
            horzLine: {
                labelVisible: false,
                visible: false
            }
        },
    });
};

const crosshairMoveHandler = param => {
    if (isCursorActive) {
        if (!param.point && !param.seriesData) {
            return;
        }
        if (param.time) {
            const data = param.seriesData.get(series);
            if (timeUnderCursor !== data.time) {
                timeUnderCursor = data.time;
                updateMarkers(timeUnderCursor);
                updateLevelPriceLine(data);
            }
        }
    }
};

const chartClickHandler = param => {
    if (isCursorActive) {
        if (!param.point) {
            return;
        }
        const data = param.seriesData.get(series);
        saveLevel(data);
        console.log(`Click at ${param.point.x}, ${param.point.y}. The time is ${param.time}.`);
    }
}

const visibleTimeRangeChangeHandler = (range) => {
    if (range) {
        if (state) {
            if (firstLeftBarTime === range.from) {
                console.log('load from:', range.from);
            }
        }
    }
}

export default {
    props: {
        data: {
            type: Array,
            required: true,
        },
        initialHeight: {
            type: Number,
            required: true
        },
        symbol: {
            type: String,
            required: true
        }
    },
    mounted() {
        const exchangeService = new ExchangeService();
        const dimensions = this.$refs.chartContainer.getBoundingClientRect();
        console.log(dimensions.width, this.initialHeight);
        chart = createChart(this.$refs.chartContainer, {
            width: dimensions.width,
            height: this.initialHeight,
            layout: {
                background: { color: '#222' },
                textColor: '#DDD',
            },
            grid: {
                vertLines: { color: '#444' },
                horzLines: { color: '#444' },
            },
            crosshair: {
                mode: LightweightCharts.CrosshairMode.Magnet,
                vertLine: {
                    width: 1,
                    color: '#C3BCDB44',
                    style: LightweightCharts.LineStyle.Solid,
                    labelBackgroundColor: '#9B7DFF',
                },
                horzLine: {
                    width: 1,
                    color: '#C3BCDB44',
                    style: LightweightCharts.LineStyle.Solid
                },
            },
            rightPriceScale: {
                borderColor: '#71649C'
            },
            timeScale: {
                borderColor: '#71649C'
            },
            autoSize: false
        });
        if (!chart) {
            return;
        }
        exchangeService.getSymbolMinPrice(this.symbol)
            .then(minPriceValue => {
                if (!chart) {
                    console.log('chart is null by somereason');
                    return;
                }
                series = chart.addBarSeries({
                    //lastValueVisible: false,
                    priceLineVisible: false,
                    color: 'rgb(45, 77, 205)',
                    priceFormat: {
                        type: 'price',
                        minMove: minPriceValue, // updated minMove
                    }
                });
                if (this.data[0]) {
                    series.setData(this.data);
                    firstLeftBarTime = this.data[0].time;
                }
                chart.timeScale().subscribeVisibleTimeRangeChange( visibleTimeRangeChangeHandler );
                chart.timeScale().fitContent();
                chart.subscribeCrosshairMove( crosshairMoveHandler );
                chart.subscribeClick( chartClickHandler );
                window.addEventListener('resize', () =>
                    resizeHandler(this.$refs.chartContainer)
                );
            });
    },
    unmounted() {
        if (chart) {
            chart.remove();
            chart = null;
        }
        if (series) {
            series = null;
        }
        window.removeEventListener('resize', resizeHandler);
    },
    watch: {
        data(newData) {
            if (!series) {
                return;
            }
            series.setData(newData);
            firstLeftBarTime = newData[0].time;
            console.log('first bar time:', firstLeftBarTime);
        },
        initialHeight(newData) {
            console.log(newData);
        },
    },
    methods: {
        highlightLevelBars(price, side, accuracy) {
            highlightLevelBars(price, side, accuracy);
            updateMarkers(logic.timeUnderCursor, (newMarkers) => {
                series.setMarkers(newMarkers);
            });
        },
        fitContent() {
            if (!chart) {
                return;
            }
            chart.timeScale().fitContent();
        },
        updateLastBar(lastBar) {
            //console.log('last bar: ', lastBar);
            series.update({
                time: lastBar.open_time / 1000,
                open: lastBar.open,
                high: lastBar.high,
                low: lastBar.low,
                close: lastBar.close,
            });
        },
        createNewLevel(tradingStyle, side) {
            createNewLevel(tradingStyle, side);
        },
        saveLevel() {
            saveLevel();
        },
        getLastLevelPrice() {
            return getLastAddedLevelPrice();
        },
        getBoundingClientRect() {
            return this.$refs.chartContainer.getBoundingClientRect();
        }
    },
    expose: ['fitContent', 'createNewLevel', 'saveLevel', 'highlightLevelBars', 'getLastLevelPrice', 'getBoundingClientRect', 'setInitialHeight', 'updateLastBar'],
};
</script>

<template>
    <div class="lw-chart" ref="chartContainer"></div>
</template>

<style scoped>
.lw-chart {
    height: fit-content;
    box-sizing: content-box;
}
</style>
