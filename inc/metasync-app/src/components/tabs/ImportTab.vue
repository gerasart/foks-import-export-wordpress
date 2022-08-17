<template>
  <div class="q-pa-md q-gutter-sm">
    <div class="row">
      <div class="col-md-8">

        <div class="statistic">
          <div v-if="progress.total_count">Total products: <strong>{{ progress.total_count }}</strong></div>
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

        <q-linear-progress v-if="progress.count" size="25px" :value="+progress.count.toFixed(2) / 100" color="accent">
          <div class="absolute-full flex flex-center">
            <q-badge color="white" text-color="accent" :label="'import progress'"/>
          </div>
        </q-linear-progress>

        <q-btn v-if="!progress.is" color="purple" @click="ImportData" :label="$t('import')"/>

      </div>

      <div class="col-md-4">
        <custom-table
          :columns="columns"
          :rows="progress.logRows"
          title="Import logs"
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

const $q = useQuasar();
const Settings = ref(window?.settings);
const progress = reactive({
  is: false,
  count: 0,
  current_count: 0,
  total_count: 0,
  isError: false,
  logRows: [],
});

const columns = ref([
  { name: 'action', label: 'Action', field: 'action', sortable: true },
  { name: 'message', label: 'Message', field: 'message' },
]);

function ImportData() {
  const notify = $q.notify({
    group: false,
    timeout: 0,
    spinner: QSpinnerGears,
    message: 'Import...',
  });

  progress.is = true;

  axios.post(window.ajaxurl, qs.stringify({action: 'importFoks'})).then((res) => {
    progress.is = false;

    if (res?.data?.success) {
      notify({
        icon: 'done',
        spinner: QSpinnerGears,
        message: 'Import ready!',
        timeout: 1000
      });
    }
  }).catch(() => {
    progress.is = false;
    progress.isError = true;
    $q.notify({
      type: 'negative',
      message: 'Some error exception.'
    });
  });

  checkTotal();
}

function checkTotal() {
  if (!progress.total_count) {
    axios.get(Settings._rawValue.logs_url + 'total.json').then((res) => {
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
        progress.logRows.push({
          action: 'error checkTotal',
          message: JSON.stringify(error),
        })
        this.checkTotal();
      }
    });
  }
}

function checkProgress() {
  axios.get(Settings._rawValue.logs_url + 'current.json').then((res) => {
    const currentCount = res.data;
    progress.current_count = res.data;
    progress.count = (currentCount / progress.total_count * 100);
    progress.logRows.push({
      action: 'progress',
      message: progress.count,
    })

    if (currentCount !== progress.total_count && !progress.isError) {
      checkProgress();
    }
  }).catch((error) => {
    progress.logRows.push({
      action: 'error checkProgress',
      message: JSON.stringify(error),
    })
  });
}
</script>
