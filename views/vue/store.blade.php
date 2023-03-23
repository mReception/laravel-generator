import { defineStore } from 'pinia';
import {useQuasar} from 'quasar'
import {{ $config->modelNames->camelPlural }}Service from 'src/services/{{ $config->modelNames->dashed }}.service';
import { {{ $config->modelNames->name }} }  from 'src/models/{{ $config->modelNames->dashed }}';
import { {{ $config->modelNames->name }}FormRequest } from 'src/models/requests/{{ $config->modelNames->name }}FormRequest';
import OptionsSelect from 'src/models/common/options-select';
import {PaginationForm} from 'src/models/requests/PaginationForm';
import Pagination from 'src/models/common/pagination';
import Errors from 'src/models/common/errors';

const $q = useQuasar()

interface State {
    {{ $config->modelNames->camelPlural }}: {{ $config->modelNames->name }}[],
    current{{ $config->modelNames->name }}: {{ $config->modelNames->name }}|null,
    {{ $config->modelNames->camelPlural }}Options: OptionsSelect[],
    pagination: Pagination | null,
    errors: Errors,
    formDialog: boolean
}


export const use{{ $config->modelNames->plural }} = defineStore('{{ $config->modelNames->dashedPlural }}', {
  state: (): State => {
    return {
        {{ $config->modelNames->camelPlural }}: [],
        current{{ $config->modelNames->name }}: null,
        {{ $config->modelNames->camelPlural }}Options: [],
        pagination: null,
        errors: new Errors('',[]),
        formDialog: false
    }
  },

  getters: {
    list (state) {
      return state.{{ $config->modelNames->camelPlural }};
    },
    getErrors(state) {
        return state.errors;
    },
    getOptions(state) {
        return state.{{ $config->modelNames->camelPlural }}Options;
    },
    getCurrentItem(state) {
        return state.current{{ $config->modelNames->name }};
    },
    showFormDialog(state) {
       return state.formDialog;
    }

  },

  actions: {
    async fetchAll(form: {{ $config->modelNames->name }}FormRequest, paginationForm: PaginationForm) {
      try {
          const { data } = await {{ $config->modelNames->camelPlural }}Service.getAll(form, paginationForm);
          if (data.success) {
            this.{{ $config->modelNames->camelPlural }} = data.data
            this.pagination = {
                sortBy: paginationForm.sortBy ?? 'desc',
                descending: paginationForm.descending ?? false,
                page: data.current_page,
                rowsPerPage: data.per_page,
                rowsNumber: data.total,
            }
          }
      } catch (error: any) {
        console.error(error)
      }
    },
    set{{ $config->modelNames->name }} ({{ $config->modelNames->camel }}: {{ $config->modelNames->name }}) {
        this.current{{ $config->modelNames->name }} = {{ $config->modelNames->camel }}
    },
    async fetchOptions(fetchIfNotEmpty = false)
      {
          if (fetchIfNotEmpty && this.getOptions.length !== 0) {
              return
          }
          try {
              const paginationForm = new PaginationForm(null, null, null, 'name', false, ['id', 'name'])
              const {data} = await {{ $config->modelNames->camelPlural }}Service.getOptions({status: ['active', 10]}, paginationForm)
              if (data.success) {
                  this.{{ $config->modelNames->camelPlural }}Options = []
                  data.data.forEach((element: {{ $config->modelNames->name }} ) => this.{{ $config->modelNames->camelPlural }}Options.push({
                      id: element.id,
                      name: element.name,
                      field: '{{ $config->modelNames->camel }}_id',
                      value: (element.id).toString()
                  }))
              }
          } catch (_) {
              console.error(_)
          }
      },
    async get(id: number) {
      try {
          const {data} = await {{ $config->modelNames->camelPlural }}Service.get(id);
        if (data.success) {
            if (this.{{ $config->modelNames->camelPlural }}.filter({{ $config->modelNames->camel }} => {{ $config->modelNames->camel }}.id === data.data.id).length === 0 ) {
                this.{{ $config->modelNames->camelPlural }}.push(data.data)
          }
        }
      } catch (_) {
          console.error(_)
      }
    },
    async create({{ $config->modelNames->camel }}: {{ $config->modelNames->name }}FormRequest) {

        try {
            this.clearErrors()
            const {data} = await {{ $config->modelNames->camelPlural }}Service.create({{ $config->modelNames->camel }});
            if (data.success) {
                this.{{ $config->modelNames->camelPlural }}.push(data.data)
                this.formDialog = false
                $q.notify({
                    type: 'success',
                    message: '{{ $config->modelNames->human }} was created.',
                })
            }
        } catch (error: any) {
            console.error(error)
            if(error.response.status===422) {
                this.setErrors (error.response.data.errors)
                $q.notify({
                type: 'negative',
                    message: '{{ $config->modelNames->human }} was not created. Please, check form fields.',
                })
            }
            if(error.response.status===500) {
                this.setErrors(error.response.data.errors)
                $q.notify({
                    type: 'negative',
                    message: '{{ $config->modelNames->human }} was not created. Errors occured.',
                })
            }
        }
    },
    async update(form: {{ $config->modelNames->name }}FormRequest, id: number) {
        try {
            this.clearErrors()
             const {data} = await {{ $config->modelNames->camelPlural }}Service.update(form, id);
             if (data.success) {
                const index = this.{{ $config->modelNames->camelPlural }}.findIndex((item)=> item.id === id)
                if(index >=0) {
                    this.{{ $config->modelNames->camelPlural }}[index] = data.data
                } else {
                    this.{{ $config->modelNames->camelPlural }}.push(data.data)
                }
                this.formDialog = false
                this.current{{ $config->modelNames->name }} = null
                $q.notify({
                    type: 'success',
                    message: '{{ $config->modelNames->human }} was updated.',
                })
             }
        } catch (error: any) {
            console.error(error)
            if(error.response.status === 422)
            {
                this.setErrors(error.response.data.errors)
                $q.notify({
                    type: 'negative',
                    message: '{{ $config->modelNames->human }} was not created. Please, check form fields.',
                })
            }
            if (error.response.status === 500) {
                this.setErrors(error.response.data.errors)
                $q.notify({
                    type: 'negative',
                    message: '{{ $config->modelNames->human }} was not created. Errors occured.',
                })
            }
        }
    },
    async delete(id: number) {
      const {data} = await {{ $config->modelNames->camelPlural }}Service.delete(id);
      if (data.success) {
          const index = this.findIndexById(id);
          if (index === -1) return;
          this.{{ $config->modelNames->camelPlural }}.splice(index, 1);
      }
    },
    findIndexById(id: number) {
      return this.{{ $config->modelNames->camelPlural }}.findIndex((item: {{ $config->modelNames->name }}) => item.id === id);
    },
    findIndexByName(name: string) {
      return this.{{ $config->modelNames->camelPlural }}.findIndex((item: {{ $config->modelNames->name }}) => item.name === name);
    },
    setErrors(errors: Errors) {
        this.errors = errors
    },
    clearErrors() {
        this.errors = {message: '', errors: [], status: 200}
    },
    setCurrentByIndex(index: number) {
          this.current{{ $config->modelNames->name }} = this.{{ $config->modelNames->camelPlural }}[index]
    },
    clearCurrent() {
          this.current{{ $config->modelNames->name }} = null
    },
  }
});


