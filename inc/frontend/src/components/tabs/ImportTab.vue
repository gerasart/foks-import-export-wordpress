<!--
  - Copyright (c) 2022.
  - Created by metasync.site.
  - Developer: gerasymenkoph@gmail.com
  - Link: https://t.me/gerasart
  -->

<template>
  <div class="q-pa-md q-gutter-sm">
    <div class="mainTitle">{{ $t('title_import') }}</div>

    <div class="row">
      <div class="col-md-8">

        <div class="statistic">
          <div v-if="progress.total_count">{{ $t('total_products') }}: <strong>{{ progress.total_count }}</strong></div>
          <div v-else-if="progress.is">
            {{ $t('waiting_total') }}
            <q-spinner-ball
                color="primary"
                size="2em"
            />
          </div>
          <div v-if="progress.current_count">{{ $t('loaded_products') }}<strong>{{ progress.current_count }}</strong>
          </div>
        </div>

        <q-linear-progress v-if="progress.count" class="mb30" size="25px" :value="+progress.count.toFixed(2) / 100"
                           color="accent">
          <div class="absolute-full flex flex-center">
            <q-badge color="white" text-color="accent" :label="$t('import_progress')"/>
          </div>
        </q-linear-progress>

        <q-btn v-if="!progress.is" color="purple" @click="ImportData" :label="$t('import')"/>

      </div>

      <div class="col-md-4">
        <custom-table
            :columns="columns"
            :rows="progress.logRows"
            :title="$t('import_log')"
        />
      </div>

    </div>
  </div>
</template>

<script setup>
import {reactive, ref} from "vue";
import axios from 'axios';
import * as qs from 'qs';
import {useQuasar, QSpinnerGears} from 'quasar'
import CustomTable from "../modules/CustomTable.vue";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const $q = useQuasar();
const Settings = ref(window?.settings);
const progress = reactive({
  is: false,
  count: 0,
  current_count: 0,
  total_count: 0,
  isError: false,
  logRows: [],
  isSuccess: false,
});

const columns = ref([
  {name: 'action', label: t('action'), field: 'action', sortable: true},
  {name: 'message', label: t('message'), field: 'message'},
]);

function ImportData() {
  const notify = $q.notify({
    group: false,
    timeout: 0,
    spinner: QSpinnerGears,
    message: t('title_import')+'...',
  });

  pushLog('import', 'Start import')

  progress.is = true;

  axios.post(Settings.value?.ajaxUrl, qs.stringify({action: 'importFoks'})).then((res) => {
    progress.is = false;

    if (res?.data?.success) {
      notify({
        icon: 'done',
        spinner: QSpinnerGears,
        message: t('import_ready'),
        timeout: 1000
      });
      progress.isSuccess = true;
      progress.total_count = 0;
      progress.count = 0;
      progress.current_count = 0;
      pushLog('import', 'Finish import')
    }
  }).catch((error) => {
    progress.is = false;
    progress.isError = true;
    $q.notify({
      type: 'negative',
      message: 'Some error exception.'
    });
    pushLog('ImportData', error);
  });

  checkTotal();
}

function checkTotal() {
  if (!progress.total_count && !progress.isSuccess) {
    axios.get(Settings.value.logs_url + 'total.json').then((res) => {
      progress.total_count = res.data;

      if (!progress.total_count && !progress.isError) {
        checkTotal();
      } else {
        if (!progress.isError) {
          checkProgress();
        }
      }
    }).catch((error) => {
      if (error) {
        pushLog('checkTotal', error);
        checkTotal();
      }
    });
  }
}

function checkProgress() {
  axios.get(Settings.value.logs_url + 'current.json').then((res) => {
    const currentCount = res.data;
    progress.current_count = res.data;
    progress.count = (currentCount / progress.total_count * 100);

    if (currentCount !== progress.total_count && !progress.isError) {
      checkProgress();
    }
  }).catch((error) => {
    pushLog('checkProgress', error);
  });
}

function pushLog(action, message) {
  progress.logRows.push({
    action: action,
    message: JSON.stringify(message),
  });
}
</script>
