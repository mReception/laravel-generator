<template>
    <div class="billing-info p_relative d_block">
        <q-card-section class="q-pa-md">
            <base-form v-model="saveModel" :errors="store.errors"></base-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align="right">
            <q-btn flat class="text-accent" v-close-popup>Cancel</q-btn>
            <q-btn flat class="text-accent" :loading="loading" @click="saveOrUpdate">
                <?php echo "{{ store.getCurrentItem ? 'Update' : 'Save' }}"; ?>
            </q-btn>
        </q-card-actions>
    </div>
</template>

<script setup>
import {onMounted, reactive} from "vue";
import {use{{ $config->modelNames->name }}} from "stores/{{ $config->modelNames->dashed }}";
import BaseForm from "components/layouts/BaseForm.vue";
import {dbFieldsTypes} from "src/use/dbConsts/{{ $config->modelNames->dashed }}";
import {useProcessingStatus} from "stores/processing/processing-status";
import {baseFormHelper} from "src/use/baseFormHelper";

const store = use{{ $config->modelNames->name }}()
const storeStatus = useProcessingStatus()

const saveModel = reactive({})
const {setOptions, saveOrUpdate, loading} = baseFormHelper(store, saveModel, dbFieldsTypes)

onMounted(async () => {
    setOptions('processing_status_id', storeStatus)
})
</script>

