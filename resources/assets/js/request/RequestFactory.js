// import MasterdataRequest from './MasterdataRequest';
import UserRequest from './UserRequest';
import ExamRegisterRequest from './ExamRegisterRequest';
// import GameRequest from './GameRequest';
// import GameBountyRequest from './GameBountyRequest';
// import TransactionRequest from './TransactionRequest';
// import ChatRequest from './ChatRequest';
// import UserBountiesRequest from './UserBountiesRequest';
// import RatingRequest from './RatingRequest';
// import NotificationRequest from './NotificationRequest';
// // import PaymentRequest from './PaymentRequest';

const requestMap = {
  // MasterdataRequest,
  UserRequest,
  ExamRegisterRequest
  // GameRequest,
  // GameBountyRequest,
  // TransactionRequest,
  // ChatRequest,
  // UserBountiesRequest,
  // RatingRequest,
  // NotificationRequest
  // // PaymentRequest
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