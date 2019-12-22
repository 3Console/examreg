<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <img src="/images/default-logo.png">
            <div class="text-center wrap-text-login">
              <span class="text-login">{{ $t('common.login') }}</span>
            </div>
            <form>
              <div class="form-group">
                <input
                  v-model="username"
                  @focus="resetError"
                  :placeholder="$t('common.email_placeholder')"
                  :class="{ error: errors.has('username') }"
                  name="username"
                  data-vv-as="username"
                  data-vv-validate-on="none"
                  v-validate="'required'"
                  type="text"
                  class="form-control">
                <div class="invalid-feedback" v-if="errors.has('username')">{{ errors.first('username') }}</div>
              </div>
              <div class="form-group">
                <input
                  v-model="password"
                  @focus="resetError"
                  :placeholder="$t('common.password')"
                  :class="{ error: errors.has('password') }"
                  name="password"
                  data-vv-validate-on="none"
                  v-validate="'required'"
                  type="password"
                  class="form-control">
                <div class="invalid-feedback" v-if="errors.has('password')">{{ errors.first('password')}}</div>
              </div>
              <div class="invalid-feedback" v-if="errors.has('error')">{{ errors.first('error') }}</div>
              <div class="form-group">
                <button :disabled="isSubmitting" type="button" @click="onClickSubmit" class="btn btn-login">
                  {{ getSubmitName($t('common.login')) }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import rf from 'requestfactory';
  import AuthenticationUtils from 'common/AuthenticationUtils';
  import RemoveErrorsMixin from 'common/RemoveErrorsMixin';

  export default {
    mixins: [RemoveErrorsMixin],
    data() {
      return {
        username: '',
        password: '',
      }
    },
    methods: {
      async onClickSubmit () {
        await this.$validator.validateAll();
        if (this.errors.any()) {
          return;
        }

        if (this.isSubmitting) {
          return;
        }

        this.resetError();
        this.startSubmit();
        rf.getRequest('UserRequest').login(this.username, this.password)
          .then(response => {
            this.endSubmit();
            AuthenticationUtils.saveAuthenticationData(response);
            const destination = this.$route.query.destination || '/';
            window.location.href = destination; 
          })
          .catch(error => {
            this.endSubmit();
            if (!error.response) {
              this.showError(this.$t("common.message.network_error"));
              return;
            }
            this.convertRemoteErrors(error);
          });
      },
    },
    mounted() {
      window.addEventListener('keyup', (event) => {
        if (event.keyCode === 13) { 
          this.onClickSubmit();
        }
      });
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../../../sass/_variables.scss";

  .card {
    border: none;
    background: none;

    img {
      width: 100%;
    }

    .card-body {
      .btn-login {
        text-align: center;
        border-radius: 22.5px;
        height: 35px;
        width: 100%;
        font-weight: normal;
        font-style: normal;
        font-stretch: normal;
        line-height: normal;
        letter-spacing: normal;
        font-size: 15px;
        background-color: #009974;
        border-color: #009974;
        color: #fff;
      }
    }

    .text-login {
      color: #009974;
      font-size: 30px;
      text-align: center;
      width: 119px;
      height: 30px;
    }

    .wrap-text-login {
      margin: 30px auto;
    }

    .invalid-feedback {
      color: red;
      display: block;
    }
  }
</style>