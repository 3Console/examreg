<template>
  <div class="notification-page">
    <section class="section_body_head">
      <div class="body-contain">
        <div class="heading-title">
          <h1 class="title">Notification</h1>
        </div>
        <div class="message-box">
          <div class="message-content">
            <i class="fa fa-info-circle"></i>
            <span>Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</span>
          </div>
          <div class="message-content">
            <i class="fa fa-info-circle"></i>
            <span>Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</span>
          </div>
          <div class="message-content">
            <i class="fa fa-info-circle"></i>
            <span>Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script>
  export default {
    
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables.scss";
  .body-contain {
    margin-top: 20px;
    .heading-title {
      // border-bottom: 1px solid #d8d8d8;
      // margin-bottom: 20px;

      .title {
        color: #000000;
      }
    }
  }

  .message-box {
    background: #e4e6e8;
    padding: 24px;

    .message-content {
      color: #f00;
      margin-bottom: 10px;
    }
  }
</style>
