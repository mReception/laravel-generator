import httpClient from "src/services/http.service";
import {{ $config->modelNames->name }} from 'src/models/{{ $config->modelNames->dashed }}';
import {PaginationForm} from "src/models/requests/PaginationForm";
import { {{ $config->modelNames->name }}FormRequest} from "src/models/requests/{{ $config->modelNames->name }}FormRequest";

const resourceRoute  = 'api/{{ $config->prefixes->getRoutePrefixWith('/') }}{{ $config->modelNames->dashedPlural }}'

const {{ Str::camel($config->modelNames->plural) }}Service = {

    async getAll(form: {{ $config->modelNames->snake }}FormRequest, paginationForm: PaginationForm) {
        return httpClient.get(resourceRoute,{params: {...paginationForm.toRequest(),...form.toRequest()??{}}} )
    },

    async getOptions(data:any, paginationForm: PaginationForm ) {
        return httpClient.get(resourceRoute, {params: {...paginationForm.toRequest(), data}} )
    },

    async create({{ $config->modelNames->snake }}: {{ $config->modelNames->name }}) {
        return httpClient.post(resourceRoute, {{ $config->modelNames->snake }})
    },

    async update(form: [], id: number) {
        return httpClient.put(resourceRoute+'/' + id, form)
    },

    async get(id: number) {
        return httpClient.get(resourceRoute+'/' + id)
    },

    async delete(id: number) {
        return httpClient.delete(resourceRoute+'/' + id)
    },

};

export default {{ Str::camel($config->modelNames->plural) }}Service;

