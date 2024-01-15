class LocalStorageService {
    private readonly rootKey: string;

    constructor() {
        this.rootKey = 'g_trading_client';
    }

    setItem(key: string, value: any): void {
        const existingData: { [key: string]: any } = this.getAllData() ?? {};
        existingData[key] = value;
        window.localStorage.setItem(this.rootKey, JSON.stringify(existingData));
    }

    getItem(key: string): any {
        const allData: { [key: string]: any } | null = this.getAllData();
        return allData ? allData[key] : null;
    }

    removeItem(key: string): void {
        const allData: { [key: string]: any } | null = this.getAllData();
        if (allData && allData.hasOwnProperty(key)) {
            delete allData[key];
            window.localStorage.setItem(this.rootKey, JSON.stringify(allData));
        }
    }

    getAllData(): { [key: string]: any } | null {
        const json: string | null = window.localStorage.getItem(this.rootKey);
        return json ? JSON.parse(json) : null;
    }
}

export default new LocalStorageService();
