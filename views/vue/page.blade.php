<template>
    <q-page class="q-pa-sm">
        <table-{{ $config->modelNames->dashed }} />
    </q-page>
</template>

<script setup>
import Table{{ $config->modelNames->name }} from "components/settings/iban/{{ $config->modelNames->name }}Iban.vue";
</script>
