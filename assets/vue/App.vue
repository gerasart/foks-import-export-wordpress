<template>
  <div class="foks_ie">
    <Settings></Settings>
  </div>
</template>

<script>
  import List from "./components/List";
  import Settings from "./components/Settings";

  export default {
    name: "app",
    components: {Settings, List},
  }
</script>

<style lang="scss">
  @import "~ant-design-vue/dist/antd.css";
  @import "../styles/main";
</style>
