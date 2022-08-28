<!--
  - Copyright (c) 2022.
  - Created by metasync.site.
  - Developer: gerasymenkoph@gmail.com
  - Link: https://t.me/gerasart
  -->

<template>
  <q-layout view="hhh lpR fff">
    <q-header elevated bordered class="bg-grey-1 text-black" height-hint="98">
      <q-toolbar>
        <q-toolbar-title>
          <a
              class="logo"
              href="https://foks.biz/"
              target="_blank"
          >
            <img src="../../assets/images/logo.png" alt="">
          </a> {{ $t('title') }}
        </q-toolbar-title>
        <lang-switcher/>
      </q-toolbar>
    </q-header>
    <q-drawer
        show-if-above
        :width="200"
        :breakpoint="500"
        bordered
        class="bg-grey-3"
    >
      <q-scroll-area class="fit">
        <custom-menu/>
      </q-scroll-area>
    </q-drawer>

    <q-page-container class="ml-10">
      <q-page class="q-pa-md">
        <router-view/>
      </q-page>
    </q-page-container>

    <q-footer class="bg-grey-1 text-black text-center">
      <q-toolbar>
        <q-toolbar-title>
          <div>
            <span>v{{ Settings.version }}</span> <a class="devLogo" href="https://t.me/gerasart">
            <img src="../../assets/images/developer.png" alt=""></a>
          </div>
        </q-toolbar-title>
      </q-toolbar>
    </q-footer>

  </q-layout>
</template>

<script setup>
import LangSwitcher from "../modules/LangSwitcher.vue";
import CustomMenu from "../modules/CustomMenu.vue";
import {ref} from "vue";

const Settings = ref(window?.settings);
</script>

<style scoped lang="scss">
.logo, .devLogo {
  vertical-align: sub;
}

.devLogo {
  img {
    width: 24px
  }
}
</style>
