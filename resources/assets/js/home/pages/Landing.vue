<template>
  <div class="home-page">
    <section class="section_body_head">
      <div class="body-contain">
        <div class="heading-title">
          <h1 class="title">{{ $t('landing_page.welcome_text') }}</h1>
        </div>
        <div class="presentation">
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer convallis ornare sollicitudin. Ut eget lacinia urna, mattis venenatis arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>
        </div>
      </div>
      <div class="container">
        <notification />
        <about-us-page />
      </div>
    </section>
  </div>
</template>
<script>
  import Notification from './Notification';
  import AboutUsPage from './AboutUsPage';
  export default {
    components: {
      Notification,
      AboutUsPage,
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables.scss";
  .body-contain {
    padding: 120px 160px;
    background-image: url("/images/developer.png");
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;

    .heading-title {
      text-align: center;
      color: #fff;
      text-transform: uppercase;
      margin-bottom: 40px;

      .title {
        font-weight: bold;
        letter-spacing: 7px;
      }
    }

    .presentation {
      color: #fff;
      text-align: center;
    }
  }
</style>
