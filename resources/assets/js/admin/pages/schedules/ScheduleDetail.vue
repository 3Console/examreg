<template>
  <div class="boxCore" id="schedule-detail">
    <section class="clearfix">
      <div class="filter_container clearfix">
        <button type="button" class="btn btn-print" @click="print">
          <i class="fa fa-print"></i> Print
        </button>
        <div class="document-container" id="document" style="margin-top: 10px; background-color: #fff; padding: 12px;">
          <div class="document-header" style="text-align: center;">
            <div class="row">
              <div class="col-md-5">
                <div class="header-left" style="font-size: 14px; text-transform: uppercase;">
                  Vietnam National University
                </div>
                <div class="header-left-mini" style="font-size: 14px; text-transform: uppercase; font-weight: bold;">
                  University of Engineering and Technology
                </div>
              </div>
              <div class="col-md-7">
                <div class="header-right" style="font-size: 14px; text-transform: uppercase; font-weight: bold;">
                  Socialist Republic of Vietnam
                </div>
                <div class="header-right-mini" style="font-size: 12px; font-weight: bold;">
                  Independence - Freedom - Happiness
                </div>
              </div>
            </div>
          </div>
          <div class="document-body" style="margin-top: 20px;">
            <div class="document-title" style="font-size: 20px; text-align: center; text-transform: uppercase; font-weight: bold;">
              List Student
            </div>
            <div class="list-item">
              <div class="item-container" style="display: flex;">
                <label class="item-title" style="margin-right: 10px; font-weight: bold;">Subject: </label>
                <div class="item-data">{{ schedule.subject }}</div>
              </div>
              <div class="item-container" style="display: flex;">
                <label class="item-title" style="margin-right: 10px; font-weight: bold;">Lecturer: </label>
                <div class="item-data">{{ schedule.lecturer }}</div>
              </div>
              <div class="item-container" style="display: flex;">
                <label class="item-title" style="margin-right: 10px; font-weight: bold;">Shift: </label>
                <div class="item-data">{{ schedule.start_time }} - {{ schedule.end_time }}</div>
              </div>
              <div class="item-container" style="display: flex;">
                <label class="item-title" style="margin-right: 10px; font-weight: bold;">Location: </label>
                <div class="item-data">{{ schedule.room }} - {{ schedule.address }}</div>
              </div>
              <div class="item-container" style="display: flex;">
                <label class="item-title" style="margin-right: 10px; font-weight: bold;">Date: </label>
                <div class="item-data">{{ schedule.date }}</div>
              </div>
            </div>
            <div class="datatable">
              <table style="width: 100%; border: 1px solid #d8d8d8;">
                <thead>
                  <tr>
                    <th class="col1 text-center" style="border: 1px solid #d8d8d8; width: 50px;">No</th>
                    <th class="col2 text-center" style="border: 1px solid #d8d8d8; width: 150px;">MSV</th>
                    <th class="col3 text-center" style="border: 1px solid #d8d8d8; width: 250px;">Name</th>
                    <th class="col4 text-center" style="border: 1px solid #d8d8d8; width: 150px;">Date of birth</th>
                    <th class="col5 text-center" style="border: 1px solid #d8d8d8; width: 250px;">Course</th>
                    <th class="col6 text-center" style="border: 1px solid #d8d8d8; width: 150px;">Signature</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(student, index) in students">
                    <td class="col1 text-center" style="width: 50px; border: 1px solid #d8d8d8; padding: 0px 5px;">
                      {{ index + 1 }}
                    </td>
                    <td class="col2 text-left" style="width: 150px; border: 1px solid #d8d8d8; padding: 0px 5px;">
                      {{ student.msv }}
                    </td>
                    <td class="col3 text-left" style="width: 250px; border: 1px solid #d8d8d8; padding: 0px 5px;">
                      {{ student.full_name }}
                    </td>
                    <td class="col4 text-left" style="width: 150px; border: 1px solid #d8d8d8; padding: 0px 5px;">
                      {{ student.dob }}
                    </td>
                    <td class="col5 text-left" style="width: 250px; border: 1px solid #d8d8d8; padding: 0px 5px;">
                      {{ student.course }}
                    </td>
                    <td class="col6 text-left" style="width: 150px; border: 1px solid #d8d8d8; padding: 0px 5px;"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="document-footer" style="margin-top: 20px; margin-bottom: 60px;">
            <div class="row">
              <div class="col-md-6"></div>
              <div class="col-md-6">
                <div class="time-confirmation" style="text-align: center; font-size: 14px;">
                  Ha Noi, .../.../2019
                </div>
                <div class="heading-confirmation" style="text-align: center; text-transform: uppercase; font-size: 14px; font-weight: bold;">
                  Confirmation of Training Room
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
  import rf from '../../lib/RequestFactory';
  import RemoveErrorsMixin from '../../common/RemoveErrorsMixin';

  export default {
    mixins: [RemoveErrorsMixin],
    data() {
      return {
        id: undefined,
        titlePage: 'Semester',
        schedule: {},
        students: [],
        searchKey: '',
        limit: 10,
        column: 5,
        params: {},
        rows: [],
        classes: [],
        isLoading: false,
      }
    },
    methods: {
      onDatatableFinish() {
        this.rows = this.$refs.datatable.rows;
        window._.each(this.rows, item => {
          this.$set(item, 'editable', false);
        });
      },

      search() {
        this.$refs.datatable.$emit('DataTable:filter', Object.assign(this.params, {search_key: this.searchKey}));
      },

      getData(params) {
        const meta = {
          semesterId: this.$route.params.id
        };
        return rf.getRequest('SemesterRequest').getSemesterClass(Object.assign({}, params, meta));
      },

      getClasses() {
        rf.getRequest('ClassRequest').getAllClass().then((res) => {
          this.classes = res.data;
        });
      },

      print() {
        this.$htmlToPaper('document', () => {
          this.showSuccess('Printing completed');
        });
      }
    },
    mounted() {
      // this.getClasses();
      this.id = this.$route.params.id;
      rf.getRequest('ScheduleRequest').getSchedule(this.id).then(res => {
        this.schedule = res.data;
      });
      rf.getRequest('ScheduleRequest').getStudents(this.id).then(res => {
        this.students = res.data;
      });
      this.$emit('EVENT_PAGE_CHANGE', this);
    },
  }
