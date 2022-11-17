<template>
    <div>
        <p>\{\{ title \}\}</p>
        <ul>
            <li v-for="item in items" :key="item.id" @click="increment">
                \{\{ item.id \}\} - \{\{ item.content \}\}
            </li>
        </ul>
        <p>Count: {{ itemsCount }} / {{ meta.totalCount }}</p>
        <p>Active: {{ active ? 'yes' : 'no' }}</p>
        <p>Clicks on items: {{ clickCount }}</p>
    </div>
</template>

<script lang="ts">
    import {
        defineComponent,
        PropType,
        computed,
        ref,
        toRef,
        Ref,
    } from 'vue';
    import { {{ $config->modelNames->name }}, Meta } from './models';

    function useClickCount() {
        const clickCount = ref(0);
        function increment() {
            clickCount.value += 1
            return clickCount.value;
        }

        return { clickCount, increment };
    }

    function useDisplayTodo(items: Ref<{{ $config->modelNames->name }}[]>) {
        const itemsCount = computed(() => items.value.length);
        return { itemsCount };
    }

    export default defineComponent({
        name: 'ItemsComponent',
        props: {
            title: {
                type: String,
                required: true
            },
            items: {
                type: Array as PropType<{{ $config->modelNames->name }}[]>,
                default: () => []
            },
            meta: {
                type: Object as PropType<Meta>,
                required: true
            },
            active: {
                type: Boolean
            }
        },
        setup (props) {
            return { ...useClickCount(), ...useDisplayTodo(toRef(props, 'items')) };
        },
    });
</script>


