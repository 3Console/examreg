<template>
  <div id="notfound" class="clearfix">
    <div class="content">
      <h1>Page Not Found</h1>
      <p>We can’t find the page you’re looking for.
You can either return to the previous page, visit our homepage or contact our support team.</p>
    </div>
  </div>
</template>

<script>
  export default {}
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  #notfound {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%,-50%);
    .content {
      max-width: 600px;
      text-align: center;
      color: #000000;
    }
  }
</style>
