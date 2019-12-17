export default class BaseRequest {
  get(url, params = {}) {
    return new Promise((resolve, reject) => {
      window.axios
        .get(url, {
          params: params
        })
        .then(function (response) {
          resolve(response.data);
        })
        .catch(function (error) {
          reject(error);
        });
    });
  }

  put(url, data = {}) {
    return new Promise((resolve, reject) => {
      window.axios
        .put(url, data)
        .then(function (response) {
          resolve(response.data);
        })
        .catch(function (error) {
          reject(error);
        });
    });
  }

  post(url, data = {}) {
    return new Promise((resolve, reject) => {
      window.axios
        .post(url, data)
        .then(function (response) {
          resolve(response.data);
        })
        .catch(function (error) {
          reject(error);
        });
    });
  }

  del(url, params = {}) {
    return new Promise((resolve, reject) => {
      window.axios
        .delete(url, { params: params } )
        .then(function (response) {
          resolve(response.data);
        })
        .catch(function (error) {
          reject(error);
        });
    });
  }

  _responseHandler(resolve, res) {
    return resolve(res.body.data);
  }

  _errorHandler(reject, err) {
    window.app.$broadcast('EVENT_COMMON_ERROR', err);
    return reject(err);
  }

}
