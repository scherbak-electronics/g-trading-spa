<script>
import { createChart } from 'lightweight-charts';
import * as LightweightCharts from "lightweight-charts";
import { useLevelsState } from "@/stores/trading/levels";
import { ChartCustomLogic } from "@/model/ChartCustomLogic";

// Lightweight Chart instances are stored as normal JS variables
// If you need to use a ref then it is recommended that you use `shallowRef` instead
let logic;
let state;
let series;
let chart;


// Function to get the correct series constructor name for current series type.
function getChartSeriesConstructorName(type) {
    return `add${type.charAt(0).toUpperCase() + type.slice(1)}Series`;
}

// Creates the chart series and sets the data.
const addSeriesAndData = (type, seriesOptions, data) => {
    const seriesConstructor = getChartSeriesConstructorName(type);
    series = chart[seriesConstructor](seriesOptions);
    series.setData(data);
    series.applyOptions({
        //lastValueVisible: false,
        priceLineVisible: false,
    });
};

// Auto resizes the chart when the browser window is resized.
const resizeHandler = container => {
    if (!chart || !container) return;
    const dimensions = container.getBoundingClientRect();
    chart.resize(dimensions.width, dimensions.height);
};



function saveLevel(ohlcData) {
    logic.isCursorActive = false;
    saveLevelPriceLine(ohlcData);
    updateMarkers(logic.timeUnderCursor);
    chart.applyOptions({
        crosshair: {
            horzLine: {
                labelVisible: true,
                visible: true
            }
        }
    });
}

function updateMarkers(time) {
    logic.updateMarkers(time, (newMarkers) => {
        series.setMarkers(newMarkers);
    });
}

function updateLevelPriceLine(ohlcData) {
    if (logic.cursorPriceLine) {
        series.removePriceLine(logic.cursorPriceLine);
        logic.cursorPriceLine = undefined;
    }
    logic.cursorPriceLine = series.createPriceLine(
        logic.getPriceLineOptions(ohlcData)
    );
}

const saveLevelPriceLine = (ohlcData) => {
    if (logic.cursorPriceLine) {
        series.removePriceLine(logic.cursorPriceLine);
        logic.cursorPriceLine = undefined;
    }
    let priceLineData = logic.getPriceLineOptions(ohlcData);
    state.addLevel({
        price: priceLineData.price,
        priceLine: series.createPriceLine(priceLineData),
        markers: [logic.getLevelMarker(ohlcData.time)]
    });
}

const createNewLevel = (tradingStyle, side) => {
    logic.createNewLevel(tradingStyle, side);
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
    if (logic.isCursorActive) {
        if (!param.point && !param.seriesData) {
            return;
        }
        if (param.time) {
            const data = param.seriesData.get(series);
            if (logic.timeUnderCursor !== data.time) {
                logic.timeUnderCursor = data.time;
                updateMarkers(logic.timeUnderCursor);
                updateLevelPriceLine(data);
            }
        }
    }
};


const chartClickHandler = param => {
    if (logic.isCursorActive) {
        if (!param.point) {
            return;
        }
        const data = param.seriesData.get(series);
        saveLevel(data);
        console.log(`Click at ${param.point.x}, ${param.point.y}. The time is ${param.time}.`);
    }
}

export default {
    props: {
        type: {
            type: String,
            default: 'line',
        },
        data: {
            type: Array,
            required: true,
        },
        autosize: {
            default: true,
            type: Boolean,
        },
        chartOptions: {
            type: Object,
        },
        seriesOptions: {
            type: Object,
        },
        timeScaleOptions: {
            type: Object,
        },
        priceScaleOptions: {
            type: Object,
        },
    },
    mounted() {
        state = useLevelsState();
        logic = new ChartCustomLogic();
        // Create the Lightweight Charts Instance using the container ref.
        chart = createChart(this.$refs.chartContainer, this.chartOptions);
        addSeriesAndData(this.type, this.seriesOptions, this.data);

        if (this.priceScaleOptions) {
            chart.priceScale().applyOptions(this.priceScaleOptions);
        }

        if (this.timeScaleOptions) {
            chart.timeScale().applyOptions(this.timeScaleOptions);
        }

        chart.applyOptions({
            crosshair: {
                // Change mode from default 'magnet' to 'normal'.
                // Allows the crosshair to move freely without snapping to datapoints
                mode: LightweightCharts.CrosshairMode.Magnet,

                // Vertical crosshair line (showing Date in Label)
                vertLine: {
                    width: 1,
                    color: '#C3BCDB44',
                    style: LightweightCharts.LineStyle.Solid,
                    labelBackgroundColor: '#9B7DFF',
                },

                // Horizontal crosshair line (showing Price in Label)
                horzLine: {
                    width: 1,
                    color: '#C3BCDB44',
                    style: LightweightCharts.LineStyle.Solid
                },
            },
        });
        chart.timeScale().fitContent();
        chart.subscribeCrosshairMove( crosshairMoveHandler );
        chart.subscribeClick( chartClickHandler );

        if (this.autosize) {
            window.addEventListener('resize', () =>
                resizeHandler(this.$refs.chartContainer)
            );
        }
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
    /*
     * Watch for changes to any of the component properties.
     *
     * If an options property is changed then we will apply those options
     * on top of any existing options previously set (since we are using the
     * `applyOptions` method).
     *
     * If there is a change to the chart type, then the existing series is removed
     * and the new series is created, and assigned the data.
     *
     */
    watch: {
        autosize(enabled) {
            if (!enabled) {
                window.removeEventListener('resize', () =>
                    resizeHandler(this.$refs.chartContainer)
                );
                return;
            }
            window.addEventListener('resize', () =>
                resizeHandler(this.$refs.chartContainer)
            );
        },
        type(newType) {
            if (series && chart) {
                chart.removeSeries(series);
            }
            addSeriesAndData(this.type, this.seriesOptions, this.data);
        },
        data(newData) {
            if (!series) return;
            series.setData(newData);
        },
        chartOptions(newOptions) {
            if (!chart) return;
            chart.applyOptions(newOptions);
        },
        seriesOptions(newOptions) {
            if (!series) return;
            series.applyOptions(newOptions);
        },
        priceScaleOptions(newOptions) {
            if (!chart) return;
            chart.priceScale().applyOptions(newOptions);
        },
        timeScaleOptions(newOptions) {
            if (!chart) return;
            chart.timeScale().applyOptions(newOptions);
        },
    },
    methods: {
        highlightLevelBars(price, side, accuracy) {
            logic.highlightLevelBars(price, side, accuracy);
            logic.updateMarkers(logic.timeUnderCursor, (newMarkers) => {
                series.setMarkers(newMarkers);
            });
        },
        updateBarColor(barColor) {
            series.update({
                time: logic.timeUnderCursor,
                color: barColor
            });
        },
        fitContent() {
            if (!chart) return;
            chart.timeScale().fitContent();
        },
        getChart() {
            return chart;
        },
        getSeries() {
            return series;
        },
        createNewLevel(tradingStyle, side) {
            createNewLevel(tradingStyle, side);
        },
        saveLevel() {
            saveLevel();
        },
        getLastLevelPrice() {
            return state.getLastAddedLevelPrice();
        }
    },
    expose: ['fitContent', 'getChart', 'getSeries', 'createNewLevel', 'saveLevel', 'highlightLevelBars', 'getLastLevelPrice', 'updateBarColor'],
};
</script>

<template>
    <div class="lw-chart" ref="chartContainer"></div>
</template>

<style scoped>
.lw-chart {
    height: 100%;
}
</style>
