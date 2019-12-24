<template>
  <div class="profile-page">
    <section class="section_body_head">
      <div class="heading-title">
        <h1 class="title">{{ $t('profile_page.profile_header') }}</h1>
      </div>
      <div class="body-contain">
        <div class="row">
          <div class="col-md-3">
            <img src="/images/icon2.svg" class="avatar">
          </div>
          <div class="col-md-9">
            <div class="basic-information">
              <div class="form-group">
                <div class="info-item">
                  <label>{{ $t('profile_page.user_name') }}:</label>
                  <input
                    name="username"
                    type="text"
                    class="form-control"
                    data-vv-as="username"
                    data-vv-validate-on="none"
                    v-validate="'required'"
                    v-model="params.username"
                    @focus="resetError"
                    :class="{ error: errors.has('username') }"
                    disabled>
                  <div class="invalid-feedback" v-if="errors.has('username')">{{ errors.first('username') }}</div>
                </div>
                <div class="info-item">
                  <label>{{ $t('profile_page.full_name') }}:</label>
                  <input
                    name="full_name"
                    type="text"
                    class="form-control"
                    data-vv-as="full_name"
                    data-vv-validate-on="none"
                    v-validate="'required'"
                    v-model="params.full_name"
                    @focus="resetError"
                    :class="{ error: errors.has('full_name') }"
                    disabled>
                  <div class="invalid-feedback" v-if="errors.has('full_name')">{{ errors.first('full_name') }}</div>
                </div>
                <div class="info-item">
                  <label>{{ $t('profile_page.msv') }}:</label>
                  <input
                    name="msv"
                    type="text"
                    class="form-control"
                    data-vv-as="msv"
                    data-vv-validate-on="none"
                    v-validate="'required'"
                    v-model="params.msv"
                    @focus="resetError"
                    :class="{ error: errors.has('msv') }"
                    disabled>
                  <div class="invalid-feedback" v-if="errors.has('msv')">{{ errors.first('msv') }}</div>
                </div>
                <div class="info-item">
                  <label>{{ $t('profile_page.dob') }}:</label>
                  <input
                    name="dob"
                    type="text"
                    class="form-control"
                    data-vv-as="dob"
                    data-vv-validate-on="none"
                    v-validate="'required'"
                    v-model="params.dob"
                    @focus="resetError"
                    :class="{ error: errors.has('dob') }"
                    disabled>
                  <div class="invalid-feedback" v-if="errors.has('dob')">{{ errors.first('dob') }}</div>
                </div>
                <div class="info-item">
                  <label>{{ $t('profile_page.course') }}:</label>
                  <input
                    name="course"
                    type="text"
                    class="form-control"
                    data-vv-as="course"
                    data-vv-validate-on="none"
                    v-validate="'required'"
                    v-model="params.course"
                    @focus="resetError"
                    :class="{ error: errors.has('course') }"
                    disabled>
                  <div class="invalid-feedback" v-if="errors.has('course')">{{ errors.first('course') }}</div>
                </div>
                <div class="info-item">
                  <label>{{ $t('profile_page.unit') }}:</label>
                  <input
                    name="unit"
                    type="text"
                    class="form-control"
                    data-vv-as="unit"
                    data-vv-validate-on="none"
                    v-validate="'required'"
                    v-model="params.unit"
                    @focus="resetError"
                    :class="{ error: errors.has('unit') }"
                    disabled>
                  <div class="invalid-feedback" v-if="errors.has('unit')">{{ errors.first('unit') }}</div>
                </div>
                <!-- <div class="info-item">
                  <label>Bio:</label>
                  <textarea
                    name="bio"
                    class="form-control"
                    v-validate="'required'"
                    data-vv-as="bio"
                    data-vv-validate-on="none"
                    maxlength="190"
                    @focus="resetError"
                    :class="{ error: errors.has('bio') }" />
                  <div class="invalid-feedback" v-if="errors.has('bio')">{{ errors.first('bio') }}</div>
                </div> -->
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
        params: {},
      }
    },
    methods: {
      getProfile() {
        rf.getRequest('UserRequest').getProfile().then((res) => {
          this.params = res.data;
        });
      }
    },
    created() {
      this.getProfile();
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .profile-page {
    min-height: 768px;

    .heading-title {
      border-bottom: 1px solid #d8d8d8;

      .title {
        color: #242729;
      }
    }

    .body-contain {
      margin-top: 20px;

      .avatar {
        // border-radius: 50%;
      }

      .basic-information {
        padding-left: 24px;
      }
    }
  }
</style>
