import { createI18n } from 'vue-i18n';
import { DEFAULT_LANG } from '../env';
import CookieFacade from '@/facade/cookie.facade'
import en from '@/i18n/en';
import ua from '@/i18n/ua';

const i18n = createI18n({
  legacy: false,
  locale: CookieFacade.get('lang') || DEFAULT_LANG,
  fallbackLocale: DEFAULT_LANG,
  messages: {
    en,
    ua,
  },
});

export default i18n;
