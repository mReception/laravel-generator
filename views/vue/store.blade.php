
import { defineStore } from 'pinia';
import {{ $config->modelNames->camelPlural }}Service from 'src/services/{{ $config->modelNames->camel }}.service';
import {{ $config->modelNames->name }} from 'src/models/{{ $config->modelNames->dashedPlural }}';

import {{ $config->modelNames->camel }}Service from 'src/services/{{ $config->modelNames->camel }}.service';


interface State {
    {{ $config->modelNames->camelPlural }}: {{ $config->modelNames->name }}[],
    {{ $config->modelNames->camel }}: {{ $config->modelNames->name }}|null
}


export const use{{ $config->modelNames->plural }} = defineStore('{{ $config->modelNames->dashedPlural }}', {
  state: (): State => {
    return {
        {{ $config->modelNames->camelPlural }}: [],
        {{ $config->modelNames->camel }}: null,
    }
  },

  getters: {
    {{ $config->modelNames->camelPlural }}List (state) {
      return state.{{ $config->modelNames->camelPlural }};
    }
  },

  actions: {
    async fetchAll(form: []) {
      try {
          const { data } = await {{ $config->modelNames->camelPlural }}Service.getAll(form);
        debugger
        if (data.success) {
            this.{{ $config->modelNames->camelPlural }} = data.data
        }
      } catch (_) {
          console.log(_)
      }
    },
    set{{ $config->modelNames->name }} ({{ $config->modelNames->camel }}: {{ $config->modelNames->name }}) {
    this.{{ $config->modelNames->camel }} = {{ $config->modelNames->camel }}
    },

    async get(id: number) {
      try {
          const {data} = await {{ $config->modelNames->camel }}Service.get(id);
        if (data.success) {
            if (this.{{ $config->modelNames->camelPlural }}.filter({{ $config->modelNames->camel }} => {{ $config->modelNames->camel }}.id === data.data.id).length === 0 ) {
                this.{{ $config->modelNames->camelPlural }}.push(data.data)
          }
        }
      } catch (_) {
          console.log(_)
      }
    },
    async create({{ $config->modelNames->camel }}: {{ $config->modelNames->name }}) {
      try {
          const {data} = await {{ $config->modelNames->camel }}Service.create({{ $config->modelNames->camel }});
        if (data.success) {
            this.{{ $config->modelNames->camelPlural }}.push(data.data)
        }
      } catch (_) {
          console.log(_)
      }
    },
    async update(form: [], id: number) {
      try {
          const {data} = await {{ $config->modelNames->camel }}Service.update(form, id);
        if (data.success) {
            this.{{ $config->modelNames->camelPlural }}.push(data.data)
        }
      } catch (_) {
          console.log(_)
      }
    },
    async delete(id: number) {
      const {data} = await {{ $config->modelNames->camel }}Service.delete(id);
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
  }
});


