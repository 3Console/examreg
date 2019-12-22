<template>
  <div class="my-schedule-page">
    <section class="section_body_head">
      <div class="heading-title">
        <h1 class="title">{{ $t('my_schedule_page.header') }}</h1>
      </div>
      <div class="body-contain">
        <div class="group-item">
          <data-table :getData="getData"
                      :limit="limit"
                      :column="column"
                      :widthTable="'100%'"
                      ref="datatable"
                      msgEmptyData=""
                      @DataTable:finish="onDatatableFinish" >
            <template slot="body" slot-scope="props">
              <div class="item-content">
                <div class="title-subject" @click="onClickScheduleDetail(rows[ props.index ])">
                  {{ rows[ props.index ].subject }}
                </div>
                <div class="title-time">
                  <label>{{ $t('my_schedule_page.date') }}: </label>
                  <span>{{ rows[ props.index ].date }}</span>
                </div>
                <div class="title-shift">
                  <label>{{ $t('my_schedule_page.shift') }}: </label>
                  <span>{{ rows[ props.index ].start_time }} - {{ rows[ props.index ].end_time }}</span>
                </div>
                <div class="title-address">
                  <label>{{ $t('my_schedule_page.address') }}: </label>
                  <span>{{ rows[ props.index ].room }} - {{ rows[ props.index ].address }}</span>
                </div>
              </div>
            </template>
          </data-table>
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
        params: {},
        rows: [],
      }
    },
    methods: {
      getData(params) {
        return rf.getRequest('UserRequest').getUserSchedules(params);
      },
      onDatatableFinish() {
        this.rows = this.$refs.datatable.rows;
      },
      onClickScheduleDetail(schedule) {
        this.$router.push({ name: 'Schedule Detail', params: { id: schedule.id } });
      },
    },
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .my-schedule-page {
    min-height: 768px;

    .heading-title {
      border-bottom: 1px solid #d8d8d8;

      .title {
        color: #242729;
      }
    }

    .body-contain {
      margin-top: 20px;

      .group-item {
        padding-left: 12px;

        .item-content {
          border-bottom: 1px solid #eff0f1;

          .title-subject {
            font-size: 16px;
            color: #0064bd;
            margin-bottom: 10px;
            cursor: pointer;
            &:hover {
              color: #0095ff;
            }
          }

          .title-time {
            color: #535a60;
            font-size: 12px;
          }

          .title-shift {
            color: #535a60;
            font-size: 12px;
          }

          .title-address {
            color: #535a60;
            font-size: 12px;
          }
        }
      }
    }
  }
</style>
