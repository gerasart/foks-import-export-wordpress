import Cookies from 'js-cookie';

class CookieFacade {
  constructor(instance) {
    this.instance = instance;
  }
  get(key) {
    return this.instance.get(key);
  }
  set(key, value) {
    this.instance.set(key, value);
    return value;
  }
}

export default new CookieFacade(Cookies);
