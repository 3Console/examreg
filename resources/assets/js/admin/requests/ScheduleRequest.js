import BaseRequest from '../lib/BaseRequest'

export default class ScheduleRequest extends BaseRequest {
  getSchedules(params) {
    let url = '/admin/api/schedules';
    return this.get(url, params);
  }

  createSchedule(params) {
    let url = '/admin/api/schedules/create';
    return this.post(url, params);
  }

  updateSchedule(params) {
      let url = '/admin/api/schedules/update';
      return this.post(url, params);
  }
  
  removeSchedule(params) {
      let url = '/admin/api/schedules/remove';
      return this.post(url, params);
  }

  getSchedule(id) {
    const url = `/admin/api/schedules/${id}/detail`;
    return this.get(url);
  }

  getStudents(id) {
    const url = `/admin/api/schedules/${id}/students`;
    return this.get(url);
  }
}
