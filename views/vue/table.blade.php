<template>
    <div>
        <base-table
                title="{{ $config->modelNames->camelPlural }}"
                :fields="columns"
                :store="store"
        />
    </div>
</template>

<script setup>
import BaseTable from 'components/layouts/BaseTable.vue';
import {reactive} from "vue";
import {dbFields} from "src/use/dbConsts/{{ $config->modelNames->dashed }}";
import {columnsFromDbFields} from "src/use/baseTableHelper";
import {use{{ $config->modelNames->camelPlural }}} from "stores/{{ $config->modelNames->dashed }}";

const store = use{{ $config->modelNames->camelPlural }}()
const columns = reactive(columnsFromDbFields(dbFields));

</script>
