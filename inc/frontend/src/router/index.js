import { createRouter, createWebHashHistory } from 'vue-router';
import ImportTab from '../components/tabs/ImportTab.vue';
import ExportTab from '../components/tabs/ExportTab.vue';
import LogTab from '../components/tabs/LogTab.vue';
import SettingsTab from '../components/tabs/SettingsTab.vue';
import DeleteProductsTab from '../components/tabs/DeleteProductsTab.vue';
import VariationTab from '../components/tabs/VariationTab.vue';

const routes = [
  {
    path: '/',
    name: 'Settings',
    component: SettingsTab,
  },
  {
    path: '/variation',
    name: 'Variation',
    component: VariationTab,
  },
  {
    path: '/import',
    name: 'Import',
    component: ImportTab,
  },
  {
    path: '/export',
    name: 'Export',
    component: ExportTab,
  },
  {
    path: '/logs',
    name: 'Logs',
    component: LogTab,
  },
  {
    path: '/delete',
    name: 'Delete',
    component: DeleteProductsTab,
  },
]
const router = createRouter({
  history: createWebHashHistory(),
  routes,
})

export default router
