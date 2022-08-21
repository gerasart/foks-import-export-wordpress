<template>
  <div class="q-pa-md q-gutter-sm">
    <div class="mainTitle">{{ $t('title_delete') }}</div>
    <div class="field-group">
      <q-btn v-if="!isProgress" color="purple" @click="removeProducts" :label="$t('clear_products')"/>
      <q-inner-loading v-else :showing="true">
        <q-spinner-gears size="50px" color="primary"/>
      </q-inner-loading>
    </div>
  </div>
</template>

<script setup>
import {ref} from "vue";
import axios from "axios";
import * as qs from "qs";
import {useQuasar} from 'quasar';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();
const $q = useQuasar();
const isProgress = ref(false);
const Settings = ref(window?.settings);

function removeProducts() {
  $q.dialog({
    title: t('confirm'),
    message: t('do_you_want_remove_products'),
    ok: t('text_yes'),
    cancel: t('text_cancel'),
    persistent: true
  }).onOk(() => {
    isProgress.value = true;

    axios.post(window.ajaxurl, qs.stringify({
      action: 'removeProducts',
    })).then(() => {
      isProgress.value = false;
    }).catch(() => {
      isProgress.value = false;
    });
  });
}
</script>
