import Vue from 'vue';

Vue.mixin({
  data() {
    return {
      translates: window.translates,
    };
  },
  methods: {
    Translates(slug) {
      let result = this.translates.filter(result => result.slug === slug);

      return (result.length) ? result[0].title : this.ucFirst(slug.replace('_', ' '));
    },
    ajaxUrl() {
      return window.ajaxurl;
    },
    convertStringToDate(string) {
      return new Date(this.stringToIso(string))
    },
    stringToIso(string) {
      let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;

      return string.replace(pattern, '$3-$2-$1')
    },
    currentPath() {
      return location.pathname;
    },
    parseGetParams(get_param) {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(get_param);
    }
  }
});
