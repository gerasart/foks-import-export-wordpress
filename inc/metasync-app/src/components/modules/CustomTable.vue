<template>
  <div class="q-pa-md">
    <div class="title" v-if="title">{{title}}</div>
    <q-table
      v-if="columns"
      :rows="rows"
      :columns="columns"
      row-key="name"
    />
  </div>
</template>

<script setup>
import {ref} from "vue";

const props = defineProps({
  rows: Array,
  columns: Array,
  title: String,
})

const columns_example = ref([
  { name: 'id', label: 'Id', field: 'id', sortable: true },
  { name: 'action', label: 'Action', field: 'action' },
  { name: 'message', label: 'Message', field: 'message' },
  { name: 'created_at', label: 'Created at', field: 'created_at' },
]);

const rows_example = ref([
  {
    id: 10,
    action: 'error',
    message: 'some error info',
    created_at: '12-12-1991',
  },
]);

</script>

<style scoped>
  .title {
    text-align: left;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 10px;
  }
</style>
