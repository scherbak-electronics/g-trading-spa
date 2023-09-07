import ModelService from "@/services/ModelService";


export default class TradingService extends ModelService {
    constructor() {
        super();
        this.url = '/trading/exchange';
    }

    public getTestChart() {
        const symbol = 'BTCBUSD';
        const interval = '1h';
        // const startTime = $request->input('start_time');
        // const endTime = $request->input('end_time');
        // Current timestamp
        const nowTimestamp = Date.now();


        // Timestamp for 90 days ago
        const daysToMilliseconds = 90 * 24 * 60 * 60 * 1000; // 90 days in milliseconds
        const pastTimestamp = nowTimestamp - daysToMilliseconds;

        return this.get(this.url + `/?symbol=${symbol}&interval=${interval}&start_time=${pastTimestamp}&end_time=${nowTimestamp}`);
    }

    public getChartData(symbol, interval) {
        // const startTime = $request->input('start_time');
        // const endTime = $request->input('end_time');
        // Current timestamp
        const nowTimestamp = Date.now();

        // Timestamp for 90 days ago
        const daysToMilliseconds = 90 * 24 * 60 * 60 * 1000; // 90 days in milliseconds
        const pastTimestamp = nowTimestamp - daysToMilliseconds;

        return this.get(this.url + `/?symbol=${symbol}&interval=${interval}&start_time=${pastTimestamp}&end_time=${nowTimestamp}`);
    }
}
