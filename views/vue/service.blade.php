import httpClient from "./http.service";
import Ticket from "@/models/ticket";

const resourceRoute  = '{{ $config->prefixes->getRoutePrefixWith('/') }}{{ $config->modelNames->dashedPlural }}'

const {{ $config->modelNames->plurals }}Service = {


    async getAll(data: array) {
        return httpClient.get(resourceRoute, JSON.stringify(data))
    },

    async create({{ $config->modelNames->snake }}: {{ $config->modelNames->name }}) {
        return httpClient.post(resourceRoute, {{ $config->modelNames->snake }})
    },

    async get(id: number) {
        return httpClient.get(resourceRoute+'?id=' + id)
    },

    async delete(id: number) {
        return httpClient.delete(resourceRoute+'?id=' + id)
    },

    async update({{ $config->modelNames->snake }}: {{ $config->modelNames->name }}, id: number) {
        return httpClient.put(resourceRoute+'?id=' + id, {{ $config->modelNames->snake }})
    },
};

export default ticketsService;

