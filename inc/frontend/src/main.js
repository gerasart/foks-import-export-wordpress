import {createApp} from 'vue'
import {Quasar, Notify, Dialog} from 'quasar'
import router from "@/router/index"
import i18n from '@/plugins/i18n.js';

import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'

import App from './App.vue'

const main = createApp(App);
main.use(router);
main.use(i18n);

main.use(Quasar, {
    plugins: {Notify, Dialog},
});

main.mount('#app');
