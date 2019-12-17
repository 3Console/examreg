import BaseRequest from '../lib/BaseRequest'

export default class LocationRequest extends BaseRequest {
  getLocations(params) {
    let url = '/admin/api/locations';
    return this.get(url, params);
  }
  getAllLocation() {
    let url = '/admin/api/locations/all';
    return this.get(url);
  }
  createLocation(params) {
    let url = '/admin/api/locations/create';
    return this.post(url, params);
  }
  updateLocation(params) {
      let url = '/admin/api/locations/update';
      return this.post(url, params);
  }
  removeLocation(params) {
      let url = '/admin/api/locations/remove';
      return this.post(url, params);
  }
}
