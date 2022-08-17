<template>
  <q-layout view="hhh lpR fff">
    <q-header  elevated  bordered class="bg-grey-1 text-black" height-hint="98">
      <q-toolbar>
        <q-toolbar-title>
          {{$t('title')}}
        </q-toolbar-title>
          <lang-switcher />
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
       <custom-menu />
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
            <span>v3.0.0</span> <a style="vertical-align: sub;" href="https://t.me/gerasart">
              <img style="width: 24px"  src="../../assets/images/developer.png" alt=""></a>
          </div>
        </q-toolbar-title>
      </q-toolbar>
    </q-footer>

  </q-layout>
</template>

<script setup lang="ts">
import LangSwitcher from "../modules/LangSwitcher.vue";
import CustomMenu from "../modules/CustomMenu.vue";
</script>
