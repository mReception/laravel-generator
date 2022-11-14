@php
    echo "<?php".PHP_EOL;
    /** @var InfyOm\Generator\Common\GeneratorConfig $config */
@endphp

namespace {{ $config->namespaces->apiRequest }};

use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use InfyOm\Generator\Request\APIRequest;
{!! $use !!}

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
        $rules = {{ $config->modelNames->name }}::$rules;
        {!! $enumRules !!}
        return $rules;
    }
}
