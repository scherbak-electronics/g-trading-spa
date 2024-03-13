<script>
import {createChart} from 'lightweight-charts';
import {getLevelSelectionCrosshair, getDefaultCrosshair, getPriceLineOptions, getMinPriceDecimalPlaces} from "@/helpers/chart";
import ExchangeService from "@/services/ExchangeService";

let logic;
let state;
let series;
let chart;
let firstLeftBarTime;
let markers = [];
let highlightMarkers = [];
let isCursorActive = false;
let timeUnderCursor = 0;
let priceUnderCursor = 0;
let levelName = '';
let levelSide = '';
let levelSnapTo = '';
let levels = [];
let cursorPriceLine = undefined;
let stateMarkets = undefined;
let stateLevels = undefined;
let roundDecimalPlaces = undefined;

// start level selection mode, change cursor,
// after that you have to stop selection mode and add selected price level
// using confirmLevelSelection
const beginLevelSelection = (name) => {
    isCursorActive = true;
    levelName = name;
    chart.applyOptions({
        crosshair: getLevelSelectionCrosshair()
    });
};

const confirmLevelSelection = (price) => {
    addLevel(levelName, price);
    chart.applyOptions({
        crosshair: getDefaultCrosshair()
    });
    isCursorActive = false;
    emit('onLevelSelected', price);
};

function addLevel(name, price) {
    let priceLineOptions = getPriceLineOptions(name, price);
    levels.push({
        price: price,
        priceLine: series.createPriceLine(priceLineOptions)
    });
}

function removeLevel(price) {
    levels = levels.filter(level => {
        if (level.price !== price) {
            return true;
        } else {
            series.removePriceLine(level.priceLine);
            return false;
        }
    });
}

function highlightLevelBars(price, side, accuracy) {
    highlightMarkers = [];
    let levelMarker = {
        position: 'belowBar',
        color: '#559955',
        shape: 'arrowUp',
        text: 'LFB'
    };
    stateMarkets.topChartData.forEach(item => {
        let maxDiff = 100;
        let itemPriceDiff = Math.abs(price - item.high);
        if (itemPriceDiff < maxDiff) {
            highlightMarkers.push({
                time: item.time,
                ...levelMarker
            });
        }
    });
}

function getLastAddedLevelPrice() {
    if (levels.length) {
        return levels[levels.length - 1].price;
    }
}

const crosshairMoveHandler = param => {
    if (isCursorActive) {
        if (!param.point && !param.seriesData) {
            return;
        }
        if (param.time) {
            priceUnderCursor = series.coordinateToPrice(param.point.y);
            const data = param.seriesData.get(series);
            if (levelSnapTo) {
                if (timeUnderCursor !== data.time) {
                    timeUnderCursor = data.time;
                }
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
        priceUnderCursor = series.coordinateToPrice(param.point.y);
        confirmLevelSelection(priceUnderCursor);
        //console.log(`Click at ${param.point.x}, ${param.point.y}. The time is ${param.time}.`);
    }
};

const visibleTimeRangeChangeHandler = (range) => {
    if (range) {
        if (state) {
            if (firstLeftBarTime === range.from) {
                console.log('load from:', range.from);
            }
        }
    }
};

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
            crosshair: getDefaultCrosshair(),
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
                roundDecimalPlaces = getMinPriceDecimalPlaces(minPriceValue);
                if (!chart) {
                    console.log('chart is null by somereason');
                    return;
                }
                series = chart.addBarSeries({
                    //lastValueVisible: false,
                    priceLineVisible: true,
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
                chart.subscribeCrosshairMove((param) => this.crosshairMoveHandler(param));
                chart.subscribeClick((param) => this.chartClickHandler(param));
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
        removeLevel: removeLevel,
        highlightLevelBars(price, side, accuracy) {

        },
        crosshairMoveHandler(param) {
            if (isCursorActive) {
                if (!param.point && !param.seriesData) {
                    return;
                }
                if (param.point.y) {
                    priceUnderCursor = series.coordinateToPrice(param.point.y);
                    this.$emit('onLevelChange', {
                        price: priceUnderCursor,
                        name: levelName
                    });
                }
                if (param.time) {
                    const data = param.seriesData.get(series);
                    if (levelSnapTo) {
                        if (timeUnderCursor !== data.time) {
                            timeUnderCursor = data.time;
                        }
                    }
                }
            }
        },
        chartClickHandler( param ) {
            if (isCursorActive) {
                if (!param.point) {
                    return;
                }
                const data = param.seriesData.get(series);
                priceUnderCursor = series.coordinateToPrice(param.point.y);
                this.confirmLevelSelection(priceUnderCursor);
                //console.log(`Click at ${param.point.x}, ${param.point.y}. The time is ${param.time}.`);
            }
        },
        confirmLevelSelection (price) {
            addLevel(levelName, price);
            chart.applyOptions({
                crosshair: getDefaultCrosshair()
            });
            isCursorActive = false;
            this.$emit('onLevelSelected', {
                price: price,
                name: levelName
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
        beginLevelSelection: beginLevelSelection,
        addLevel: addLevel,
        setLevels(value) {
            levels.forEach(level => {
                if (level.priceLine) {
                    series.removePriceLine(level.priceLine);
                }
            });
            levels = value;
            levels.forEach(level => {
                if (level.price) {
                    let options = {
                        price: level.price
                    };
                    if (level.options) {
                        options = level.options;
                    }
                    level.priceLine = series.createPriceLine(options);
                }
            });
        },
        getLastLevelPrice() {
            return getLastAddedLevelPrice();
        },
        getBoundingClientRect() {
            return this.$refs.chartContainer.getBoundingClientRect();
        }
    },
    expose: ['fitContent', 'beginLevelSelection', 'addLevel', 'setLevels', 'highlightLevelBars', 'getLastLevelPrice', 'getBoundingClientRect', 'setInitialHeight', 'updateLastBar'],
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
