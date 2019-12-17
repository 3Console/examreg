<template>
  <div>
    404 Not Found
  </div>
</template>

<script>
  export default {
    data() {
      return {
        titlePage: '404 Not Found'
      }
    },
    mounted() {
      this.$emit('EVENT_PAGE_CHANGE', this);
    }
  }
</script>