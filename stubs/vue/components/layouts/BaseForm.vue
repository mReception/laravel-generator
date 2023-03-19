<template>
    <div class="row q-gutter-y-md q-col-gutter-lg" style="align-items: flex-end">
      <template v-for="(item, key) in model" :key="key">
        <div v-if="!item.hidden" class="form-group" :class="item.class ?  item.class :  'col-6'">

            <q-select
              v-if="item.type === 'select'"
              :label="convertKeyToLabel(key)"
              dense
              :loading=" !(item.options.length >= 1)"
              v-model="item.value"
              :options="item.options"
              :option-value="(item) => item.id !== undefined ? item.id : item"
              :option-label="(item) =>  item.name ? item.name : item"
              class=" full-width"
              :error-message="getValidationErrors(key)"
              :error="hasValidationErrors(key)"
            />
            <template v-else-if="item.type === 'toggle'">
              <label class="p_relative d_block fs_16 font_family_poppins color_black mb_2" > {{ convertKeyToLabel(key) }}</label>
              <q-toggle
                :label=" item.value === YesNoEnum.YES ? 'YES' : 'NO'"
                :false-value="item.options[0]"
                :true-value="item.options[1]"
                v-model="item.value"
                class="full-width row no-wrap items-start"
                style="height: 70px"
                :error-message="getValidationErrors(key)"
                :error="hasValidationErrors(key)"
              />
            </template>
            <q-item tag="label" v-ripple  v-else-if="item.type === 'checkbox'" >
              <q-item-section>
                <q-item-label class="p_relative d_block fs_16 font_family_poppins color_black mb_2">{{convertKeyToLabel(key)}}</q-item-label>
                <q-item-label v-if="hasValidationErrors('priority_urgency')"
                              color="orange-9"
                              class="text-negative">
                  {{getValidationErrors(key)}}
                </q-item-label>
              </q-item-section>
              <q-item-section avatar top>
                <q-checkbox v-model="item.value" />
              </q-item-section>
            </q-item>
            <q-input v-else-if="key === 'dateRange'"
                     :model-value="dateRageValue(item)"
                     :label="convertKeyToLabel(key)"
                     class="full-width"
                     :error-message="getValidationErrors('from')  ||  getValidationErrors('to')"
                     :error="hasValidationErrors('from') ||  hasValidationErrors('to')"
            >
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                    <q-date v-model="item.value" range>
                      <div class="row items-center justify-end">
                        <q-btn v-close-popup label="Close" color="primary" flat></q-btn>
                      </div>
                    </q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
            <q-input v-else-if="item.type === 'date'"
                     v-model="item.value"
                     :label="convertKeyToLabel(key)"
                     class="full-width"
                     mask="date"
                     dense :rules="['date']"
                     :error-message="getValidationErrors(key)"
                     :error="hasValidationErrors(key)"
            >
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                    <q-date v-model="item.value">
                      <div class="row items-center justify-end">
                        <q-btn v-close-popup label="Close" color="primary" flat></q-btn>
                      </div>
                    </q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>

            <template v-else>
              <label class="p_relative d_block fs_16 font_family_poppins color_black mb_2">{{convertKeyToLabel(key)}}</label>
              <q-input
                dense outlined color="accent"

                :rules="[val => !!val || 'Field is required']"
                v-model="item.value"
                v-bind="itemProps(item,key)"
                :error-message="getValidationErrors(key)"
                :error="hasValidationErrors(key)"
              />
            </template>
        </div>
      </template>


    </div>
</template>

<script setup>
import {convertKeyToLabel} from "src/use/capitalizeFirstLetter";
import {computed, reactive} from "vue";
import {validationHelper} from "src/utils/validationHelper";
import { YesNoEnum} from '/src/use/dbConsts/ConsignmentsEnums'
const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  errors: Object,
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

const errorsLocal = computed(() => props.errors)

const {
  getValidationErrors,
  hasValidationErrors
} = validationHelper(errorsLocal)


const itemProps = (item, key) => {
  let props = {
    dark: item.dark ? this.dark : undefined,
    dense: item.dense ?? false,
    reverseFillMask: item.reverseFillMask ?? false,
    mask: item.mask ?? '',
    autogrow: false
  }
  if(item.type =='number' ) {
    props['type'] = 'number'
  }

  if (item.type == 'disable') {
    props['disable'] = true
  }
  return props
}

</script>

<style scoped>

</style>
