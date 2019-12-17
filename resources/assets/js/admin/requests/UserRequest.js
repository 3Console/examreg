import BaseRequest from '../lib/BaseRequest'

export default class UserRequest extends BaseRequest {

  getUsers(params) {
    let url = '/admin/api/users';
    return this.get(url, params);
  }

  getReferrers(params) {
    let url = '/admin/api/referrers';
    return this.get(url, params);
  }

  updateUserInfo(data) {
    let url = `/admin/api/users/${data.id}`;
    return this.put(url, data);
  }

  getCurrentAdmin(useCache = true, params) {
    if (this.user && useCache) {
      return new Promise((resolve, reject) => {
        resolve(this.user);
      });
    }
    return new Promise((resolve, reject) => {
      const url = '/admin/api/user';
      const self = this;
      this.get(url, params)
          .then(function (user) {
            self.user = user;
            resolve(user);
          })
          .catch(function (error) {
            reject(error);
          });
    });
  }

  getUsersWithSetting(parmas) {
    let url = '/api/users';
    return this.get(url, parmas);
  }

  updateSettingOtp(id, state) {
    let url = `/admin/api/users/otp/${id}`;
    return this.put(url, {otp_verified: state});
  }

  disableAccount(id) {
    let url = `/admin/api/users/disable/${id}`;
    return this.put(url, {});
  }

  enableAccount(id) {
    let url = `/admin/api/users/enable/${id}`;
    return this.put(url, {});
  }

  getDeviceRegister(params) {
    let url = `/admin/api/user/${params.userId}/devices`;
    return this.get(url, params);
  }

  deleteDevice(params) {
    let url = `/admin/api/user/${params.userId}/device/${params.deviceId}`;
    return this.del(url, params);
  }

}
