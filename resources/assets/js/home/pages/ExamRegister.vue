<template>
  <div class="exam-register-page">
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
              <option value="0">All</option>
              <option value="1">My Class</option>
            </select>
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
        class_option: 0,
      }
    },
    watch: {
      'semester_id' (newValue) {
        this.getSemesterDetail(newValue);
      }
    },
    methods: {
      getSemesters() {
        rf.getRequest('ExamRegisterRequest').getSemesters().then(res => {
          this.semesters = res.data;
        });
      },
      getSemesterDetail(semesterId) {
        rf.getRequest('ExamRegisterRequest').getSemesterDetail({id: semesterId}).then(res => {
          this.semester = res.data;
        });
      }
    },
    mounted() {
      this.getSemesters();
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .exam-register-page {
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
