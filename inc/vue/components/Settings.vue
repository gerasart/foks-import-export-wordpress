<template>
  <div class="foks_settings">
    <a-col class="block_col import_block" :span="8">
      <div class="title">{{text.title_import}}</div>
      <div class="import_block-link"></div>

      <div class="field-group">
        <a-input v-model="Foks.import" class="import-link" :placeholder="text.url"></a-input>
        <a-spin class="progress" size="large" v-if="progress" />
        <a-button v-if="!progress" type="primary" class="import_now" @click="importFoks">{{text.import}}</a-button>
      </div>

      <div class="field-group">
        <div class="sub_title">{{text.update}}</div>
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
        <a-button type="primary" @click="save">{{text.save}}</a-button>
      </div>

    </a-col>

    <a-col class="block_col export_block" :span="8">
      <div class="title">{{text.title_export}}</div>
      <div class="field-group">
        <div class="export_block-link"><a target="_blank" :href="Foks.export">{{Foks.export}}</a></div>
      </div>
    </a-col>

  </div>
</template>

<script>
    // https://my.foks.biz/s/pb/f?key=547d2e64-c4b9-417e-bd28-3760c25409cd&type=drop_foks&ext=xml
    export default {
        name: "Settings",
        data() {
            return {
                progress: false,
                text: {
                    title_import: 'Import',
                    title_export: 'Export',
                    success: 'Import success',
                    save: 'Save settings',
                    import: 'Import now',
                    saved: 'Saved!',
                    update: 'Import auto update',
                    url: 'Import url'
                },
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
                this.$message.config({
                    top: '50px',
                    duration: 2
                });

                this.progress = true;
                this.$store.dispatch('sendRequest', request).then(res => {
                    this.progress = false;
                    if (res.data.success) {
                        this.$message.success({content: this.text.success});
                    }

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
                    this.$message.success({content: this.text.saved});
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

    .progress {
      margin-top: 30px;
      margin-left: 40px;
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
