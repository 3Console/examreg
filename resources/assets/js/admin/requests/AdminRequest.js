import BaseRequest from '../lib/BaseRequest'

export default class AdminRequest extends BaseRequest {

  clearCache() {
    let url = '/admin/api/clear-cache';
    return this.post(url);
  }

  getUsers(params) {
    let url = '/admin/api/users2';
    return this.get(url, params);
  }

  getUserBalances(params) {
    return this.get('/admin/api/user-balances', params);
  }

  getUserTransactions(params) {
    return this.get('/admin/api/user-transactions', params);
  }

  updateUser(params) {
    let url = '/admin/api/user/update';
    return this.post(url, params);
  }

  getUserLoginHistory(params) {
      let url = '/admin/api/user-login-history/';
      return this.get(url, params);
  }

  getAdmins(params) {
    const url = '/admin/api/administrators';
    return this.get(url, params);
  }

  getAdministratorById(id) {
    const url = `/admin/api/administrators/${id}`;
    return this.get(url);
  }

  updateAdministrator(params) {
    const url = '/admin/api/administrators/update';
    return this.post(url, params);
  }

  createAdministrator(params) {
    const url = '/admin/api/administrators/create';
    return this.post(url, params);
  }

  deleteAdministrator(id) {
    const url = '/admin/api/administrators/delete';
    return this.del(url, { id: id });
  }

  uploadCSV(data) {
    const url = '/admin/api/user/upload';
    return this.post(url, data);
  }

  updateUserBalance(params) {
    let url = '/admin/api/user-balances/update';
    return this.post(url, params);
  }

  getBounties(params) {
    return this.get('/admin/api/bounties', params);
  }

  getUserBountyHistory(params) {
    return this.get('/admin/api/bounties/detail', params);
  }

  getReport(params) {
    const url = '/admin/api/bounties/reports';
    return this.get(url, params);
  }
}
