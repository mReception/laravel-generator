<template>
    <div>
        <base-table
                title="{{ $config->modelNames->camelPlural }}"
                :fields="fields"
                :store="store"
        />
    </div>

</template>


<script setup>

    import BaseTable from 'components/layouts/BaseTable.vue';
    import {reactive} from "vue";
    import {dbFields} from "src/use/dbConsts/{{ $config->modelNames->dashed }}";
    import {columnsFromDbFields} from "src/use/baseTableHelper";
    import {useProcessingPriorities} from "stores/{{ $config->modelNames->dashed }}";

    const store = use{{ $config->modelNames->camelPlural }}()
    const fields = [];
    const columns = reactive(columnsFromDbFields(dbFields));


</script>
