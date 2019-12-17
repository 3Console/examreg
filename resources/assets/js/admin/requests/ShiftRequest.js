import BaseRequest from '../lib/BaseRequest'

export default class ShiftRequest extends BaseRequest {
  getShifts(params) {
    let url = '/admin/api/shifts';
    return this.get(url, params);
  }
  getAllShift() {
    let url = '/admin/api/shifts/all';
    return this.get(url);
  }
  createShift(params) {
    let url = '/admin/api/shifts/create';
    return this.post(url, params);
  }
  updateShift(params) {
      let url = '/admin/api/shifts/update';
      return this.post(url, params);
  }
  removeShift(params) {
      let url = '/admin/api/shifts/remove';
      return this.post(url, params);
  }
}
