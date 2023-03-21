<template>
    <div>
        <base-table
                :title="title"
                :fields="columns"
                :store="store"
                <?php echo  '@selected="(value) =>  selected = value"'; ?>
                <?php echo  PHP_EOL; ?>
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

import {use{{ $config->modelNames->plural }}} from 'stores/{{ $config->modelNames->dashed }}';
import {{ $config->modelNames->name }}FormComponent from './{{ $config->modelNames->name }}FormComponent.vue'

@foreach($properties as $name => $property)
@if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
import { use{{ $property['name_plural_title'] }} } from 'src/stores/{{ $property['import'] }}';
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

const {addColumnOptions} = baseTableHelper(columns);
onMounted(async () => {
    @foreach($properties as $name => $property)
        @if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
        await store{{ $property['name_plural_title'] }}.getOptions(true)
    addColumnOptions('{{$property['field_name']}}', store{{ $property['name_plural_title'] }}.getOptions)
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
         {field: '{{$property['field_name']}}', secondField: 'report_to_user', component: GetUserNameCell},
          @else
         {field: '{{$property['field_name']}}', component: ObjectWithNameCell},
         @endif
    @elseif($property['filter_type']==='date')
         {field: 'created_at', component: FormattedDateCell, title: 'Date'},
    @endif
    @endforeach
]

</script>
