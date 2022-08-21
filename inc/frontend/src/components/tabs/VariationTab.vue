<template>
  <div class="q-pa-md q-gutter-sm">
    <div class="mainTitle">{{ $t('title_variation') }}</div>

    <div v-if="!isProgress" class="field-group">
      <q-btn color="purple" @click="importData" :label="$t('import_attributes')"/>
    </div>

    <div v-if="variationOptions.length && !isProgress" class="field-group">
      <q-select
          filled
          use-chips
          v-model="variations"
          :options="variationOptions"
          :label="$t('select_variation')"
          multiple
          emit-value
          map-options
          style="width: 350px"
      >
        <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
          <q-item v-bind="itemProps">
            <q-item-section>
              <q-item-label v-html="opt.label"/>
            </q-item-section>
            <q-item-section side>
              <q-toggle :model-value="selected" @update:model-value="toggleOption(opt)"/>
            </q-item-section>
          </q-item>
        </template>
      </q-select>
      <hr>
      <q-btn color="secondary" @click="saveVariations" :label="$t('save')"/>
    </div>

    <q-inner-loading v-if="isProgress" :showing="true">
      <q-spinner-gears size="50px" color="primary"/>
    </q-inner-loading>
  </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import * as qs from "qs";
import {QSpinnerBall, useQuasar} from 'quasar'
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const $q = useQuasar();
const Settings = ref(window?.settings);
const isProgress = ref(false);
const variations = ref([]);
const variationOptions = ref([])

function saveVariations() {
  isProgress.value = true;

  axios.post(window.ajaxurl, qs.stringify({
    action: 'saveVariations',
    data: variations.value,
  })).then(() => {
    isProgress.value = false;
  });
}

async function getAttributes() {
 return axios.post(Settings.value?.ajaxUrl, qs.stringify({
    action: 'getAttributes',
  })).then((res) => {
   variationOptions.value = parseAttributes(res.data.data);
  });
}

function importData() {
  isProgress.value = true;
  const notify = $q.notify({
    group: false,
    timeout: 0,
    spinner: QSpinnerBall,
    message: t('title_import')+'...',
  })

  axios.post(Settings.value?.ajaxUrl, qs.stringify({
    action: 'importAttributes',
  })).then((res) => {
    variationOptions.value = parseAttributes(res.data.data);
    notify({
      icon: 'done',
      spinner: QSpinnerBall,
      message: t('import_ready'),
      timeout: 1000
    });

    isProgress.value = false;
  });
}

function parseAttributes(data) {
  return data.map((item) => {
    return {
      label: item.name,
      value: parseInt(item.id),
    }
  });
}

onMounted(async () => {
  isProgress.value = true;
  await getAttributes();

  if (Settings.value.variations) {
    variations.value = Settings.value.variations.map((item) => parseInt(item));
  }

  isProgress.value = false;
});
</script>
