<template>
  <input type="text"
         v-number-only
         v-model="model"
         @keypress="eventKeyPress"/>
</template>

<script>
  export default {
    data() {
      return {
        model: "",
        originValue: ''
      }
    },
    props: ['value'],
    watch: {
      value() {
        this.init();
      },
      model(newValue) {
        this.$emit('input', newValue);
        this.emitDirtyIfNeed();
      }
    },
    methods: {
      eventKeyPress(event) {
        let stringValue = "" + this.value;
        let charCode = (event.which) ? event.which : event.keyCode;
        if ((stringValue.length >= 18) || (charCode === 46 && (~ stringValue.indexOf('.') < 0)) ||
          ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46)) {
          event.preventDefault();
        } else {
          return true;
        }
      },

      init() {
        if (!this.value) {
          this.model = "";
          return;
        }
        this.model = this.value;
      },

      emitDirtyIfNeed() {
        const a = parseFloat(this.originValue);
        const b = parseFloat(this.model);
        if (a !== b) {
          this.$emit('dirty', true);
        }
      }
    },
    directives: {
      "number-only": {
        bind(el, binding) {
          el.value = insertNumber(el.value);
          binding.value = el.value;
        },

        inserted(el, bind) {
          el.value = insertNumber(el.value);
          bind.value = el.value;
        },

        update(el, bind) {
          el.value = insertNumber(el.value);
          bind.value = el.value;
        },
      }
    },

    mounted() {
      this.init();
      this.originValue = window._.cloneDeep(this.model);
    }
  }

  function insertNumber(newValue) {
    newValue = "" + newValue;
    newValue = newValue.match(/(\d)+(\.)?(\d)?/gi) ? newValue.match(/(\d)+(\.)?(\d)?/gi).join('') : "";
    return newValue;
  }
</script>

<style scoped>

</style>