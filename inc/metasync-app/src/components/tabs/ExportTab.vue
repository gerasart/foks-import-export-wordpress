<template>
  <div class="q-pa-md q-gutter-sm">
    <div class="title">{{ $t('title_export') }}</div>
    <div class="field-group">
      <div v-if="!progress.is" class="export_block-link stable">
        Stable xml: <a target="_blank" :href="Settings?.logs_url+$t('export_file')">{{
          Settings?.logs_url
        }}{{ $t('export_file') }}</a>
      </div>
      <hr>
      <q-btn v-if="!progress.is" color="purple" @click="ExportData" :label="$t('export_now')"/>
      <q-inner-loading v-else :showing="true">
        <q-spinner-gears size="50px" color="primary"/>
      </q-inner-loading>
    </div>
  </div>
</template>

<script setup>
import {reactive, ref} from "vue";
import axios from "axios";

const Settings = ref(window?.settings);
const progress = reactive({is: false});

function ExportData() {
  progress.is = true;
  axios.get(Settings._rawValue.export).then(() => {
    progress.is = false;
  }).catch(error => {
    this.progress = false;
  });
}
</script>
