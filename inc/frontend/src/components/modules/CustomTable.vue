<!--
  - Copyright (c) 2022.
  - Created by metasync.site.
  - Developer: gerasymenkoph@gmail.com
  - Link: https://t.me/gerasart
  -->

<template>
  <div class="q-pa-md">
    <q-table
      v-if="columns"
      :rows="rows"
      :columns="columns"
      :title="title"
      row-key="name"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  rows: Array,
  columns: Array,
  title: String,
});
</script>
