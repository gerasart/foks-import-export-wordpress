<!--
  - Copyright (c) 2022.
  - Created by metasync.site.
  - Developer: gerasymenkoph@gmail.com
  - Link: https://t.me/gerasart
  -->

<template>
  <div class="q-pa-md q-gutter-sm">
    <div class="mainTitle">{{ $t('title_commands') }}</div>

    <div class="row">
      <div class="col-md-6">
        <div class="field-group mb30" v-for="(command, index) in commands" :key="index">
          <div class="title">{{ command.title }}</div>
          <q-separator/>
          <q-input color="black" filled :model-value="command.text" :dense="false" readonly>
            <template v-slot:append>
              <q-avatar @click="copyText(command.text)">
                <clipboard-icon/>
              </q-avatar>
            </template>
          </q-input>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {copyToClipboard} from 'quasar'
import ClipboardIcon from "../icons/ClipboardIcon.vue";
import {useQuasar} from 'quasar'
import {ref} from "vue";
import {useI18n} from 'vue-i18n';

const {t} = useI18n();
const $q = useQuasar();


const commands = ref([
  {
    title: t('import_products_cli'),
    text: 'php wp-content/plugins/foksImportExport/bin/console.php import-products'
  },
  {
    title: t('import_attributes_cli'),
    text: 'php wp-content/plugins/foksImportExport/bin/console.php import-attributes'
  },
  {
    title: t('export_products_cli'),
    text: 'php wp-content/plugins/foksImportExport/bin/console.php export-products'
  },
  {
    title: t('clear_products_cli'),
    text: 'php wp-content/plugins/foksImportExport/bin/console.php clear-products'
  },
]);


function copyText(text) {
  copyToClipboard(text)
      .then(() => {
        $q.notify({
          message: t('copied'),
          color: 'purple',
          position: 'center'
        })
      });
}

</script>

<style scoped>

</style>