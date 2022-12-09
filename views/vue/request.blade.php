export default interface {{ $config->modelNames->name }}FormRequest {

@foreach($properties as $name => $property)
    {{ $name }}?: {{ $property['type'] }}|null;
@endforeach
}
