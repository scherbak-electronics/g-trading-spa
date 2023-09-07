import ModelService from "@/services/ModelService";

export default class ExchangeService extends ModelService {
    constructor() {
        super();
        this.url = '/trading/exchange';
    }

    // Timestamp for 90 days ago
    //const daysToMilliseconds = 90 * 24 * 60 * 60 * 1000; // 90 days in milliseconds
    public getKlineData(symbol, interval, startTime, endTime, limit) {
        return this.get(this.url + `/kline/?symbol=${symbol}&interval=${interval}&start_time=${startTime}&end_time=${endTime}&limit=${limit}`);
    }

    public getExchangeInfo(symbol, permissions) {
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

    public getAllSymbols(quoteAsset, permissions) {
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
}
