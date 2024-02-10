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
}
