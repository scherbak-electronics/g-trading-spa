import { useMarketsStateStore } from "@/stores/trading/markets";
import { useLevelsState } from "@/stores/trading/levels";
import stylesConfig from "@/stub/trading/styles";
const tradingStylesConfig = stylesConfig();


export class ChartCustomLogic {
    markers = [];
    highlightMarkers = [];
    isCursorActive = false;
    timeUnderCursor = 0;
    levelTradingStyleName = '';
    levelSide = '';
    cursorPriceLine = undefined;
    stateMarkets = undefined;
    stateLevels = undefined;

    constructor() {
        this.stateMarkets = useMarketsStateStore();
        this.stateLevels = useLevelsState();
    }

    createNewLevel(tradingStyleName, side) {
        this.isCursorActive = true;
        this.levelTradingStyleName = tradingStyleName;
        this.levelSide = side;
    }

    getPriceLineOptions(ohlcData) {
        return {
            price: ohlcData[tradingStylesConfig[this.levelTradingStyleName][this.levelSide].levelPriceLineOptions.pricePropertyName],
            color: '#3179F5',
            lineWidth: 1,
            lineStyle: 2, // LineStyle.Dashed
            axisLabelVisible: true,
            title: this.levelTradingStyleName + ' - ' + this.levelSide
        };
    }

    getLevelMarker(time) {
        return {
            time: time,
            ...tradingStylesConfig[this.levelTradingStyleName][this.levelSide].levelMarker
        };
    }

    getCursorMarker(time) {
        return {
            time: time,
            ...tradingStylesConfig[this.levelTradingStyleName][this.levelSide].cursorMarker
        };
    }

    highlightLevelBars(price, side, accuracy) {
        this.highlightMarkers = [];
        this.stateMarkets.topChartData.forEach(item => {
            let maxDiff = 100;
            let itemPriceDiff = Math.abs(price - item.high);
            if (itemPriceDiff < maxDiff) {
                this.highlightMarkers.push({
                    time: item.time,
                    ...tradingStylesConfig[this.levelTradingStyleName][this.levelSide].levelMarker
                });
            }
        });
    }

    updateMarkers(time, callback) {
        if (!this.markers) {
            this.markers = [];
            if (this.isCursorActive) {
                this.markers.push(this.getCursorMarker(time));
            }
            this.markers.push(...this.stateLevels.getAllLevelsMarkers());
            this.markers.push(...this.highlightMarkers);
            callback(this.markers);
        }
        if (this.markers) {
            this.markers = undefined;
        }
    }
}

