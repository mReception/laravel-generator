<template>
  <div class="billing-info p_relative d_block">
    <q-card-section class="q-pa-md">
      <div class="row q-gutter-y-md q-col-gutter-lg" style="align-items: flex-end">
          @foreach($properties as $name => $property)
                  @if($property['filter_type']==='select' && str_ends_with($property['field_name'],'_id') && $property['field_name']!=='id')
                      <div class="col-6 form-group">
                          <q-select v-model="form.{{$property['js_name']}}" :options="{{ $property['camel_plural'] }}" label="{{ $property['human'] }}"
                                    :option-value="(item) => item === null ? '-1' : item.id"
                                    :option-label="(item) => item === null ? '-' : item.name"
                                    :error-message="getValidationErrors('{{$property['field_name']}}')"
                                    :error="hasValidationErrors('{{$property['field_name']}}')"
                                    :rules="[val => !!val || 'Field is required']"
                          />
                      </div>
                  @elseif($property['js_type']==='boolean' && $property['js_name']!=='id')
                      <div class="row q-gutter-y-xs q-col-gutter-lg">
                          <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                              <div class="pull-left">
                                  <q-checkbox keep-color color="accent"
                                              v-model="form.{{$property['js_name']}}"
                                              label="{{ $property['human'] }}"
                                              :error-message="getValidationErrors('{{$property['js_name']}}')"
                                              :error="hasValidationErrors('{{$property['js_name']}}')"
                                  />
                                  <q-item tag="label" v-ripple>
                                      <q-item-section>
                                          <q-item-label class="p_relative d_block fs_16 font_family_poppins color_black mb_2">{{ $property['human'] }}*</q-item-label>
                                          <q-item-label v-if="hasValidationErrors('priority_urgency')"
                                                        color="orange-9"
                                                        class="text-negative">
                                              <?php echo "{{"?> getValidationErrors('{{$property['js_name']}}') <?php echo "}}"?>
                                          </q-item-label>
                                      </q-item-section>
                                      <q-item-section avatar top>
                                          <q-checkbox v-model="form.{{$property["js_name"]}}"  />
                                      </q-item-section>
                                  </q-item>
                              </div>
                          </div>
                      </div>
                  @elseif($property['filter_type']==='date'
                            && $property['js_name']!=='created_at'
                            && $property['js_name']!=='updated_at'
                            && $property['js_name']!=='deleted_id'
                            )
                      <div class="col-6 form-group">
                          <q-input filled v-model="form.{{$property['js_name']}}"
                                   label="{{ $property['human'] }}"
                                   :error-message="getValidationErrors('{{$property['js_name']}}')"
                                   :error="hasValidationErrors('{{$property['js_name']}}')"
                          >
                              <template v-slot:prepend>
                                  <q-icon name="event" class="cursor-pointer">
                                      <q-popup-proxy cover transition-show="scale" transition-hide="scale" ref="q{{ $property['row_name'] }}Proxy">
                                          <q-date
                                                  v-model="form.{{$property['js_name']}}"
                                                  mask="DD/MM/YYYY"
                                                  @update:model-value="$refs.{{ $property['row_name'] }}.hide();"
                                          >
                                              <div class="row items-center justify-end">
                                                  <q-btn v-close-popup label="Close" color="primary" flat />
                                              </div>
                                          </q-date>
                                      </q-popup-proxy>
                                  </q-icon>
                              </template>
                          </q-input>
                      </div>
                  @else
                      @if ($property['js_name']!=='id')
                      <div class="col-6 form-group">
                          <label class="p_relative d_block fs_16 font_family_poppins color_black mb_2">{{ $property['human'] }}*</label>
                          <q-input dense outlined color="accent" v-model="form.{{$property['js_name']}}"
                                   :error-message="getValidationErrors('{{$property['js_name']}}')"
                                   :error="hasValidationErrors('{{$property['js_name']}}')"
                                   :rules="[val => !!val || 'Field is required']"
                          />
                      </div>
                      @endif

                  @endIf
          @endforeach
      </div>
    </q-card-section>
    <q-separator/>
    <q-card-actions align="right">
          <q-btn flat class="text-accent" v-close-popup>Cancel</q-btn>
          <q-btn flat class="text-accent" @click="update" v-if="form.id">Update</q-btn>
          <q-btn flat class="text-accent" @click="save" v-else>Save</q-btn>
      </q-card-actions>
  </div>
</template>

<script setup>
    import {reactive, ref, computed, onMounted, watch} from "vue";
    import {validationHelper} from 'src/utils/validationHelper';
    import { use{{ $config->modelNames->camelPlural }} } from "src/stores/{{ $config->modelNames->camelPlural }}";
    import { {{ $config->modelNames->name }}RequestForm} from "src/models/requests/{{ $config->modelNames->name }}RequestForm";


    @foreach($properties as $name => $property)
        @if($property['filter_type']==='select' && str_ends_with($property['js_name'],'_id'))
    import { use{{ $property['name_plural'] }} } from "src/stores/{{ $property['camel_plural'] }}";
        @endif
    @endforeach

    const store = use{{ $config->modelNames->camelPlural }}()
    const currentItem = computed(() => store.current{{ $config->modelNames->name }})
    const form = reactive(new {{ $config->modelNames->name }}Form())
    const errors = computed(() => store.errors)
    const {
        showValidationError,
        getValidationErrors,
        hasValidationErrors,
        hasErrors
    } = validationHelper(errors)

    @foreach($properties as $name => $property)
        @if($property['filter_type']==='select' && str_ends_with($property['js_name'],'_id'))
    const store{{ $property['name_plural'] }} = use{{ $property['name_plural'] }}()
      @endif
    @endforeach


    const save = () => {
        store.create(form)
    }

    const update = () => {
        store.update(form, currentItem.value.id)
    }

    onMounted(async () => {
    @foreach($properties as $name => $property)
        @if($property['filter_type']==='select' && str_ends_with($property['js_name'],'_id'))
        await store{{ $property['name_plural'] }}.fetchOptions()
        @endif
    @endforeach
        if (currentItem.value && currentItem.value.id){
            @foreach($properties as $name => $property)
                form.{{$property['js_name']}} = currentItem.value.{{$property['js_name']}}
            @endforeach
        }
    })

</script>

<style scoped>

</style>
