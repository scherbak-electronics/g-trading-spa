import { defineStore } from 'pinia';
import { useGlobalStateStore } from "@/stores";
import TradingService from "@/services/TradingService";

export const useMarketsStateStore = defineStore('state_markets', {
    state: () => {
        return {
            symbol: 'BTCBUSD',
            interval: '1d',
            topChartData: [],
            bottomChartData: []
        }
    },
    actions: {
        loadTradingData() {
            const tradingService = new TradingService();
            const stateGlobal = useGlobalStateStore();
            stateGlobal.setUILoading(true);
            tradingService.getChartData(this.symbol, this.interval) // Replace '/users/binance' with your actual Laravel route
                .then(response => {
                    this.topChartData = response.data.kline_data.map(item => {
                        return {
                            time: item.open_time / 1000,
                            open: item.open,
                            close: item.close,
                            low: item.low,
                            high: item.high,
                            color: undefined
                        };
                    });
                    stateGlobal.setUILoading(false);
                })
                .catch(error => {
                    stateGlobal.setUILoading(false);
                    console.error(error);
                });
        }
    }
});
