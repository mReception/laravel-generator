@php
    echo "<?php".PHP_EOL;
    /** @var InfyOm\Generator\Common\GeneratorConfig $config */
@endphp

namespace {{ $config->namespaces->apiRequest }};

use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use InfyOm\Generator\Request\APIRequest;

class Create{{ $config->modelNames->name }}APIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            {{ $config->modelNames->name }}::$rules,
            {{ array_map(function ($field) {
                if(!empty($field->requestValidators)) {
                    return [$field->name => $field->requestValidators];
                }
            }, $config->fields) }}
        );
    }
}
