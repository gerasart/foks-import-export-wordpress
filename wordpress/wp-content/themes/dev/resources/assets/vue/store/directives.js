import Vue from 'vue';
import {gsap} from "gsap";

import * as ScrollMagic from 'scrollmagic-with-ssr';
// import "scrollmagic/scrollmagic/uncompressed/plugins/animation.gsap";
// import scrollMonitor from 'scrollmonitor';
// import { ScrollMagicPluginGsap } from "scrollmagic-plugin-gsap";
// ScrollMagicPluginGsap(ScrollMagic, gsap);

// TweenMax.defaultOverwrite = false;
gsap.defaults({overwrite: false});


/* global scrollMonitor */
Vue.directive('scroll-to', {
  /* el, binding, vnode */
  bind: function (el, binding) {
    el.addEventListener('click', (e) => {
      e.preventDefault();

      gsap.to(window, 2, {
        scrollTo: {
          y: binding.value,
          offsetY: 32,
          autoKill: false
        },
        // ease: Power2.easeOut
        ease: "power2.out",
      });
    });
  },
});


Vue.directive('viewport', {
  /* el, binding, vnode */
  inserted: function (el, binding, vnode) {
  // update: function (el, binding, vnode) {
    let elementWatcher = scrollMonitor.create(el);
    vnode.data.watcher = elementWatcher;

    let callback = () => {
      // console.log('in view', el);
      if (typeof binding.value === 'function') {
        binding.value.bind(vnode.context)(el, vnode);
        if (binding.modifiers.once) {
          elementWatcher.destroy();
        }
      }
    };

    let arg = binding.arg ? binding.arg : 'enter';

    switch (arg) {
      case 'partExit':
        elementWatcher.partiallyExitViewport(callback);
        break;
      case 'inOut':
        elementWatcher.visibilityChange(callback);
        break;
      case 'fully':
        elementWatcher.fullyEnterViewport(callback);
        break;
      case 'exit':
        elementWatcher.exitViewport(callback);
        break;
      case 'enter':
      default:
        elementWatcher.enterViewport(callback);
        break;
    }
  },
  unbind(el, binding, vnode) {
    let watcher = vnode.data.watcher;
    if ( watcher ) {
      watcher.destroy();
    }
  }
});

Vue.directive('image', {
  /* el, binding, vnode */
  inserted: function (el, binding, vnode) {
    let elementWatcher = scrollMonitor.create(el);
    vnode.data.watcher = elementWatcher;

    let img = el.querySelector('img');

    // let ScrollMagic = require('scrollmagic-with-ssr');

    let controller = new ScrollMagic.Controller();

    gsap.set(el, {perspective: 1000, overflow: 'hidden'});
    // el.style.cssText = "perspective: 1000px; overflow: 'hidden';";

    let tween = gsap.fromTo(img, 1,
      {yPercent: -25, rotationX: "5deg", scale: 1.2, ease: "none"},
      {yPercent: 25, rotationX: "0deg", scale: 1, ease: "none",
        // overwrite: false,
        onInterrupt: false,
        immediateRender: false
      });


    let callback = () => {
      if (window) {
        img.style.transition = 'all .3s linear';
        new ScrollMagic.Scene({triggerElement: el, duration: "300%", offset: -window.innerHeight})
          .setTween(tween)
          .addTo(controller);
        // elementWatcher.destroy();
      }
    };

    let arg = binding.arg ? binding.arg : 'enter';

    switch (arg) {
      case 'full':
      case 'fully':
        elementWatcher.fullyEnterViewport(callback);
        break;
      case 'enter':
      default:
        elementWatcher.enterViewport(callback);
        break;
    }
  },
  unbind(el, binding, vnode) {
    let watcher = vnode.data.watcher;
    if ( watcher ) {
      watcher.destroy();
    }
  }
});

