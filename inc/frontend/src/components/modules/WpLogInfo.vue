<!--
  - Copyright (c) 2022.
  - Created by metasync.site.
  - Developer: gerasymenkoph@gmail.com
  - Link: https://t.me/gerasart
  -->

<template>
    <q-dialog v-model="isDialog" transition-show="rotate" transition-hide="rotate">
      <q-card>
        <q-card-section>
          <div class="text-h6">Wp logs</div>
        </q-card-section>

        <q-separator/>

        <q-card-section style="max-height: 60vh; width:  700px" class="scroll">
          <div v-if="content">
            {{ content }}
          </div>

          <q-inner-loading v-else :showing="true">
            <q-spinner-gears size="50px" color="primary"/>
          </q-inner-loading>

        </q-card-section>

        <q-separator/>

        <q-card-actions align="right">
          <q-btn flat label="Close" color="primary" v-close-popup/>
        </q-card-actions>
      </q-card>
    </q-dialog>
</template>

<script setup>
import {onMounted, ref, defineProps} from 'vue'
import axios from "axios";
import * as qs from "qs";
import {useVModel} from "../../composable/useVModel";

const content = ref(false);
const Settings = ref(window?.settings);
const props = defineProps({
  dialog: {
    type: Boolean,
    default: false
  },
});

const isDialog = useVModel(props, 'dialog')

onMounted(() => {
  axios.post(Settings.value?.ajaxUrl, qs.stringify({
    action: 'getWPLogs',
  })).then((res) => {
    content.value = res.data.data;
  });
});
</script>
