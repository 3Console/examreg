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
}