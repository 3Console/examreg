<template>
  <div class="my-schedule-detail">
    <section class="section_body_head">
      <div class="heading-title">
        <h1 class="title">{{ $t('schedule_detail.header') }}</h1>
      </div>
      <div class="body-contain">
        <button type="button" class="btn btn-print" @click="print">
          <i class="fa fa-print"></i> {{ $t('schedule_detail.print') }}
        </button>
        <div class="print-content-box">
          <div class="document-container" id="document" style="margin-top: 10px; background-color: #fff; padding: 12px;">
            <div class="document-header" style="text-align: center;">
              <div class="row">
                <div class="col-md-5">
                  <div class="header-left" style="font-size: 14px; text-transform: uppercase;">
                    {{ $t('schedule_detail.header_left_1') }}
                  </div>
                  <div class="header-left-mini" style="font-size: 14px; text-transform: uppercase; font-weight: bold;">
                    {{ $t('schedule_detail.header_left_2') }}
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="header-right" style="font-size: 14px; text-transform: uppercase; font-weight: bold;">
                    {{ $t('schedule_detail.header_right_1') }}
                  </div>
                  <div class="header-right-mini" style="font-size: 12px; font-weight: bold;">
                    {{ $t('schedule_detail.header_right_2') }}
                  </div>
                </div>
              </div>
            </div>
            <div class="document-body" style="margin-top: 20px;">
              <div class="document-title"
                   style="font-size: 20px; text-align: center; text-transform: uppercase; font-weight: bold; margin-bottom: 20px;">
                {{ $t('schedule_detail.title') }}
              </div>
              <div class="list-item">
                <div class="item-container" style="display: flex;">
                  <label class="item-title" style="margin-right: 10px; width: 20%;">{{ $t('schedule_detail.subject') }}: </label>
                  <div class="item-data" style="width: 80%; font-weight: bold;">{{ schedule.subject }}</div>
                </div>
                <div class="item-container" style="display: flex;">
                  <label class="item-title" style="margin-right: 10px; width: 20%;">{{ $t('schedule_detail.name') }}: </label>
                  <div class="item-data" style="width: 80%; font-weight: bold;">{{ schedule.full_name }}</div>
                </div>
                <div class="item-container" style="display: flex;">
                  <label class="item-title" style="margin-right: 10px; width: 20%;">{{ $t('schedule_detail.msv') }}: </label>
                  <div class="item-data" style="width: 80%;">{{ schedule.msv }}</div>
                </div>
                <div class="item-container" style="display: flex;">
                  <label class="item-title" style="margin-right: 10px; width: 20%;">{{ $t('schedule_detail.dob') }}: </label>
                  <div class="item-data" style="width: 80%;">{{ schedule.dob }}</div>
                </div>
                <div class="item-container" style="display: flex;">
                  <label class="item-title" style="margin-right: 10px; width: 20%;">{{ $t('schedule_detail.course') }}: </label>
                  <div class="item-data" style="width: 80%;">{{ schedule.course }}</div>
                </div>
              </div>
            </div>
            <div class="document-footer" style="margin-top: 20px; margin-bottom: 60px;">
              <div class="row">
                <div class="col-md-6">
                  <div class="heading-confirmation"
                       style="text-align: center; text-transform: uppercase; font-size: 14px; font-weight: bold; margin-top: 22px;">
                    {{ $t('schedule_detail.footer_left') }}
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="time-confirmation" style="text-align: center; font-size: 14px;">
                    {{ $t('schedule_detail.footer_right_1') }}
                  </div>
                  <div class="heading-confirmation" style="text-align: center; text-transform: uppercase; font-size: 14px; font-weight: bold;">
                    {{ $t('schedule_detail.footer_right_2') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
        limit: 10,
        column: 1,
        schedule: {},
        rows: [],
      }
    },
    methods: {
      print() {
        this.$htmlToPaper('document', () => {
          this.showSuccess('Printing completed');
        });
      }
    },
    mounted() {
      this.id = this.$route.params.id;
      rf.getRequest('UserRequest').getScheduleDetail(this.id).then(res => {
        this.schedule = res.data;
      });
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .my-schedule-detail {
    min-height: 768px;
    color: #000000;

    .heading-title {
      border-bottom: 1px solid #d8d8d8;

      .title {
        color: #242729;
      }
    }

    .body-contain {
      margin-top: 20px;

      .btn-print {
        border: 1px solid #053064;
        line-height: 20px;
        padding: 3px 12px;
        font-size: 14px;
        font-weight: bold;
        border-radius: 4px;
        text-align: center;
        color: #053064;
        transition: 0.5s;
        min-width: 120px;
        height: 40px;
        cursor: pointer;
        text-transform: uppercase;
        &:hover {
          background-color: #053064;
          border-color: #053064;
          color: $color_white;
          transition: 0.5s;
        }
        .fa-print {
          float: left;
          font-size: 20px;
          margin-right: 5px;
          line-height: 20px;
        }
      }

      .print-content-box {
        margin-top: 20px;
      }
    }
  }
</style>
