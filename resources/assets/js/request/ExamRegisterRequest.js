import BaseModelRequest from './base/BaseModelRequest';

export default class ExamRegisterRequest extends BaseModelRequest {
  getSemesters() {
    const url = '/exam-register/semesters';
    return this.get(url);
  }

  getSemesterDetail(params) {
    const url = '/exam-register/semesters/detail';
    return this.get(url, params);
  }

  getAllSemesterClass(params) {
    const url = '/exam-register/semesters/classes';
    return this.get(url, params);
  }

  getAllUserClass(params) {
    const url = '/exam-register/semesters/user-class';
    return this.get(url, params);
  }

  checkUserClass(id) {
    const url = `/exam-register/semesters/${id}/check`;
    return this.get(url);
  }

  checkUserSchedule(id) {
    const url = `/exam-register/semesters/${id}/user-schedule`;
    return this.get(url);
  }

  checkSchedule(id) {
    const url = `/exam-register/semesters/${id}/schedule`;
    return this.get(url);
  }

  countSlot(id) {
    const url = `/exam-register/semesters/${id}/slot`;
    return this.get(url);
  }

  getUserStatus(id) {
    const url = `/exam-register/semesters/${id}/user-status`;
    return this.get(url);
  }

  getClassSchedule(params) {
    const url = '/exam-register/semesters/class-schedule';
    return this.get(url, params);
  }

  submitSchedule(params) {
    let url = '/exam-register/semesters/submit';
    return this.post(url, params);
  }

  cancelSchedule(params) {
    let url = '/exam-register/semesters/cancel';
    return this.post(url, params);
  }
}