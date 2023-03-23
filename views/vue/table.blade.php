<template>
    <div>
        <base-table
                :title="title"
                :fields="columns"
                :store="store"
                <?php echo  '@selected="(value) =>  selected = value"'; ?>
                <?php echo  PHP_EOL; ?>
                :custom-cells="customCells"
                @edit="edit"
                @add="add"
        />
        <base-dialog v-model="store.formDialog" :selected="selected" :itemId="itemId"  :title="title">
            <{{ $config->modelNames->dashed }}-form-component :items="selected"/>
        </base-dialog>
    </div>
</template>

<script setup>
import BaseTable from 'components/layouts/BaseTable.vue';
import BaseDialog from 'components/layouts/BaseDialog.vue';
import {onMounted, reactive, ref} from 'vue';
import {dbFields} from 'src/use/dbConsts/{{ $config->modelNames->dashed }}';
import ObjectWithNameCell from 'components/base-table/ObjectWithNameCell.vue';
import GetUserNameCell from 'components/base-table/GetUserNameCell.vue';
import {baseTableHelper, columnsFromDbFields} from "src/use/baseTableHelper";
import FormattedDateCell from 'components/base-table/formatted-date-cell.vue';
import YesNoCell from "components/base-table/YesNoCell.vue";
import {use{{ $config->modelNames->plural }}} from 'stores/{{ $config->modelNames->dashed }}';
import {{ $config->modelNames->name }}FormComponent from './{{ $config->modelNames->name }}FormComponent.vue'

@foreach($properties as $name => $property)
@if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
import { use{{ $property['name_plural_title'] }} } from 'src/stores/{{ $property['import'] }}';
@elseif($property['filter_type']==='enum')
import { {{$property['class']}}Enum } from 'src/use/dbConsts/{{ $config->modelNames->dashed }}';
@endif
@endforeach

const title ='{{ $config->modelNames->human }}'
const store = use{{ $config->modelNames->plural }}()
@foreach($properties as $name => $property)
@if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
const store{{ $property['name_plural_title'] }} = use{{ $property['name_plural_title'] }}()
@endif
@endforeach

const columns = reactive(columnsFromDbFields(dbFields));
const selected = ref([])
const itemId = ref(null)

const {addColumnOptions, addEnumSelect} = baseTableHelper(columns);

onMounted(async () => {
@foreach($properties as $name => $property)
@if($property['filter_type']==='enum')
    addEnumSelect('{{$property['field_name']}}', {{$property['class']}}Enum)
@endif
@endforeach
@foreach($properties as $name => $property)
@if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
     store{{ $property['name_plural_title'] }}.fetchOptions(true).then(() => {
        addColumnOptions('{{$property['field_name']}}', store{{ $property['name_plural_title'] }}.getOptions)
    })
@endif
@endforeach
})

const edit = id => {
    store.formDialog = true
    const index = store.{{ $config->modelNames->camelPlural }}.findIndex((item)=> item.id === id)
    if(index >=0) {
        store.current{{ $config->modelNames->name }} = store.{{ $config->modelNames->camelPlural }}[index]
        itemId.value = id
    }
}
const add = () => {
    store.formDialog = true
    store.current{{ $config->modelNames->name }} = null
    itemId.value = null
}

const customCells = [
@foreach($properties as $name => $property)
@if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
@if(str_starts_with($property['field_name'],'user'))
    {field: '{{$property['field_name']}}', secondField: '{{str_replace('_id','',$property['field_name'])}}', component: GetUserNameCell},
@else
    {field: '{{$property['field_name']}}',  secondField: '{{str_replace('_id','',$property['field_name'])}}', component: ObjectWithNameCell},
@endif
@elseif($property['filter_type']==='date')
    {field: '{{$property['field_name']}}', component: FormattedDateCell, title: 'Date'},
@elseif($property['type']=='boolean')
    {field: '{{$property['field_name']}}', component: YesNoCell},
@endif
@endforeach
]

</script>
