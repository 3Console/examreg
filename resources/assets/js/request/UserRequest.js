import BaseModelRequest from './base/BaseModelRequest';

export default class UserRequest extends BaseModelRequest {
  login(username, password) {
    let params = {
      grant_type: 'password',
      client_id: process.env.MIX_CLIENT_ID,
      client_secret: process.env.MIX_CLIENT_SECRET,
      username: username,
      password: password,
      scope: '*',
    }
    return this.post('/oauth/token', params);
  }

  getProfile() {
    const url = '/user/profile';
    return this.get(url);
  }

  getUserSchedules(params) {
    const url = '/user/schedules';
    return this.get(url, params);
  }

  getScheduleDetail(id) {
    const url = `/user/schedules/${id}/detail`;
    return this.get(url);
  }
}
