import ModelService from "@/services/ModelService";

export default class ExchangeService extends ModelService {
    private readonly isFutures: boolean;
    constructor(isFutures:boolean) {
        super();
        this.url = '/trading/exchange';
        this.isFutures = isFutures;
    }

    public async getKlineData(symbol:string, interval:string) {
        let params = {
            symbol,
            interval,
            is_futures: this.isFutures ? this.isFutures : null
        };
        //`?symbol=${symbol}&interval=${interval}&is_futures=${this.isFutures}`;

        const response = await this.get(this.url + `/kline/`, { params });
        if (response?.data?.kline_data) {
            return response.data.kline_data.map(item => {
                return {
                    time: item.open_time / 1000,
                    open: item.open,
                    close: item.close,
                    low: item.low,
                    high: item.high,
                    color: undefined
                };
            });
        }
        return null;
    }

    public getExchangeInfo(symbol:string, permissions:string) {
        let params = '';
        if (symbol || permissions) {
            params += '?';
        }
        if (symbol) {
            params += `symbol=${symbol}`;
        }
        if (permissions) {
            if (symbol) {
                params += '&';
            }
            params += `permissions=${permissions}`;
        }
        return this.get(this.url + '/info/' + params);
    }

    public getAllSymbols(quoteAsset:string, permissions:string) {
        let params = '';
        if (quoteAsset || permissions) {
            params += '?';
        }
        if (quoteAsset) {
            params += `quote_asset=${quoteAsset}`;
        }
        if (permissions) {
            if (quoteAsset) {
                params += '&';
            }
            params += `permissions=${permissions}`;
        }
        return this.get(this.url + '/symbols/' + params);
    }

    public async getTicker24h(quoteAsset:string, sortBy:string, sortDir:string) {
        let params = {
            quoteAsset: quoteAsset ? quoteAsset : null,
            sortBy: sortBy ? sortBy : null,
            sortDir: sortDir ? sortDir : null,
            is_futures: this.isFutures ? this.isFutures : null
        };//`?is_futures=${this.isFutures}`;

        // if (quoteAsset) {
        //     params += `&quoteAsset=${quoteAsset}`;
        // }
        // if (sortBy) {
        //     params += `&sortBy=${sortBy}`;
        // }
        // if (sortDir) {
        //     params += `&sortDir=${sortDir}`;
        // }
        const response = await this.get(this.url + '/ticker24h/', { params });
        if (response?.data?.ticker24h) {
            return response.data.ticker24h;
        }
        return null;
    }

    public async updateLastBar(symbol:string, interval:string)  {
        if (symbol && interval) {
            let params = {
                symbol,
                interval,
                is_futures: this.isFutures ? this.isFutures : null
            };
            const response = await this.get(this.url + `/updateLastBar/`, { params });

            if (response?.data?.last_bar) {
                return response.data.last_bar;
            }
        }
        return null;
    }

    public async getLastPrice(symbol:string) {
        let params = {
            symbol,
            is_futures: this.isFutures ? this.isFutures : null
        };
        let response = await this.get(this.url + `/priceTicker/`, { params });
        if (response?.data?.ticker) {
            return response.data.ticker.price;
        }
        return null;
    }

    public async updateExchangeInfo() {
        let params = {
            is_futures: this.isFutures ? this.isFutures : null
        };
        let response = await this.get(this.url + `/updateExchangeInfo/`, { params });
        if (response?.data?.result) {
            return response.data.result;
        }
        return null;
    }

    public async getSymbolMinPrice(symbol:string)  {
        if (symbol) {
            let params = {
                symbol,
                is_futures: this.isFutures ? this.isFutures : null
            };
            const response = await this.get(this.url + `/getSymbolMinPrice/`, { params });

            if (response?.data?.min_price) {
                return response.data.min_price;
            }
        }
        return null;
    }

    public async getOpenOrders(symbol:string)  {
        if (symbol) {
            let params = {
                symbol,
                is_futures: this.isFutures ? this.isFutures : null
            };
            const response = await this.get(this.url + `/getOpenOrders/`, { params });

            if (response?.data?.open_orders) {
                return response.data.open_orders;
            }
        }
        return null;
    }

    public async getAllOrders(symbol:string, orderId:string, startTime:string, endTime:string, limit:string)  {
        if (symbol) {
            let params = {
                symbol,
                orderId: orderId ? orderId : null,
                startTime: startTime ? startTime : null,
                endTime: endTime ? endTime : null,
                limit: limit ? limit : null,
                is_futures: this.isFutures ? this.isFutures : null
            };

            const response = await this.get(this.url + '/getAllOrders/', { params });

            if (response?.data?.all_orders) {
                return response.data.all_orders;
            }
        }
        return null;
    }

    public async getOrder(symbol:string, orderId:string, origClientOrderId:string)  {
        if (symbol && (orderId || origClientOrderId)) {
            let params = {
                symbol,
                orderId: orderId ? orderId : null,
                origClientOrderId: origClientOrderId ? origClientOrderId : null,
                is_futures: this.isFutures ? this.isFutures : null
            };

            const response = await this.get(this.url + '/getOrder/', { params });

            if (response?.data?.order) {
                return response.data.order;
            }
        }
        return null;
    }
}
