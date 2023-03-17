export const dbFields = [
    @foreach($properties as $name => $property)
        '{{ $property['js_name'] }}',
    @endforeach
];

