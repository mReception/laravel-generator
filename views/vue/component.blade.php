<template>
    <q-card>
        <q-card-section class="q-pa-none">
            <q-table
                    ref="tableRef"
                    title="{{ $config->modelNames->name }}"
                    :rows="list"
                    :columns="columns"
                    row-key="name"
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
                    <div class="text-h4">
                        <span>Linen</span>
                        <span class="q-mx-lg-lg q-px-lg"><q-btn v-if="selected.length>0"
                                                                outline
                                                                color="primary"
                                                                icon-right="arrow_drop_down"
                                                                label="Actions" style="min-width: 150px">
            <q-menu>
              <q-list style="min-width: 100px">
                <q-item clickable v-close-popup>
                  <q-item-section @click="action(selected)">Copy</q-item-section>
                </q-item>
                <q-item clickable v-close-popup>
                  <q-item-section @click="action(selected)">Delete All</q-item-section>
                </q-item>
                <q-separator />
              </q-list>
            </q-menu>
          </q-btn></span>
                    </div>
                </template>

                <template v-slot:top-right>
                    <q-input v-if="show_filter" filled borderless dense debounce="300" v-model="filter" placeholder="Search">
                        <template v-slot:append>
                            <q-icon name="search"/>
                        </template>
                    </q-input>

                    <q-btn class="q-ml-sm" icon="filter_list" @click="show_filter=!show_filter" flat title="Filter rows"/>
                    <q-btn class="q-ml-sm" icon="add" @click="add" flat  title="Add new"/>
                    <q-select
                            v-model="visibleColumns"
                            class="q-mx-lg-lg q-px-lg"
                            title="Hide/add columns"
                            multiple
                            outlined
                            dense
                            options-dense
                            :display-value="$q.lang.table.columns"
                            emit-value
                            map-options
                            :options="columns"
                            option-value="name"
                            options-cover
                            style="min-width: 150px"
                    />
                    <q-btn
                            class="q-ml-sm"
                            icon-right="file_upload"
                            title="Export to csv"
                            flat
                            @click="exportTable"
                    />
                </template>

                <template v-slot:body-selection="scope">
                    <q-checkbox :model-value="scope.selected" @update:model-value="(val, evt) => { Object.getOwnPropertyDescriptor(scope, 'selected').set(val, evt) }" />
                </template>

                <template v-slot:header-cell-name="props">
                    <q-th :props="props">
                        @{{ props.col.label }}
                        <q-input
                                outlined
                                dense
                                label="Name"
                                v-model="form.name"
                        />
                    </q-th>
                </template>

                <template v-slot:header="props">
                    <q-tr :props="props">
                        <q-th>
                            <q-checkbox v-model="props.selected" />
                        </q-th>
                        <q-th
                                v-for="col in props.cols"
                                :key="col.name"
                                :props="props"
                        >
                            @{{ col.label }}
                        </q-th>
                    </q-tr>
                    <q-tr :props="props" v-if="show_filter">
                        <q-th></q-th>
                        <q-td
                                class="col-lg-12"
                                style="padding-left: 2px; padding-right: 2px; border-bottom-style: solid;"
                                v-for="col in props.cols"
                                :key="col.name"
                                :props="props"

                        >
                            <q-input v-if="col.filter_type === undefined  || col.filter_type === 'text'"
                                     v-model="form[col.field]"
                                     @update:model-value="(value) => { updateField(value, col.field) }"
                                     @clear="(value) => { updateField(value, col.field) }"
                                     dense clearable square filled/>
                            <q-select v-if="col.filter_type === 'select'"
                                      v-model="form[col.field]"
                                      :label="col.label"
                                      :options="col.options"
                                      dense
                                      clearable
                                      use-chips

                                      use-input

                                      fill-input
                                      input-debounce="0"
                                      @filter="(val, update, abort) => { optionsFiltering(val, update, abort, col.field) }"
                                      @filter-abort="(val, update, abort) => { abortOptionsFilterFn(val, update, abort, col.field) }"

                                      @update:model-value="(value) => { updateField(value, col.field) } "
                                      @clear="(value) => { updateField(value, col.field) }"

                                      :option-value="(item) => item === null ? '-1' : item.id"
                                      :option-label="(item) => item === null ? '-' : item.name"
                                      :multiple ="col.multiple"
                            />


                            <q-input v-if="col.filter_type === 'date'"
                                     style="width: 200px; padding-bottom: 0px;"
                                     filled
                                     v-model="form[col.field]"
                                     mask="date"
                                     :rules="[col.filter_type]"
                                     dense
                                     default-year-month
                                     clearable
                                     @clear="(value) => { updateField(value, col.field) }"
                                     @update:model-value="(value) => { updateField(value, col.field) }"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date v-model="form[col.field]" dense @update:model-value="(value) => { updateField(value, col.field) }">
                                                <div class="row items-center justify-end">
                                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                                    <q-btn label="OK" color="primary" flat @click="(value) => { updateField(value, col.field) }" v-close-popup />
                                                </div>
                                            </q-date>
                                        </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>

                            <q-input v-if="col.filter_type === 'date_range'"
                                     style="width: 200px;"
                                     dense
                                     filled v-model="form[col.field]"
                                     mask="date"
                                     :rules="['date']"
                                     default-year-month
                                     clearable
                                     @clear="(value) => { updateField(value, col.field) }"
                                     @update:model-value="(value) => { updateField(value, col.field) }">
                                <template v-slot:append>
                                    <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date v-model="form[col.field]" range dense @update:model-value="(value) => { updateField(value, col.field) }">
                                                <div class="row items-center justify-end">
                                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                                    <q-btn label="OK" color="primary" flat @click="(value) => { updateField(value, col.field) }" v-close-popup />
                                                </div>
                                            </q-date>
                                        </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>
                        </q-td>
                    </q-tr>
                </template>


                <template v-slot:body-cell-Action="props">
                    <q-td :props="props">
                        <q-btn icon="edit" size="sm" flat dense @click="edit(props.row.id)" />
                        <q-btn icon="delete" size="sm" flat dense @click="remove(props.row.id)" />
                    </q-td>
                </template>

                <template v-slot:body-cell-created_at="props">
                    <q-td key="name" :props="props">
                        @{{ formattedDate(props.row.created_at, props.row.formatDate ?? 'DD/MM/YY HH:mm:ss') }}
                    </q-td>
                </template>

                <template v-slot:body-cell-updated_at="props">
                    <q-td key="name" :props="props">
                        @{{ formattedDate(props.row.updated_at, props.row.formatDate ?? 'DD/MM/YY HH:mm:ss') }}
                    </q-td>
                </template>

                <template v-slot:body-cell-name="props">
                    <q-td key="name" :props="props">
                        @{{ props.row.name }}
                        <q-popup-edit v-model="props.row.name" title="Edit the Name" auto-save v-slot="scope" @save="(val) => update(val, 'name', props.row.id)">
                            <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set"/>
                        </q-popup-edit>
                    </q-td>
                </template>


            </q-table>
        </q-card-section>

        <{{$config->modelNames->dashedPlural}}-form-dialog
                v-model:visible="formDialog.visible"
                v-model:id="formDialog.id"/>
    </q-card>
