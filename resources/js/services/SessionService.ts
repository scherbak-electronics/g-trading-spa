import ModelService from "@/services/ModelService";

export default class SessionService extends ModelService {
    constructor() {
        super();
        this.url = '/trading/session';
    }

    public async getSession(id:number) {
        let params = `?id=${id}`;
        const response = await this.get(this.url + `/get/` + params);
        if (response?.data?.session) {
            return response.data.session;
        }
        return null;
    }

    public async createSession(symbol:string) {
        let data = {
            'symbol': symbol
        };
        let response = await this.post(this.url, data);
        if (response?.data?.new_session) {
            return response.data.new_session;
        }
        return null;
    }

    public async updateSession(id:number, data) {
        let params = `${id}`;
        let response = await this.patch(this.url + `/` + params, data);
        //let response = await this.api.patch(this.url + `/` + params, data, {});
        if (response?.data?.session) {
            return response.data.session;
        }
        return null;
    }
}
