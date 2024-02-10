import { defineStore } from 'pinia';

export const useExchangeStateStore = defineStore('state_exchange', {
    state: () => {
        const sortByOptions = ['quote_volume', 'price_change_percent'];
        const sortDirOptions = ['desc', 'asc'];
        return {
            symbol: 'BTCUSDT',
            interval: '1d',
            topChartData: [],
            bottomChartData: [],
            firstLeftBarTime: 0,
            tickers: [],
            lastPrice: 0,
            priceChange: 0,
            tickersQuoteAssetFilter: '',
            tickersSortBy: '',
            tickersSortDir: 'desc',
            sortByOptions,
            sortDirOptions,
            sortByOptionsSelected: 0,
            sortDirOptionsSelected: 0
        }
    },
    actions: {
        setSortByChange() {
            if (this.tickersSortBy !== 'price_change_percent') {
                this.tickersSortBy = 'price_change_percent';
            }
            this.setNextDirOption();
        },
        setSortByVolume() {
            if (this.tickersSortBy !== 'quote_volume') {
                this.tickersSortBy = 'quote_volume';
            }
            this.setNextDirOption();
        },
        setNextDirOption() {
            this.sortDirOptionsSelected++;
            this.sortDirOptionsSelected = this.sortDirOptionsSelected % this.sortDirOptions.length;
            this.tickersSortDir = this.sortDirOptions[this.sortDirOptionsSelected];
        }
    }
});
