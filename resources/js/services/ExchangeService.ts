import ModelService from "@/services/ModelService";

export default class ExchangeService extends ModelService {
    constructor() {
        super();
        this.url = '/trading/exchange';
    }

    public async getKlineData(symbol:string, interval:string) {
        let params = `?symbol=${symbol}&interval=${interval}`;
        const response = await this.get(this.url + `/kline/` + params);
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
        let params:string = '';
        if (quoteAsset || sortBy || sortDir) {
            params += '?';
        }
        if (quoteAsset) {
            params += `quoteAsset=${quoteAsset}`;
        }
        if (sortBy) {
            if (quoteAsset) {
                params += '&';
            }
            params += `sortBy=${sortBy}`;
        }
        if (sortDir) {
            if (quoteAsset || sortBy) {
                params += '&';
            }
            params += `sortDir=${sortDir}`;
        }
        const response = await this.get(this.url + '/ticker24h/' + params);
        if (response?.data?.ticker24h) {
            return response.data.ticker24h;
        }
        return null;
    }

    public async updateLastBar(symbol:string, interval:string)  {
        if (symbol && interval) {
            const response = await this.get(this.url + `/updateLastBar/?symbol=${symbol}&interval=${interval}`);

            if (response?.data?.last_bar) {
                return response.data.last_bar;
            }
        }
        return null;
    }

    public async getLastPrice(symbol:string) {
        let response = await this.get(this.url + `/priceTicker/?symbol=${symbol}`);
        if (response?.data?.ticker) {
            return response.data.ticker.price;
        }
        return null;
    }

    public async updateExchangeInfo() {
        let response = await this.get(this.url + `/updateExchangeInfo/`);
        if (response?.data?.result) {
            return response.data.result;
        }
        return null;
    }

    public async getSymbolMinPrice(symbol:string)  {
        if (symbol) {
            const response = await this.get(this.url + `/getSymbolMinPrice/?symbol=${symbol}`);

            if (response?.data?.min_price) {
                return response.data.min_price;
            }
        }
        return null;
    }

    public async getOpenOrders(symbol:string)  {
        if (symbol) {
            const response = await this.get(this.url + `/getOpenOrders/?symbol=${symbol}`);

            if (response?.data?.open_orders) {
                return response.data.open_orders;
            }
        }
        return null;
    }

    public async getAllOrders(symbol:string, orderId:string, startTime:string, endTime:string, limit:string)  {
        let params = '';
        if (symbol) {
            params += `?symbol=${symbol}`;
            if (orderId) {
                params += `&orderId=${orderId}`;
            }
            if (startTime) {
                params += `&startTime=${startTime}`;
            }
            if (endTime) {
                params += `&endTime=${endTime}`;
            }
            if (limit) {
                params += `&limit=${limit}`;
            }
            const response = await this.get(this.url + '/getAllOrders/' + params);

            if (response?.data?.all_orders) {
                return response.data.all_orders;
            }
        }
        return null;
    }
}
