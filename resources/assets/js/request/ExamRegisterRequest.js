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
}