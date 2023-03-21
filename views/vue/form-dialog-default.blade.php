<template>
    <div class='billing-info p_relative d_block'>
        <q-card-section class='q-pa-md'>
            <base-form v-model='saveModel' :errors='store.errors'></base-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align='right'>
            <q-btn flat class='text-accent' v-close-popup>Cancel</q-btn>
            <q-btn flat class='text-accent' :loading='loading' @click='saveOrUpdate'>
                <?php echo "{{ store.getCurrentItem ? 'Update' : 'Save' }}"; ?>
            </q-btn>
        </q-card-actions>
    </div>
</template>

<script setup>
import {onMounted, reactive} from 'vue';
import {use{{ $config->modelNames->plural }}} from 'stores/{{ $config->modelNames->dashed }}';
import BaseForm from 'components/layouts/BaseForm.vue';
import {dbFieldsTypes} from 'src/use/dbConsts/{{ $config->modelNames->dashed }}';
@foreach($properties as $name => $property)
@if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
import { use{{ $property['name_plural_title'] }} } from 'src/stores/{{ $property['import'] }}';
@endif
@endforeach

import {baseFormHelper} from 'src/use/baseFormHelper';

const store = use{{ $config->modelNames->plural }}()

@foreach($properties as $name => $property)
@if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
const store{{ $property['name_plural_title'] }} = use{{ $property['name_plural_title'] }}()
@endif
@endforeach


const saveModel = reactive({})
const {setOptions, saveOrUpdate, loading} = baseFormHelper(store, saveModel, dbFieldsTypes)

onMounted(async () => {
    @foreach($properties as $name => $property)
    @if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id'))
    setOptions('{{$property['field_name']}}', store{{ $property['name_plural_title'] }})
    @endif
    @endforeach
})
</script>

