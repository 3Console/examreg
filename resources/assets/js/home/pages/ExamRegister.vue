<template>
  <div class="exam-register-page" id="register-page">
    <section class="container pad-12">
      <div class="heading-title">
        <h1 class="title">Exam Register</h1>
      </div>
      <div class="body-contain">
        <div class="semester-heading">
          <label class="semester-title">Semester: </label>
          <select name="semester_id"
                  class="form-control semester-form"
                  v-model="semester_id">
            <option v-for="item in semesters" :value="item.id">{{ item.name }}</option>
          </select>
        </div>
        <template v-if="semester.is_register === 1">
          <div class="duration-heading">
            <i class="fa fa-clock-o"></i>
            <span>Duration: </span>
            <span>{{ semester.start_time }} - {{ semester.end_time }}</span>
          </div>
          <div class="class-heading">
            <label class="class-title">Classes: </label>
            <select name="class_option"
                    class="form-control class-form"
                    v-model="class_option">
              <option value="public">All</option>
              <option value="private">My Class</option>
            </select>
            <input type="text"
                   name="searchKey"
                   placeholder="Search"
                   class="form-control search-form"
                   v-model="searchKey">
          </div>
          <div class="class-list">
            <data-table :getData="getAllSemesterClass"
                        :limit="limit"
                        :column="column"
                        :widthTable="'100%'"
                        ref="datatable"
                        msgEmptyData=""
                        @DataTable:finish="onDatatableFinish" >
              <template slot="body" slot-scope="props">
                <div class="col-md-4">
                  <div class="item-content">
                    <div class="box-classes">
                      <img src="/images/developer.png">
                      <div class="box-title" @click="onClickClassDetail(rows[ props.index ])">
                        <div class="subject-heading">{{ rows[ props.index ].subject }} [{{ rows[ props.index ].class_code }}]</div>
                        <div class="lecturer-heading">Lecturer: {{ rows[ props.index ].lecturer }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </data-table>
          </div>
        </template>
        <template v-else>
          <div class="no-data-semester">
            <img src="/images/check-out.svg">
            <span>This semester is closed or not chosen</span>
          </div>
        </template>
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
        semesters: [],
        semester_id: '',
        semester: {},
        class_option: 'public',
        limit: 9,
        column: 1,
        params: {},
        rows: [],
        searchKey: '',
      }
    },
    watch: {
      'semester_id' (newValue) {
        this.getSemesterDetail(newValue);
        this.refresh();
      },
      'searchKey' (newValue) {
        setTimeout(() => {
          this.search()
        }, 500);
      },
      'class_option' (newValue) {
        this.refresh();
      }
    },
    methods: {
      onDatatableFinish() {
        this.rows = this.$refs.datatable.rows;
      },
      search() {
        this.$refs.datatable.$emit('DataTable:filter', Object.assign(this.params, {search_key: this.searchKey}));
      },
      onClickClassDetail(unitClass) {
        this.$router.push({ name: 'Class Detail', params: { id: unitClass.class_id } });
      },
      getSemesters() {
        rf.getRequest('ExamRegisterRequest').getSemesters().then(res => {
          this.semesters = res.data;
        });
      },
      getSemesterDetail(semesterId) {
        rf.getRequest('ExamRegisterRequest').getSemesterDetail({id: semesterId}).then(res => {
          this.semester = res.data;
        });
      },
      getAllSemesterClass(params) {
        const meta =  {
          id: this.semester_id
        }
        if(this.class_option === 'private') {
          return rf.getRequest('ExamRegisterRequest').getAllUserClass(Object.assign({}, params, meta));
        }
        return rf.getRequest('ExamRegisterRequest').getAllSemesterClass(Object.assign({}, params, meta));
      },
      refresh() {
        this.$refs.datatable.refresh();
      },
    },
    mounted() {
      this.getSemesters();
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .exam-register-page {
    min-height: 768px;
    .pad-12 {
      padding: 12px;

      .heading-title {
        border-bottom: 1px solid #d8d8d8;
        margin-bottom: 20px;

        .title {
          color: #000000;
        }
      }

      .body-contain {
        .semester-heading {
          display: flex;
          margin-bottom: 20px;

          .semester-title {
            margin-right: 20px;
            position: relative;
            top: 7px;
            font-weight: bold;
          }

          .semester-form {
            width: 240px;
          }
        }

        .duration-heading {
          float: right;
          position: relative;
          top: -50px;
        }

        .class-heading {
          display: flex;
          margin-bottom: 20px;

          .class-title {
            margin-right: 35px;
            position: relative;
            top: 7px;
            font-weight: bold;
          }

          .class-form {
            width: 240px;
          }

          .search-form {
            width: 240px;
            margin-left: 20px;
          }
        }

        .class-list {
          .item-content {
            border: 1px solid #d8d8d8;
            margin-bottom: 20px;
            
            .box-classes {
              img {
                height: 112px;
              }

              .box-title {
                padding: 12px;
                cursor: pointer;
                color: #0064bd;
                &:hover {
                  color: #0095ff;
                }

                .lecturer-heading {
                  font-size: 12px;
                  color: #8e8686;
                }
              }
            }
          }
        }

        .no-data-semester {
          padding: 20px;
          text-align: center;
          img {
            width: 120px;
          }
          span {
            vertical-align: sub;
            margin-left: 20px;
          }
        }
      }
    }
  }
</style>
