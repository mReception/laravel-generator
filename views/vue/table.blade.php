<template>
    <div>
        <base-table
                :title="title"
                :fields="columns"
                :store="store"
                <?php echo  '@selected="(value) =>  selected = value"'; ?>
                @edit="edit"
                @add="add"
        />
        <base-dialog v-model="formDialog" :selected="selected" :itemId="itemId"  :title="title">
            <{{ $config->modelNames->dashed }}-form-component :items="selected"/>
        </base-dialog>
    </div>
</template>

<script setup>
import BaseTable from 'components/layouts/BaseTable.vue';
import BaseDialog from "components/layouts/BaseDialog.vue";
import {reactive, ref} from "vue";
import {dbFields} from "src/use/dbConsts/{{ $config->modelNames->dashed }}";
import {columnsFromDbFields} from "src/use/baseTableHelper";
import {use{{ $config->modelNames->camelPlural }}} from "stores/{{ $config->modelNames->dashed }}";
import {{ $config->modelNames->name }}FormComponent from 'src/components/{{ $config->modelNames->name }}FormComponent.vue'
const title ='{{ $config->modelNames->human }}'
const store = use{{ $config->modelNames->camelPlural }}()
const columns = reactive(columnsFromDbFields(dbFields));
const selected = ref([])

const formDialog = ref(false)
const itemId = ref(null)
const edit = id => {
    formDialog.value = true
    itemId.value = id
}
const add = () => {
    formDialog.value = true
}

</script>
