import zlib from 'browserify-zlib';
import buffer from 'buffer';
import rf from '../lib/RequestFactory';

const masterdataVersionKey = 'masterdata_version';
const masterdataKey = 'masterdata';

export default class MasterdataUtils {

  static loadData() {
    MasterdataUtils.masterdataVersion = window.localStorage.getItem(masterdataVersionKey);
    const zipData = window.localStorage.getItem(masterdataKey);
    if (!zipData) {
      return;
    }

    try {
      const buf = buffer.Buffer.from(zipData, 'base64');
      const unzipData = zlib.unzipSync(buf).toString();
      MasterdataUtils.cacheMasterdata = JSON.parse(unzipData);
    } catch (e) {
      console.log(e);
    }
  }

  static isDataLoaded() {
    return MasterdataUtils.masterdataVersion;
  }

  static isDataChanged(version) {
    if (!MasterdataUtils.isDataLoaded()) {
      MasterdataUtils.loadData()
    }
    return version && MasterdataUtils.masterdataVersion != version;
  }

  static clearMasterdata() {
    MasterdataUtils.masterdataVersion = undefined;
    MasterdataUtils.cacheMasterdata = undefined;
    window.localStorage.removeItem(masterdataVersionKey);
    window.localStorage.removeItem(masterdataKey);
  }

  static async saveMasterdata(version, data) {
    MasterdataUtils.cacheMasterdata = data;
    MasterdataUtils.masterdataVersion = version;

    let zipData = undefined;
    try {
      zipData = zlib.gzipSync(JSON.stringify(MasterdataUtils.cacheMasterdata)).toString('base64');
    } catch (e) {
      console.log(e);
    }
    window.localStorage.setItem(masterdataKey, zipData);
    window.localStorage.setItem(masterdataVersionKey, MasterdataUtils.masterdataVersion);
    return data;
  }

  static getCachedMasterdata() {
    if (!MasterdataUtils.isDataLoaded()) {
      MasterdataUtils.loadData()
    }
    return MasterdataUtils.cacheMasterdata;
  }

  static getCoins(callback) {
    rf.getRequest('MasterdataRequest').getAll().then(res => {
      let coins = [];
      coins = window._.flatMap(res.coin_settings, function(value) {
        return [value.coin];
      });
      coins = window._.uniq(coins);
      if (callback) {
        callback(coins);
      }
    });
  }

  static getCurrencies(callback) {
    rf.getRequest('MasterdataRequest').getAll().then(res => {
      let currencies = [];
      currencies = window._.flatMap(res.coin_settings, function(value) {
        return [value.currency];
      });
      currencies = window._.uniq(currencies);
      if (callback) {
        callback(currencies);
      }
    });
  }

  static getCurrenciesAndCoins(callback) {
    rf.getRequest('MasterdataRequest').getAll().then(res => {
      let currencies = [];
      let coins = [];
      currencies = window._.flatMap(res.coin_settings, function(value) {
        return [value.currency];
      });
      coins = window._.flatMap(res.coin_settings, function(value) {
        return [value.coin];
      });
      currencies = window._.uniq(currencies.concat(coins));
      if (callback) {
        callback(currencies);
      }
    });
  }
};
