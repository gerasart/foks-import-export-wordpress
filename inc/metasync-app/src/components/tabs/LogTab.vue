<template>
  <div class="q-pa-md">
    <q-table
      :rows="rows"
      :columns="columns"
      row-key="name"
    />
  </div>
</template>

<script setup>
import {ref} from "vue";

const columns = ref([
  { name: 'id', label: 'Id', field: 'id', sortable: true },
  { name: 'action', label: 'Action', field: 'action' },
  { name: 'message', label: 'Message', field: 'message' },
  { name: 'created_at', label: 'Created at', field: 'created_at' },
]);

const rows = ref([
  {
    id: 10,
    action: 'pizda',
    message: 'dassasasadsdsdsdasad',
    created_at: '12-12-1991',
  },
]);

</script>

<style scoped>

</style>
