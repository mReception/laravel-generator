export const dbFields = [
@foreach($properties as $name => $property)
@if(!in_array($property['field_name'],['created_at','updated_at','deleted_at','account_id'])  )
    '{{ $property['field_name'] }}',
@endif
@endforeach
];

export const dbFieldsTypes = {
@foreach($properties as $name => $property)
@if(!in_array($property['field_name'],['id','created_at','updated_at','deleted_at','account_id'])  )
    '{{ $property['field_name'] }}': @if(substr($property['field_name'], -3) === '_id' || $property['filter_type'] == 'enum') 'select' @elseif(strpos($property['field_name'], 'is_' ) !== false) 'checkbox'@else 'input'@endif,
@endif
@if(in_array($property['field_name'],['id','account_id']))
    '{{ $property['field_name'] }}': 'hidden',
@endif
@endforeach
}

@foreach($properties as $name => $property)
@if($property['filter_type']==='enum')
export enum {{$property['class']}}Enum {
@foreach($property['enum_options'] as $option)
    {{$option}} = '{{$option}}',
@endforeach
}
@endif
@endforeach
