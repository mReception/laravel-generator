export default interface {{ $config->modelNames->name }}FormRequest {

@foreach($properties as $name => $property)
    {{ $name }}?: {{ $property['js_form_type'] }}|null;
@endforeach
}
