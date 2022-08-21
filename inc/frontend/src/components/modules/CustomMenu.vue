<template>
  <q-list>
    <template v-for="(menuItem, index) in menuList" :key="index">
      <router-link class="menuItem" :to="{name: menuItem.route}">
        <q-item clickable :active="menuItem.route === routeName" v-ripple>
          <q-item-section avatar>
            <q-icon :name="menuItem.icon"/>
          </q-item-section>
          <q-item-section>
            {{ menuItem.label }}
          </q-item-section>
        </q-item>
      </router-link>

      <q-separator :key="'sep' + index" v-if="menuItem.separator"/>
    </template>

  </q-list>
</template>

<script setup lang="ts">
import {useRoute} from "vue-router";
import {computed, ref} from "vue";
import { useI18n } from "vue-i18n";

const route = useRoute();
const routeName = computed(() => route.name);
const { t } = useI18n();
const menuList = ref([
  {
    icon: 'settings',
    label: t('title_settings'),
    route: 'Settings',
    separator: true
  },
  {
    icon: 'style',
    label: t('title_variation'),
    route: 'Variation',
    separator: true
  },
  {
    icon: 'download',
    label: t('title_import'),
    route: 'Import',
    separator: true
  },
  {
    icon: 'upload',
    label: t('title_export'),
    route: 'Export',
    separator: true
  },
  {
    icon: 'history',
    label: t('title_logs'),
    route: 'Logs',
    separator: true
  },
  {
    icon: 'delete',
    label: t('title_delete'),
    route: 'Delete',
    separator: true
  },
]);
</script>

<style>
.menuItem {
  text-decoration: none;
  color:black;
}
</style>
