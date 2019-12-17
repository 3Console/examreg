<template>
  <router-view @EVENT_PAGE_CHANGE="onEventPageChange"/>
</template>
<script>
  export default {
    methods: {
      onEventPageChange(data) {
        this.$emit('EVENT_PAGE_CHANGE', data);
      }
    }
  }
</script>
<style lang="scss" scoped>
</style>