</template>


<script setup>
    import {
        defineComponent,
        PropType,
        computed,
        ref,
        toRef,
        Ref,
    } from 'vue';
    import { {{ $config->modelNames->name }}, Meta } from 'src/models';

    import {computed, defineComponent, nextTick, onMounted, reactive, ref, toRaw} from 'vue'
    import { {{ $config->modelNames->plural }} }from 'src/stores/{{ $config->modelNames->camelPlural }}'
    import {{$config->modelNames->name}}FormDialog from 'src/components/{{$config->modelNames->name}}/{{$config->modelNames->name}}FormDialog.vue'
    import {PaginationForm} from 'src/models/requests/PaginationForm'
    import { {{$config->modelNames->name}}FormRequest} from 'src/models/requests/{{$config->modelNames->name}}FormRequest'

    import {date, exportFile, useQuasar} from 'quasar'

    import useWrapCsvValue from 'src/utils/useWrapCsvValue'
    const wrapCsvValue = useWrapCsvValue()

    const $q = useQuasar()
    const pagination = ref({
        sortBy: 'created_at',
        descending: true,
        page: 1,
        rowsPerPage: 10,
        rowsNumber: 10
    })
    const paginationForm = reactive(new PaginationForm(0,10,null,'created_at',true,['*']))
    const loading = ref(false)
    const formDialog = reactive({visible: false, id: null})
    const form = reactive(new {{$config->modelNames->name}}FormRequest())
    const filter = ref('')
    const show_filter = ref(false)
    const store = use{{ $config->modelNames->plural }}()
    const list = computed(() => store.{{ $config->modelNames->camelPlural }}ist)
    const serverPagination = computed(() => store.serverPagination)

    const view = (id) => {
        store.get(id)
        formDialog.id = id
        formDialog.visible = true
    }

    const add = () => {
        formDialog.id = null
        formDialog.visible = true
    }

    const edit = async (id) => {
        const index = store.findIndexById(id)
        if (index >= 0) {
            store.setCurrentLinenByIndex(index)
        } else {
            await store.get(id)
        }
        formDialog.id = id
        formDialog.visible = true
    }

    const remove = async (id) => {
        await Dialog.create({
            title: 'Confirm',
            message: 'Are you sure you want to delete this item?',
            cancel: true,
            persistent: true
        }).onOk(() => {
            store.delete(id)
        }).onCancel(() => {
            console.log('')
        }).onDismiss(() => {
            console.log('')
        })
    }

    const update = (val, field, id) => {
        store.update({[field]: val}, id)
    }

    onMounted(async () => {
        await fetch()
    })

    const optionsFiltering = (val, update, abort, field) => {
        update(() => {
            const index = columns.findIndex((column) => column.field === field)
            if (val === '') {
                columns[index].options = columns[index]['options']
            } else {
                const needle = val.toLowerCase()
                columns[index].options = columns[index]['options'].filter((element) => element.name.toLowerCase().indexOf(needle) > -1)
            }

            // "ref" is the Vue reference to the QSelect
            ref => {
                if (val !== '' && ref.options.length > 0 && ref.getOptionIndex() === -1) {
                    ref.moveOptionSelection(1, true) // focus the first selectable option and do not update the input-value
                    ref.toggleOption(ref.options[ref.optionIndex], true) // toggle the focused option
                }
            }
        })
    }

    const abortOptionsFilterFn = (val, update, abort, field) => {
        console.log(val + ' ' + field)
        update(() => {
            const index = columns.findIndex((column) => column.field === field)
            // if (field === 'factory') {
            //     columns[index].options = partnersList.value
            // }
        })
    }

    const fields = [
        @foreach($properties as $name => $property)
        {
            name: '{{ $property['row_name'] }}',
            label: '{{ $property['js_type'] }}',
            field: '{{ $property['row_name'] }}',
            @if ($property['filter_type']==='select')
            filter_type: 'select',
            options: [],
            multiple: true,
            @endif
            @if ($property['filter_type']==='date')
            filter_type: 'date',
            @endif
            sortable: true,
            sort: (a, b) => parseInt(a, 10) - parseInt(b, 10)
        }
        @endforeach
    ]

    const columns = reactive(fields);

    const visibleColumns = ref([])

    fields.forEach((element) => {
        if (element.visible === undefined || element.visible) {
            visibleColumns.value.push(element.name)
        }
    })

    async function fetch(props) {

        if (props) {
            const {page, rowsPerPage, sortBy, descending} = props.pagination
            paginationForm.filter = props.filter
            paginationForm.descending = descending
            paginationForm.sortBy = sortBy

            // calculate starting row of data
            paginationForm.startRow = (page - 1) * rowsPerPage
            // get all rows if "All" (0) is selected
            paginationForm.fetchCount = rowsPerPage === 0 ? pagination.value.rowsNumber : rowsPerPage
        }
        // fetch data from "server"
        const response = await store.fetchAll(form, paginationForm)
        if (response) {
            // update rowsCount with appropriate value
            pagination.value.rowsNumber = serverPagination.value.rowsNumber

            // don't forget to update local pagination object
            pagination.value.page = serverPagination.value.page
            pagination.value.rowsPerPage = serverPagination.value.rowsPerPage
            pagination.value.sortBy = serverPagination.value.sortBy
            pagination.value.descending = serverPagination.value.descending

            // ...and turn of loading indicator
            loading.value = false
        }
    }

    const updateField = (value) => {
        console.log(value)
        fetch()
    }

    const formattedDate = (dateString, format) => {
        const dateModel = new Date(dateString)
        return date.formatDate(dateModel, format ?? 'YYYY-MM-DD')
    }

    const tableRef = ref()
    const selected = ref([])
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

    const action = (selected) => {
        console.log(selected)
    }

    const exportTable = () => {
        // naive encoding to csv format
        const content = [columns.map(col => wrapCsvValue(col.label))].concat(
            list.value.map(row => columns.map(col => wrapCsvValue(
                typeof col.field === 'function'
                    ? col.field(row)
                    : row[col.field === void 0 ? col.name : col.field],
                col.format,
                row
            )).join(','))
        ).join('\r\n')

        const status = exportFile(
            'table-export.csv',
            content,
            'text/csv'
        )

        if (status !== true) {
            $q.notify({
                message: 'Browser denied file download...',
                color: 'negative',
                icon: 'warning'
            })
        }
    }

</script>


