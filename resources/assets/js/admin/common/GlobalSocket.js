import Vue from 'vue';

export default class GlobalSocket {

  constructor(publicChannelsOnly) {
    //public channels

    Vue.mixin({
      mounted: function () {
        if (this.getSocketEventHandlers) {
          window._.forIn(this.getSocketEventHandlers(), (handler, eventName) => {
            this.$on(eventName, handler);
          });
        }
      },
      beforeDestroy() {
        if (this.getSocketEventHandlers) {
          window._.forIn(this.getSocketEventHandlers(), (handler, eventName) => {
            this.$off(eventName, handler);
          });
        }
      }
    });
  }

}
