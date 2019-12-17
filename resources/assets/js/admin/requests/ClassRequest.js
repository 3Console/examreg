import BaseRequest from '../lib/BaseRequest'

export default class ClassRequest extends BaseRequest {
  getClasses(params) {
    let url = '/admin/api/classes';
    return this.get(url, params);
  }

  getAllClass() {
    let url = '/admin/api/classes/all';
    return this.get(url);
  }

  createClass(params) {
    let url = '/admin/api/classes/create';
    return this.post(url, params);
  }
  updateClass(params) {
      let url = '/admin/api/classes/update';
      return this.post(url, params);
  }
  removeClass(params) {
      let url = '/admin/api/classes/remove';
      return this.post(url, params);
  }

  getUnitClass(id) {
    const url = `/admin/api/classes/${id}/detail`;
    return this.get(url);
  }

  getUserClass(params) {
    let url = '/admin/api/classes/user';
    return this.get(url, params);
  }
  createStudent(params) {
    let url = '/admin/api/classes/create-student';
    return this.post(url, params);
  }
  updateStudent(params) {
      let url = '/admin/api/classes/update-student';
      return this.post(url, params);
  }
  removeStudent(params) {
      let url = '/admin/api/classes/remove-student';
      return this.post(url, params);
  }

  uploadStudentCSV(data) {
    const url = '/admin/api/classes/upload';
    return this.post(url, data);
  }
}
