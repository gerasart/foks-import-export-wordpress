<template>
  <q-select
    v-model="lang"
    :options="Object.values(langList)"
    @update:model-value="switchLang"
    label="Language"
    dense
    borderless
    emit-value
    map-options
    options-dense
    style="min-width: 150px"
  />
</template>

<script setup lang="ts">
import {Langs} from '@/enums/langs'
import {DEFAULT_LANG} from '@/env'
import CookieFacade from '@/facade/cookie.facade'
import i18n from '@/plugins/i18n.js';
import {computed, ref} from "vue";

const langList = computed(() => Langs);
const lang = ref(CookieFacade.get('lang') || DEFAULT_LANG);

function switchLang(e) {
  CookieFacade.set('lang', e);
  i18n.global.locale.value = e;
  location.reload();
}
</script>

<style scoped>

</style>