</script>
<style lang="scss" scoped>
  @import "../../../../sass/common";
  .btn-print {
    border: 1px solid $color_eden;
    line-height: 20px;
    padding: 3px 12px;
    font-size: $font_root;
    font-weight: bold;
    border-radius: 4px;
    text-align: center;
    color: $color_eden;
    transition: 0.5s;
    min-width: 86px;
    cursor: pointer;
    text-transform: uppercase;
    &:focus,
    &:active,
    &:hover {
      background-color: $color_eden;
      border-color: $color_eden;
      color: $color_white;
      transition: 0.5s;
    }
    .fa-print {
      float: left;
      margin-right: 5px;
      line-height: 20px;
    }
  }

  #schedule-detail {
    .filter_container {
      margin: 12px 0px;
      width: 100%;

      .document-container {
        margin-top: 10px;
        background-color: #fff;
        padding: 12px;

        .document-header {
          text-align: center;

          .header-left {
            font-size: 14px;
            text-transform: uppercase;
          }

          .header-left-mini {
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
          }

          .header-right {
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
          }

          .header-right-mini {
            font-size: 12px;
            font-weight: bold;
          }
        }

        .document-body {
          margin-top: 20px;

          .document-title {
            font-size: 20px;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
          }

          .list-item {
            .item-container {
              display: flex;

              .item-title {
                margin-right: 10px;
              }
            }
          }

          .datatable {
            table {
              width: 100%;
              border: 1px solid #d8d8d8;
            }

            thead {
              th {
                border: 1px solid #d8d8d8;
              }

              .col1 {
                width: 50px;
              }
              .col2 {
                width: 150px;
              }
              .col3 {
                width: 250px;
              }
              .col4 {
                width: 150px;
              }
              .col5 {
                width: 250px;
              }
              .col6 {
                width: 150px;
              }
            }

            tbody {
              td {
                border: 1px solid #d8d8d8;
                padding: 0px 5px;
              }
            }
          }
        }

        .document-footer {
          margin-top: 20px;
          margin-bottom: 60px;

          .time-confirmation {
            text-align: center;
            font-size: 14px;
          }

          .heading-confirmation {
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: bold;
          }
        }
      }
    }
  }
</style>
