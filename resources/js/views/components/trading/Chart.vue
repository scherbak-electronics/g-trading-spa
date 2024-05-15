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
                this.$emit('onChartReady');
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
        beginLevelSelection(name) {
            isCursorActive = true;
            levelName = name;
            chart.applyOptions({
                crosshair: getLevelSelectionCrosshair()
            });
        },
        removeLevel(name) {
            levels = levels.filter(level => {
                if (level.name === name) {
                    series.removePriceLine(level.priceLine);
                    return false;
                } else {
                    return true;
                }
            });
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
            this.removeLevel(levelName);
            this.addLevel(levelName, price);
            chart.applyOptions({
                crosshair: getDefaultCrosshair()
            });
            isCursorActive = false;
            this.$emit('onLevelSelected', {
                name: levelName,
                price: price
            });
        },
        cancelSelection() {
            chart.applyOptions({
                crosshair: getDefaultCrosshair()
            });
            isCursorActive = false;
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
        addLevel(name, price) {
            let priceLineOptions = getPriceLineOptions(name, price);
            levels.push({
                name: name,
                price: price,
                priceLine: series.createPriceLine(priceLineOptions)
            });
        },
        setLevels(value) {
            levels.forEach(level => {
                if (level.priceLine) {
                    series.removePriceLine(level.priceLine);
                }
            });
            levels = value;
            levels.forEach(level => {
                if (level.price) {
                    let options = getPriceLineOptions(level.name, level.price);
                    if (level.options) {
                        options = level.options;
                    }
                    level.priceLine = series.createPriceLine(options);
                }
            });
        },
        getBoundingClientRect() {
            return this.$refs.chartContainer.getBoundingClientRect();
        }
    },
    expose: ['fitContent', 'beginLevelSelection', 'addLevel', 'setLevels', 'removeLevel', 'cancelSelection', 'getBoundingClientRect', 'setInitialHeight', 'updateLastBar'],
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
