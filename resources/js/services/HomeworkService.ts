import ModelService from "@/services/ModelService";

export default class HomeworkService extends ModelService {
    constructor() {
        super();
        this.url = '/homework';
    }
}
