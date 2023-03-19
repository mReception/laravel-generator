<template>
  <div class="col-6 form-group" v-for="(item, key) in model" >
    <label class="p_relative d_block fs_16 font_family_poppins color_black mb_2">Priority*</label>
    <q-input dense outlined color="accent" v-model="form.priority"
             :error-message="getValidationErrors('priority')"
             :error="hasValidationErrors('priority')"
             :rules="[val => !!val || 'Field is required']"
    />
  </div>
</template>

<script setup>
import {convertKeyToLabel} from "src/use/capitalizeFirstLetter";
import {computed} from "vue";

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  title: String,
});
const emit = defineEmits(['update:modelValue'])

const model = computed({
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
