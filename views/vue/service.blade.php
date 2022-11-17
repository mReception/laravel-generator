import httpClient from "./http.service";
import {{ $config->modelNames->name }} from "@/models/{{ $config->modelNames->dashed }}";

const resourceRoute  = '{{ $config->prefixes->getRoutePrefixWith('/') }}{{ $config->modelNames->dashedPlural }}'

const {{ Str::camel($config->modelNames->plural) }}Service = {

    async getAll(data: []) {
        return httpClient.get(resourceRoute, JSON.stringify(data))
    },

    async create({{ $config->modelNames->snake }}: {{ $config->modelNames->name }}) {
        return httpClient.post(resourceRoute, {{ $config->modelNames->snake }})
    },

    async update({{ $config->modelNames->snake }}: {{ $config->modelNames->name }}, id: number) {
        return httpClient.put(resourceRoute+'?id=' + id, {{ $config->modelNames->snake }})
    },

    async get(id: number) {
        return httpClient.get(resourceRoute+'?id=' + id)
    },

    async delete(id: number) {
        return httpClient.delete(resourceRoute+'?id=' + id)
    },

};

export default {{ Str::camel($config->modelNames->plural) }}Service;

