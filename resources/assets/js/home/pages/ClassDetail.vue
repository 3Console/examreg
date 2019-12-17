<template>
  <div class="class-detail-page">
    <section class="container pad-12">
      <template v-if="check === 0">
        <div class="warning-dialog">
          <i class="fa fa-exclamation-triangle"></i>
          <span class="warning-message">You are not in this class, so you can't register any schedules here</span>
        </div>
      </template>
      <template v-else>
        <div class="heading-contain">
          <div class="heading-title">Basic Information</div>
        </div>
      </template>
    </section>
  </div>
</template>
<script>
  import RemoveErrorsMixin from 'common/RemoveErrorsMixin';
  import rf from 'requestfactory';

  export default {
    mixins: [RemoveErrorsMixin],
    data() {
      return {
        check: {},
      }
    },
    methods: {

    },
    mounted() {
      this.id = this.$route.params.id;
      rf.getRequest('ExamRegisterRequest').checkUserClass(this.id).then(res => {
        this.check = res.data;
      });
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .class-detail-page {
    min-height: 768px;

    .pad-12 {
      padding: 12px;

      .warning-dialog {
        margin-top: 20px;
        background: #e4e6e8;
        padding: 24px;
        color: #f00;

        .warning-message {
          margin-left: 20px;
        }
      }
    }
  }
</style>
