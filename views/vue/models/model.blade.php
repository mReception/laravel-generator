@foreach($properties as $name => $property)
    * @property {{ $property['type'] }} ${{ $name }}
@endforeach
{!! $relationsDocProperties !!}
*/
export default interface {{ $config->modelNames->name }} {

@foreach($properties as $name => $property)
      {{ $property['js_name'] }}: {{ $property['js_type'] }};
@endforeach
}

