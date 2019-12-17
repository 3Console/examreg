<template>
  <div id="counter-time">
    <template v-if="isCustomTemplate">
      <slot name="body" :hours="hours | twoDigits" :minutes="minutes | twoDigits" :seconds="seconds | twoDigits" />
    </template>
    <template v-else>
      <div class="block" v-if="days">
        <p class="digit">{{ days | twoDigits }}</p>
        <p class="text">Days</p>
      </div>
      <div class="block" v-if="hours">
        <p class="digit">{{ hours | twoDigits }}</p>
        <p class="text">Hours</p>
      </div>
      <div class="block">
        <p class="digit">{{ minutes | twoDigits }}</p>
        <p class="text">Minutes</p>
      </div>
      <div class="block">
        <p class="digit">{{ seconds | twoDigits }}</p>
        <p class="text">Seconds</p>
      </div>
    </template>
  </div>
</template>
<script>
  export default {
    props : {
      end : {type: Number},
      isCustomTemplate: {type: Boolean, default: false}
    },
    data() {
      return {
        now: Math.trunc((new Date()).getTime() / 1000),
        timeInterval: null,
        isEmitted: false,
      }
    },
    filters: {
      twoDigits(value) {
        if(value.toString().length <= 1) {
          return `0${value.toString()}`;
        }
        return value.toString();
      }
    },
    computed: {
      seconds() {
        return this.getTime() % 60;
      },
      minutes() {
        return Math.trunc(this.getTime() / 60) % 60;
      },
      hours() {
        return Math.trunc(this.getTime() / 60 / 60) % 24;
      },
      days() {
        return Math.trunc(this.getTime() / 60 / 60 / 24);
      },

      isFinish() {
        return this.seconds <= 0 && this.minutes <= 0 && this.hours <= 0;
      }
    },
    watch: {
      end(newValue) {
        this.startCountTime();
      },
    },
    methods: {
      getTime() {
        const result = this.end - this.now;
        if (result < 0) {
          return 0;
        }
        return result;
      },

      startCountTime() {
        this.timeInterval = window.setInterval(() => {
          if (this.isFinish && !this.isEmitted) {
            clearInterval(this.timeInterval);
            this.isEmitted = true;
            this.$emit('CounterTime:finish');
            return;
          }
          this.now = Math.trunc((new Date()).getTime() / 1000);
        }, 1000);
      }
    },
    created() {
      this.isEmitted = false;
      this.startCountTime();
    },
    destroyed() {
      clearInterval(this.timeInterval);
    }
  }
</script>
<style lang="scss" scoped>

  #counter-time {
    display: inline-block;
    .block {
      display: inline-block;
      flex-direction: column;
      margin: 20px;
    }
    .text {
      color: #1abc9c;
      font-size: 25px;
      font-family: 'Roboto Condensed', serif;
      font-weight: 40;
      margin-top:10px;
      margin-bottom: 10px;
      text-align: center;
    }
    .digit {
      font-weight: 100;
      margin: 10px;
      text-align: center;
    }
  }
</style>
