import AdminRequest from '../requests/AdminRequest';
import UserRequest from '../requests/UserRequest';
import SemesterRequest from '../requests/SemesterRequest';
import ClassRequest from '../requests/ClassRequest';
import ScheduleRequest from '../requests/ScheduleRequest';
import ShiftRequest from '../requests/ShiftRequest';
import LocationRequest from '../requests/LocationRequest';

const requestMap = {
  AdminRequest,
  UserRequest,
  SemesterRequest,
  ClassRequest,
  ScheduleRequest,
  ShiftRequest,
  LocationRequest,
};

const instances = {};

export default class RequestFactory {

  static getRequest(classname) {
    let RequestClass = requestMap[classname];
    if (!RequestClass) {
      throw new Error('Invalid request class name: ' + classname);
    }

    let requestInstance = instances[classname];
    if (!requestInstance) {
        requestInstance = new RequestClass();
        instances[classname] = requestInstance;
    }

    return requestInstance;
  }

}
