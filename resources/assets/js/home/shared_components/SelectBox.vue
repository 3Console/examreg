<template>
  <div>
    <select name="name"
            class="form-control"
            v-validate=" isValidate ? 'required' : ''"
            data-vv-as="name"
            v-model="selected">
      <option value="" disabled="">{{ $t('common.select._default') }}</option>
      <option v-for="option in options" :value="option.id">
        {{ option.name }}
      </option>
    </select>
  </div>
</template>
<script>
  export default {
    props: {
      options: {
        type        : Array
      },
      value: {
        default     : ''
      },
      isValidate: {
        type        : Boolean,
        default     : false
      },
      name: {
        type        : String,
        default     : ''
      }
    },
    data() {
      return {
        internalSelected: this.selected
      }
    },
    watch() {
      internalSelected(newValue) {
        this.$emit('')
      }
    }
  }
</script>
<style lang="scss" scoped>
</style>