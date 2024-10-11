export default {{ Str::camel($config->modelNames->plural) }}Service;

import { {{ $config->modelNames->name }}FormRequest } from "src/models/requests/{{ $config->modelNames->name }}FormRequest";
import BaseService from "src/services/BaseService";

class {{ $config->modelNames->name }}Service extends BaseService<{{ $config->modelNames->name }}FormRequest> {
    constructor() {
    super('api/{{ $config->modelNames->dashedPlural }}');
    }
    // Add more overridden methods as needed
}

const {{ Str::camel($config->modelNames->plural) }}Service = new {{ $config->modelNames->name }}Service();

export default {{ Str::camel($config->modelNames->plural) }}Service;

