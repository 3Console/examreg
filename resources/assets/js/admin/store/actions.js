import rf from '../../admin/lib/RequestFactory';

export const getUsdTransactions = ({ commit }, payload) => {
  return new Promise(resolve => {
    rf.getRequest('AdminRequest').getUsdTransaction(payload).then(res => {
      commit('onGetUsdTransactions', res.data);
      resolve(res);
    })
  })
};

export const getKycs = ({ commit }, payload) => {
  return new Promise(resolve => {
    rf.getRequest('AdminRequest').getUserKyc(payload).then(res => {
      commit('onGetKycs', res.data);
      resolve(res);
    })
  })
};

export const getAllClasses = ({ commit }) => {
  return new Promise((resolve) => {
    rf.getRequest('ClassRequest').getClassesAll().then((res) => {
      commit('onGetClasses', res.data);
      resolve(res.data);
    })
  })
};

export const getAllShifts = ({ commit }) => {
  return new Promise((resolve) => {
    rf.getRequest('ShiftRequest').getShiftsAll().then((res) => {
      commit('onGetShifts', res.data);
      resolve(res.data);
    })
  })
};

export const getAllLocations = ({ commit }) => {
  return new Promise((resolve) => {
    rf.getRequest('LocationRequest').getLocationsAll().then((res) => {
      commit('onGetLocations', res.data);
      resolve(res.data);
    })
  })
};