<template>
    <q-page class="q-pa-sm">
        <table-{{ $config->modelNames->dashed }} />
    </q-page>
</template>

<script setup>
import Table{{ $config->modelNames->name }} from "components/Table{{ $config->modelNames->name }}.vue";
</script>
