<template>
  <div class="q-pa-md q-gutter-sm">
    <div class="mainTitle">{{ $t('title_export') }}</div>
    <div class="field-group">
      <div v-if="!isProgress && Settings?.isExportFile" class="export_block-link stable">
        <q-avatar>
          <img class="" src="../../assets/images/xml.png" alt="xml">
        </q-avatar>
        <a target="_blank" :href="Settings?.logs_url+$t('export_file')">
        {{ Settings?.logs_url }}{{ $t('export_file') }}</a>
      </div>
      <hr>
      <q-btn v-if="!isProgress" color="purple" @click="ExportData" :label="$t('export_now')"/>
      <q-inner-loading v-else :showing="true">
        <q-spinner-gears size="50px" color="primary"/>
      </q-inner-loading>
    </div>
  </div>
</template>

<script setup>
import {ref} from "vue";
import axios from "axios";

const Settings = ref(window?.settings);
const isProgress = ref(false);

function ExportData() {
  isProgress.value = true;
  axios.get(Settings.value?.export).then(() => {
    isProgress.value = false;
    Settings.value.isExportFile = true;
  }).catch(() => {
    this.progress = false;
  });
}
</script>
