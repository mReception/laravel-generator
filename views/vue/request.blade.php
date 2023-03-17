import {FormRequest} from "src/models/requests/FormRequest";

interface {{ $config->modelNames->name }}FormRequestInterface {

@foreach($properties as $name => $property)
    {{ $name }}?: {{ $property['js_form_type'] }}|null;
@endforeach
}


class {{ $config->modelNames->name }}FormRequest extends FormRequest implements {{ $config->modelNames->name }}FormRequestInterface{
@foreach($properties as $name => $property)
    {{ $name }}?: {{ $property['js_form_type'] }}|null;
@endforeach

constructor(
@foreach($properties as $name => $property)
    {{ $name }}?: {{ $property['js_form_type'] }}|null,
@endforeach
) {
    super();

@foreach($properties as $name => $property)
    this.{{ $name }} = {{ $name }} || null;
@endforeach
    }
}
export { {{ $config->modelNames->name }}FormRequest };
