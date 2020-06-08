<template>
  <div class="foks_settings">
    <a-col class="block_col import_block" :span="8">
      <div class="title">Import</div>
      <div class="import_block-link"></div>

      <div class="field-group">
        <a-input v-model="Foks.import" class="import-link" placeholder="Import url"></a-input>
        <a-button type="primary" class="import_now" @click="importFoks">Import now</a-button>

        <a-progress class="field_progress" v-if="progress" :percent="percent" />

      </div>

      <div class="field-group">
        <div class="sub_title">Import auto update</div>
        <a-radio-group name="radioGroup" v-model="Foks.update">
          <a-radio value="1">
            1h
          </a-radio>
          <a-radio value="4">
            4h
          </a-radio>
          <a-radio value="24">
            24h
          </a-radio>
        </a-radio-group>
      </div>

      <div class="field-group">
        <a-button type="primary" @click="save">Save</a-button>
      </div>

    </a-col>

    <a-col class="block_col export_block" :span="8">
      <div class="title">Export</div>
      <div class="field-group">
        <div class="export_block-link"><a target="_blank" :href="Foks.export">{{Foks.export}}</a></div>
      </div>
    </a-col>

  </div>
</template>

<script>
    export default {
        name: "Settings",
        data() {
            return {
                percent: 0,
                progress: false
            }
        },
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
            importFoks() {
                const request = {
                    action: 'importFoks',
                };
                this.$store.dispatch('sendRequest', request).then(res => {
                    console.log(res);
                });
            },
            save() {
                const request = {
                    action: 'saveSettings',
                    data: this.Foks
                };
                this.$message.config({
                    top: '50px',
                    duration: 2
                });
                this.$store.dispatch('sendRequest', request).then(res => {
                    this.$message.success({content: 'Saved!'});
                });
            }
        },
    }
</script>

<style lang="scss" scoped>
  .foks_settings {
    .block_col {
      padding: 30px;

      .title {
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 20px;
      }

      .sub_title {
        font-weight: bold;
        margin-bottom: 10px;
      }

      .field-group {
        margin-bottom: 30px;
      }
    }

    .import_now {
      margin-top: 20px;
    }

    .field_progress {
      margin-bottom: 20px;
    }

    .export_block {
      &-link {
        display: inline-block;
        padding: 10px;
        background: #eee;
      }
    }
  }
</style>
