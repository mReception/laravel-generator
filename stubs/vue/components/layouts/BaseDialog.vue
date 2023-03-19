<template>
  <q-dialog v-model="showDialog" no-backdrop-dismiss persistent >
    <q-card style="min-width: 700px;">
      <q-toolbar class="bg-primary text-grey-5 q-pa-sm">
        <q-avatar>
          <img :src="linenIcon">
        </q-avatar>
        <q-toolbar-title><span class="text-weight-bold text-uppercase">{{itemId ? 'Edit': 'New'}} {{title}} </span></q-toolbar-title>

        <q-btn flat round dense icon="close" v-close-popup />
      </q-toolbar>

      <q-card-section class="scroll q-pb-none">
        <slot></slot>
      </q-card-section>

    </q-card>
  </q-dialog>
</template>

<script setup>
import {computed} from 'vue';
import linenIcon from  "src/assets/images-2/linen_icon_accent_150x150.png"

const emit = defineEmits(['update:modelValue'])
const props = defineProps({
  selected: Array,
  itemId: Number,
  title: String,
  modelValue: {
    type: Boolean,
    default: false
  },
});
const showDialog = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  },
})
</script>

<style scoped>

</style>
