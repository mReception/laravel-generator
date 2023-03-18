export const dbFields = [
@foreach($properties as $name => $property)
    '{{ $property['field_name'] }}',
@endforeach
];

export const dbFieldsTypes = {
@foreach($properties as $name => $property)
    '{{ $property['field_name'] }}': @if(substr($property['field_name'], -3) === '_id') 'select' @else 'input'@endif,
@endforeach
}
