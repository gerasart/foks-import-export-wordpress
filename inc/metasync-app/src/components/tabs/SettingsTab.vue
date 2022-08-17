<template>
  <div class="settingsFoks">
    <div class="q-pa-md q-gutter-sm">
      <div class="row">
        <div class="col-md-4">
          <q-input v-model="Settings.import" :label="$t('import_label')" width="50"/>
        </div>
        <div class="col-md-4" v-if="Settings?.import">
          <a :href="Settings.import" target="_blank">
            <q-btn round color="brown-5" icon="directions"/>
          </a>
        </div>
      </div>
    </div>

    <div class="q-pa-md q-gutter-sm">
      <div class="title">{{ $t('settings_cron') }}</div>

      <div class="q-gutter-sm">
        <q-radio dense v-model="Settings.update" val="1" label="1h"/>
        <q-radio dense v-model="Settings.update" val="4" label="4h"/>
        <q-radio dense v-model="Settings.update" val="24" label="24h"/>
      </div>
    </div>

    <div class="q-pa-md q-gutter-sm">
      <div class="title">{{ $t('settings_img') }}</div>
      <q-checkbox dense v-model="Settings.img" :label="$t('on_off')"/>
    </div>

    <div class="q-pa-md q-gutter-sm">
      <q-btn color="secondary" :label="$t('save')" @click="saveSettings"/>
    </div>
  </div>
</template>

<script setup>
import {ref} from "vue";
import axios from 'axios';
import * as qs from 'qs';
import { useQuasar, QSpinnerGears } from 'quasar'

const $q = useQuasar();
const Settings = ref((window?.settings || {
  update: '4',
  img: false,
  import: ''
}));

function saveSettings() {
  const notify = $q.notify({
    group: false, // required to be updatable
    timeout: 0, // we want to be in control when it gets dismissed
    spinner: QSpinnerGears,
    message: 'Saving...',
  })

  axios.post(window.ajaxurl, qs.stringify({
    action: 'saveSettings',
    data: Settings._rawValue
  })).then(() => {
    notify({
      icon: 'done',
      spinner: QSpinnerGears,
      message: 'Saved!',
      timeout: 1000
    });
  });
}
</script>

<style lang="scss">
input[type="text"] {
  box-shadow: none !important;
  border-radius: inherit;
  border: none !important;
  background-color: transparent !important;
  color: inherit !important;
}

.settingsFoks {
  .title {
    font-weight: bold;
  }
}
</style>
