<template>
  <div class="q-pa-md">
    <div class="mainTitle">{{ $t('title_logs') }}</div>
    <q-btn class="mb30" color="purple" @click="removeLogs" :label="$t('clear_logs')"/>
    <q-table
      :rows="data.rows"
      :columns="columns"
      :loading="loading"
      row-key="name"
      :filter="filter"
      :auto-width="false"
    >
      <template v-slot:top-right>
        <q-input borderless dense debounce="300" v-model="filter" :placeholder="$t('search')">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

      <template v-slot:body-cell-action="props">
        <q-td :props="props">
          <div>
            <q-badge v-if="props.value === 'error'" color="red" :label="props.value" />
            <q-badge v-else :label="props.value" />
          </div>
        </q-td>
      </template>

    </q-table>
  </div>
</template>

<script setup lang="ts">
import {onMounted, reactive, ref} from "vue";
import axios from "axios";
import * as qs from "qs";
import {useQuasar} from "quasar";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const $q = useQuasar();
const filter = ref('');
const Settings = ref(window?.settings);
const columns = ref([
  { name: 'id', label: 'Id', field: 'id', sortable: true },
  { name: 'action', label: t('action'), field: 'action', sortable: true },
  { name: 'message', label: t('message'), field: 'message', style: {
      width: '50%',
      'white-space': 'pre-wrap'
    } },
  { name: 'created_at', label: t('created_at'), field: 'created_at', sortable: true },
]);
const data = reactive({rows: []});
const loading = ref(false)

loading.value = true;
onMounted(() => {
  getLogs();
});

function removeLogs() {
  loading.value = true;

  $q.dialog({
    title: t('confirm'),
    message: t('do_you_want_remove_logs'),
    ok: t('text_yes'),
    cancel: t('text_cancel'),
    persistent: true
  }).onOk(() => {
    axios.post(Settings.value?.ajaxUrl, qs.stringify({
      action: 'removeLogs',
    })).then(() => {
      data.rows = [];
      loading.value = false;
    }).catch(() => {
      loading.value = false;
    });
  }).onCancel(() => {
    loading.value = false;
  });
}

function getLogs() {
  axios.post(Settings.value?.ajaxUrl, qs.stringify({action: 'getLogs'})).then((res) => {
    data.rows = res?.data?.data || [];
    loading.value = false;
  });
}
</script>
