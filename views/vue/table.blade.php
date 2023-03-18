<template>
    <div>
        <base-table
                title="{{ $config->modelNames->human }}"
                :fields="columns"
                :store="store"
        />
    </div>
    <!--            Add New {{ $config->modelNames->human }} Dialog  -->
    <q-dialog v-model="formDialog" no-backdrop-dismiss persistent >
        <q-card style="min-width: 700px;">
            <q-toolbar class="bg-primary text-grey-5 q-pa-sm">
                <q-avatar>
                    <img src="src/assets/images-2/linen_icon_accent_150x150.png">
                </q-avatar>

                <q-toolbar-title><span class="text-weight-bold text-uppercase">New {{ $config->modelNames->human }}</span></q-toolbar-title>

                <q-btn flat round dense icon="close" v-close-popup />
            </q-toolbar>

            <q-card-section class="scroll q-pb-none">
                <{{ $config->modelNames->dashed }}-form-component :items="selected"/>
            </q-card-section>

        </q-card>
    </q-dialog>
    <!--            End New {{ $config->modelNames->human }} Dialog  -->

</template>

<script setup>

    import BaseTable from 'components/layouts/BaseTable.vue';
    import {reactive, ref} from "vue";
    import {dbFields} from "src/use/dbConsts/{{ $config->modelNames->dashed }}";
    import {columnsFromDbFields} from "src/use/baseTableHelper";
    import {use{{ $config->modelNames->camelPlural }}} from "stores/{{ $config->modelNames->dashed }}";
    import {{ $config->modelNames->name }}FormComponent from 'src/components/{{ $config->modelNames->name }}FormComponent.vue'

    const store = use{{ $config->modelNames->camelPlural }}()
    const columns = reactive(columnsFromDbFields(dbFields));

    const formDialog = ref(false)
    const orderCar = async () => {
        formDialog.value = true
    }


</script>
