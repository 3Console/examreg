import BaseRequest from '../lib/BaseRequest'

export default class SemesterRequest extends BaseRequest {
  getSemesters(params) {
    let url = '/admin/api/semesters';
    return this.get(url, params);
  }

  createSemester(params) {
    let url = '/admin/api/semesters/create';
    return this.post(url, params);
  }

  updateSemester(params) {
      let url = '/admin/api/semesters/update';
      return this.post(url, params);
  }

  removeSemester(params) {
      let url = '/admin/api/semesters/remove';
      return this.post(url, params);
  }

  getSemester(id) {
    const url = `/admin/api/semesters/${id}/detail`;
    return this.get(url);
  }

  getSemesterClass(params) {
    let url = '/admin/api/semesters/class';
    return this.get(url, params);
  }

  createSemesterClass(params) {
    let url = '/admin/api/semesters/create-class';
    return this.post(url, params);
  }

  updateSemesterClass(params) {
      let url = '/admin/api/semesters/update-class';
      return this.post(url, params);
  }

  removeSemesterClass(params) {
      let url = '/admin/api/semesters/remove-class';
      return this.post(url, params);
  }
}
