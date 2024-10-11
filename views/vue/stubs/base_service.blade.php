import httpClient from "src/services/http.service";
import { PaginationForm } from "src/models/requests/PaginationForm";
import {FormRequest} from "src/models/requests/FormRequest";

class BaseService<FormRequest> {
    resourceRoute: string;

    constructor(resourceRoute: string) {
        this.resourceRoute = resourceRoute;
    }

    async getAll(form: FormRequest, paginationForm: PaginationForm) {
        return httpClient.get(this.resourceRoute, { params: { ...paginationForm.toRequest(), ...form.toRequest() ?? {} } })
    }

    async getOptions(data: any, paginationForm: PaginationForm) {
        return httpClient.get(this.resourceRoute, { params: { ...paginationForm.toRequest(), data } })
    }

    async create(form: FormRequest) {
        return httpClient.post(this.resourceRoute, form.toCreateRequest())
    }

    async update(form: FormRequest, id: number) {
        return httpClient.put(`${this.resourceRoute}/${id}`, form.toCreateRequest())
    }

    async get(id: number) {
        return httpClient.get(`${this.resourceRoute}/${id}`)
    }

    async delete(id: number) {
        return httpClient.delete(`${this.resourceRoute}/${id}`)
    }
}

export default BaseService;