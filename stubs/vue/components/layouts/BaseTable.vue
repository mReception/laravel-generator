<template>
  <q-page class="q-pa-sm">
    <q-card>
      <q-card-section class="q-pa-none">
        <q-table
          flat
          card-class="bg-grey-1 text-accent"
          table-class="text-secondary"
          ref="tableRef"
          :rows="list"
          :columns="columns"
          row-key="id"
          :filter="filter"
          :loading="loading"
          @request="fetch"
          v-model:pagination="pagination"
          selection="multiple"
          v-model:selected="selected"
          @selection="handleSelection"
          :visible-columns="visibleColumns"
        >
          <template v-slot:top-left>
            <q-table-top-left :title="title" class="text-uppercase g_color_2" :selected="selected"
                              @action="handleAction"
                              :actions="actions"/>
          </template>
          <template v-slot:top-right>
            <search-filter :visible="show_filter" v-model="filter"/>
            <q-table-top-right :columns="columns"
                               v-model="visibleColumns"
                               :list="list"
                               @showFilter="show_filter=!show_filter"
                               @click-add-button="add"/>
          </template>

          <template v-slot:body-selection="scope">
            <q-checkbox color="accent" :model-value="scope.selected"
                        @update:model-value="(val, evt) => { Object.getOwnPropertyDescriptor(scope, 'selected').set(val, evt) }"/>
          </template>

          <template v-slot:header-cell-contact_name="props">
            <q-th :props="props">
              {{ props.col.label }}
              <q-input
                outlined
                dense
                label="Name"
                v-model="formRequest.name"
              />
            </q-th>

          </template>

          <template v-slot:header="props">
            <q-tr :props="props">
              <q-th>
                <q-checkbox color="accent" v-model="props.selected"/>
              </q-th>
              <q-th
                v-for="col in props.cols"
                :key="col.name"
                :props="props"
              >
                {{ col.label }}
              </q-th>
            </q-tr>
            <QTableHeaderFilters v-if="show_filter" v-model="columns" :props="props"/>
          </template>
          <template v-slot:body-cell-Action="props">
            <QTableEditDeleteButton :props="props" @edit="edit" @remove="remove"/>
          </template>
          <template v-slot:body-cell-factory="props">
            <q-td key="name" :props="props">
              {{ props.row.factory ? props.row.factory.name : "" }}
            </q-td>
          </template>

          <template :key="key" v-for="(cell, key) in customCells" v-slot:[`body-cell-${cell.field}`]="props">
            <component :is="cell.component" :props="props" :update="update" :field="cell.field"
                       :secondField="cell.secondField" :title="cell.title"/>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>


<script setup>
import {computed, nextTick, onMounted, provide, reactive, ref, toRaw, watch} from 'vue'
import {PaginationForm} from 'src/models/requests/PaginationForm'
import QTableTopLeft from 'components/base-table/QTableTopLeft.vue';
import QTableTopRight from 'components/base-table/QTableTopRight.vue';
import QTableHeaderFilters from 'components/base-table/QTableHeaderFilters.vue';
import QTableEditDeleteButton from 'components/base-table/QTableEditDeleteButton.vue';
import {buildServerPagination, openRemoveDialog} from 'src/use/crudTable';
import {useUsers} from 'stores/user';
import SearchFilter from 'components/base-table/SearchFilter.vue';
import {FormRequest} from "src/models/requests/FormRequest";

const emit = defineEmits(['edit', 'add', 'update:modelValue', 'selected']);
const props = defineProps({
  title: String,
  fields: Object,
  customCells: Array,
  store: Object,
  storeUpdate: Function,
  actions: Array
});

const pagination = ref({
  sortBy: 'created_at',
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10
})

const paginationForm = reactive(new PaginationForm(0, 10, null, 'created_at', true, ['*']))
const formRequest = reactive(new FormRequest())


const loading = ref(false)


const storeUsers = useUsers()
const filter = ref('')
const show_filter = ref(false)
const list = computed(() => props.store.list)
const serverPagination = computed(() => props.store.serverPagination)


const view = (id) => {
  storeUsers.get(id)
}

