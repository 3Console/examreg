<template>
  <div class="class-detail-page" id="class-schedule">
    <section class="container pad-12">
      <template v-if="check === 0">
        <div class="warning-dialog">
          <i class="fa fa-exclamation-triangle"></i>
          <span class="warning-message">{{ $t('class-schedule.notification_1') }}</span>
        </div>
      </template>
      <template v-else>
        <div class="heading-contain">
          <div class="heading-title">
            <h1 class="title">{{ $t('class-schedule.header') }}</h1>
          </div>
          <div class="heading-status">
            <label>{{ $t('class-schedule.register_status') }}: </label>
            <i>{{ register_status | statusLabel }}</i>
          </div>
          <div class="heading-status">
            <label>{{ $t('class-schedule.register_condition') }}: </label>
            <span :class="{ 'active': user_status.is_valid === 1 }" class="status-data">
              {{ user_status.is_valid === 1 ? $t('class-schedule.qualified') : $t('class-schedule.disqualified') }}
            </span>
          </div>
        </div>
        <template v-if="user_status.is_valid === 0">
          <div class="warning-dialog">
            <i class="fa fa-exclamation-triangle"></i>
            <span class="warning-message">{{ $t('class-schedule.notification_2') }}</span>
          </div>
        </template>
        <template v-else>
          <div class="data-table">
            <data-table :getData="getData"
                        :limit="limit"
                        :column="column"
                        :widthTable="'100%'"
                        ref="datatable"
                        :msgEmptyData="$t('common.data_empty')"
                        @DataTable:finish="onDatatableFinish" >
              <th class="col1 text-left" data-sort-field="date">{{ $t('class-schedule.date') }}</th>
              <th class="col2 text-left" data-sort-field="start_time">{{ $t('class-schedule.start_time') }}</th>
              <th class="col3 text-left" data-sort-field="end_time">{{ $t('class-schedule.end_time') }}</th>
              <th class="col4 text-left" data-sort-field="room">{{ $t('class-schedule.location') }}</th>
              <th class="col5 text-left" data-sort-field="slot">{{ $t('class-schedule.slot') }}</th>
              <th class="col6"></th>

              <template slot="body" slot-scope="props">
                <tr>
                  <td class="col1 text-left">
                    {{ rows[ props.index ].date }}
                  </td>
                  <td class="col2 text-left">
                    {{ rows[ props.index ].start_time }}
                  </td>
                  <td class="col3 text-left">
                    {{ rows[ props.index ].end_time }}
                  </td>
                  <td class="col4 text-left">
                    {{ rows[ props.index ].room }} - {{ rows[ props.index ].address }}
                  </td>
                  <td class="col5 text-left">
                    {{ rows[ props.index ].number }} / {{ rows[ props.index ].slot }}
                  </td>
                  <template v-if="register_status === 0">
                    <td class="col6 text-center">
                      <button class="btn btn-default"
                              v-if="rows[ props.index ].submitable === true"
                              @click="onClickSubmit(props.index)">
                        {{ $t('class-schedule.btn_submit') }}
                      </button>
                    </td>
                  </template>
                  <template v-else>
                    <td class="col6 text-center">
                      <button class="btn btn-cancel"
                              v-if="rows[ props.index ].cancelable === true"
                              @click="onClickCancel(props.index)">
                        {{ $t('class-schedule.btn_cancel') }}
                      </button>
                    </td>
                  </template>
                </tr>
              </template>
            </data-table>
          </div>
        </template>
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
        user_status: {},
        register_status: 0,
        limit: 10,
        column: 6,
        rows: [],
        isCancelAble: {},
      }
    },
    filters: {
      statusLabel: function (val) {
        if(val == 1)
          return window.i18n.t('class-schedule.registered');
        else return window.i18n.t('class-schedule.not_yet');
      },
    },
    methods: {
      onDatatableFinish() {
        this.rows = this.$refs.datatable.rows;
        window._.each(this.rows, item => {
          this.$set(item, 'submitable', true);
          this.$set(item, 'cancelable', false);
          this.$set(item, 'number', 0);
        });
        for(let i = 0; i < this.rows.length; i++) {
          rf.getRequest('ExamRegisterRequest').checkSchedule(this.rows[i].id).then(res => {
            this.isCancelAble = res.data;
            if (this.isCancelAble === 1) {
              this.rows[i].cancelable = true;
            }
          });
        }
        for(let i = 0; i < this.rows.length; i++) {
          rf.getRequest('ExamRegisterRequest').countSlot(this.rows[i].id).then(res => {
            this.rows[i].number = res.data;
            if (this.rows[i].slot - this.rows[i].number === 0) {
              this.rows[i].submitable = false;
            }
          });
        }
      },
      getData(params) {
        const meta =  {
          id: this.$route.params.id
        }
        return rf.getRequest('ExamRegisterRequest').getClassSchedule(Object.assign({}, params, meta));
      },
      onClickSubmit(index) {
        window.ConfirmationModal.show({
          type        : 'confirm',
          title       : '',
          content     : window.i18n.t('class-schedule.confirm_1'),
          onConfirm   :  () => {
            this.submitSchedule(this.rows[index]);
          },
          onCancel    : () => {
            this.$refs.datatable.refresh();
          }
        });
      },
      onClickCancel(index) {
        window.ConfirmationModal.show({
          type        : 'confirm',
          title       : '',
          content     : window.i18n.t('class-schedule.confirm_2'),
          onConfirm   :  () => {
            this.cancelSchedule(this.rows[index]);
          },
          onCancel    : () => {}
        });
      },
      submitSchedule(params) {
        this.startSubmit();
        rf.getRequest('ExamRegisterRequest').submitSchedule(params).then(res => {
          this.endSubmit();
          this.showSuccess(window.i18n.t('class-schedule.message_submit_successful'));
          rf.getRequest('ExamRegisterRequest').checkUserSchedule(this.$route.params.id).then(res => {
            this.register_status = res.data;
          });
          this.$refs.datatable.refresh();
        })
        .catch(error => {
          this.endSubmit();
          if (!error.response) {
            this.showError(window.i18n.t("common.message.network_error"));
            return;
          }
          this.convertRemoteErrors(error);
          if (this.errors.has('error')) {
            this.showError(error.response.data.message);
          }
        });
      },
      cancelSchedule(params) {
        this.startSubmit();
        rf.getRequest('ExamRegisterRequest').cancelSchedule(params).then(res => {
          this.endSubmit();
          this.showSuccess(window.i18n.t('class-schedule.message_cancel_successful'));
          rf.getRequest('ExamRegisterRequest').checkUserSchedule(this.$route.params.id).then(res => {
            this.register_status = res.data;
          });
          this.$refs.datatable.refresh();
        })
        .catch(error => {
          this.endSubmit();
          if (!error.response) {
            this.showError(window.i18n.t("common.message.network_error"));
            return;
          }
          this.convertRemoteErrors(error);
          if (this.errors.has('error')) {
            this.showError(error.response.data.message);
          }
        });
      }
    },
    mounted() {
      this.id = this.$route.params.id;
      rf.getRequest('ExamRegisterRequest').checkUserClass(this.id).then(res => {
        this.check = res.data;
      });
      rf.getRequest('ExamRegisterRequest').checkUserSchedule(this.id).then(res => {
        this.register_status = res.data;
      });
      rf.getRequest('ExamRegisterRequest').getUserStatus(this.id).then(res => {
        this.user_status = res.data;
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

      .heading-title {
        border-bottom: 1px solid #d8d8d8;
        margin-bottom: 20px;

        .title {
          color: #000000;
        }
      }
    }
  }

  .status-data {
    color: #f00;
  }

  .active {
    color: #55d184;
  }

  .btn-default {
    background: #0a3e69;
    border: 1px solid #0a3e69;
    border-radius: 4px;
    color: #fff;
    opacity: 0.9;
    position: relative;
    top: -3px;
    &:hover {
      opacity: 1;
    }
  }

  .btn-cancel {
    background: #f00;
    border: 1px solid #f00;
    border-radius: 4px;
    color: #fff;
    opacity: 0.9;
    position: relative;
    top: -3px;
    &:hover {
      opacity: 1;
    }
  }
</style>
<style lang="scss">
  @import "../../../sass/_variables";
  #class-schedule {
    .tableContainer {
      border: 1px solid #d8d8d8;
      border-bottom: 0px;

      th, td {
        border-bottom: 1px solid #d8d8d8;
      }
      
      tbody {
        tr {
          height: 50px;
          &:hover {
            background: #e4e6e8;
          }
        }
      }
    }
  }
</style>
