import Vue from 'vue';

import axios from 'axios';
import * as qs from 'qs';


Vue.mixin({
  data() {
    return {
      translations: window.translations,
      ajaxUrl: window.ajaxUrl,
    };
  },
  methods: {
    Translate(str) {
      let slug = String(str).toLowerCase().replace(/\s/g, '_');
      let result = this.translations.filter(result => result.slug === slug);

      if (!result.length) {
        let data = {
          action: 'addNewTraslation',
          slug: slug.replace('\'', ''),
        };

        if (typeof axios !== 'undefined') {
          axios.post(this.ajaxUrl, qs.stringify(data)).then(item => {
            console.log('response item',item);
          });
        }
      }

      return (result.length) ? result[0].title : this.ucFirst(slug.replace(/_/g, ' '));
    },
    ucFirst(text) {
      return text.charAt(0).toUpperCase() + text.slice(1);
    },
  },
  beforeCreate() {
    this.$options.filters.translate = this.$options.filters.translate.bind(this)
  },
  filters: {
    translate: function (value) {
      if (!value) {
        return '';
      }

      return this.Translate(value);
    }
  }
});