const add = () => {
  // storeUsers.clearUser()
  emit('add')
}

const edit = async (id) => {
  const index = props.store.findIndexById(id)
  if (index >= 0) {
    await props.store.setCurrentByIndex(index)
  } else {
    await props.store.get(id)
  }
  emit('edit', id)
}


const remove = async (id) => {
  await openRemoveDialog(storeUsers.delete, id)
}


const update = (val, field, id) => {
  props.store.update({[field]: val}, id)
}

onMounted(async () => {
  await fetch()
})


const columns = reactive(props.fields);

const visibleColumns = ref([])

props.fields.forEach((element) => {
  if (element.visible === undefined || element.visible) {
    visibleColumns.value.push(element.name)
  }
})

async function fetch(propsTable) {

  loading.value = true
  if (propsTable) {
    const {page, rowsPerPage, sortBy, descending} = propsTable.pagination
    paginationForm.filter = propsTable.filter
    paginationForm.descending = descending
    paginationForm.sortBy = sortBy

    // calculate starting row of data
    paginationForm.startRow = (page - 1) * rowsPerPage
    // get all rows if "All" (0) is selected
    paginationForm.fetchCount = rowsPerPage === 0 ? pagination.value.rowsNumber : rowsPerPage
  }


  const response = await props.store.fetchAll(formRequest, paginationForm)

  if (response) {
    Object.assign(pagination, buildServerPagination(pagination, serverPagination))
  }

  loading.value = false

}


const updateField = (value) => {
  fetch()
}


const tableRef = ref()
const selected = ref([])
watch(selected, (value) => {
  emit('selected', value);
})
const formDialog = computed(() => props.store.formDialog)
watch(formDialog, (value) => {
  if (value == false) {
    props.store.clearCurrent()
  }
}, { deep: true })
let storedSelectedRow

const handleSelection = ({rows, added, evt}) => {
  // ignore selection change from header of not from a direct click event
  if (rows.length !== 1 || evt === void 0) {
    return
  }

  const oldSelectedRow = storedSelectedRow
  const [newSelectedRow] = rows
  const {ctrlKey, shiftKey} = evt

  if (shiftKey !== true) {
    storedSelectedRow = newSelectedRow
  }

  // wait for the default selection to be performed
  nextTick(() => {
    if (shiftKey === true) {
      const tableRows = tableRef.value.filteredSortedRows
      let firstIndex = tableRows.indexOf(oldSelectedRow)
      let lastIndex = tableRows.indexOf(newSelectedRow)

      if (firstIndex < 0) {
        firstIndex = 0
      }

      if (firstIndex > lastIndex) {
        [firstIndex, lastIndex] = [lastIndex, firstIndex]
      }

      const rangeRows = tableRows.slice(firstIndex, lastIndex + 1)
      // we need the original row object so we can match them against the rows in range
      const selectedRows = selected.value.map(toRaw)

      selected.value = added === true
        ? selectedRows.concat(rangeRows.filter(row => selectedRows.includes(row) === false))
        : selectedRows.filter(row => rangeRows.includes(row) === false)
    } else if (ctrlKey !== true && added === true) {
      selected.value = [newSelectedRow]
    }
  })
}


const abortOptionsFilterFn = (val, update, abort, field) => {
  console.log(val + ' ' + field)
  update(() => {
    const index = columns.findIndex((column) => column.field === field)
    if (field === 'factory') {
      columns[index].options = list.value
    }

  })
}
/**
 * Actions *
 */
const multiRemove = (ids) => {
  ids.forEach(id => storeUsers.delete(id))
}

const deleteAction = (selected) => {
  openRemoveDialog(multiRemove, selected.map(el => el.id))
}
const handleAction = ({selected, action}) => {
  action.event(selected)
}

const actions = [
  {event: deleteAction, name: 'Delete All'},
]
if (props.actions) {
  props.actions.forEach(el => {
    actions.push(el)
  })
}

provide('formRequest', formRequest)
provide('updateField', updateField)
provide('columns', columns)

provide('abortOptionsFilterFn', abortOptionsFilterFn)

</script>
