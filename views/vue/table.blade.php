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
import {reactive, ref} from 'vue';
import {dbFields} from 'src/use/dbConsts/{{ $config->modelNames->dashed }}';
import {columnsFromDbFields} from 'src/use/baseTableHelper';
import {use{{ $config->modelNames->name }}} from 'stores/{{ $config->modelNames->dashed }}';
import {{ $config->modelNames->name }}FormComponent from './{{ $config->modelNames->name }}FormComponent.vue'
const title ='{{ $config->modelNames->human }}'
const store = use{{ $config->modelNames->name }}()
const columns = reactive(columnsFromDbFields(dbFields));
const selected = ref([])

const itemId = ref(null)
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

</script>
