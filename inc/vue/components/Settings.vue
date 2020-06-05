<template>
  <div class="foks_settings">
    <a-input v-model="Foks" placeholder="" />
    <a-button type="primary" @click="save">Save</a-button>
  </div>
</template>

<script>
  export default {
    name: "Settings",
    computed: {
      Foks: {
        get() {
          return this.$store.state.foks;
        },
        set(value) {
          this.$store.commit('setter', {foks: value})
        }
      }
    },
    methods: {
      save() {
        const request = {
          action: 'saveSettings',
          data: this.unisenderToken
        };

        this.$store.dispatch('sendRequest', request).then(res => {
          this.openNotification('Saved');
        });
      }
    },
  }
</script>

<style lang="scss">
  .settings_unisenser {
    input {
      width: 25%;
    }
  }
</style>
