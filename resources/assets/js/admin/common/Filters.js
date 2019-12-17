import Numeral from '../lib/numeral';
import Vue from 'vue';
import Utils from './Utils';
import moment from 'moment';
import BigNumber from 'bignumber.js';

Vue.filter('formatUsdAmount', function (value, zeroValue) {
  if (window._.isNil(zeroValue)) {
    zeroValue = '';
  }
  return value ? Numeral(value).format("0,0") : zeroValue;
});

Vue.filter('formatNaturalPart', function (value) {
    return Utils.formatCurrencyAmount(Math.floor(value), 'usd', value >= 0 ? '0' : '');
});

Vue.filter('phoneNumber', function(value) {
  if (value) {
    return value.replace(/(\d{3})(\d{4})(\d{4})/, '$1.$2.$3');
  }
});

Vue.filter( 'uppercase', function (value) {
  if (!!value) return ('' + value).toUpperCase();
  return '';
});

Vue.filter( 'upperFirst', function (value) {
  return window._.upperFirst(value);
});

Vue.filter( 'uppercaseFirst', function (value) {
  return window._.startCase(value);
});

Vue.filter( 'getPrecision', function (value) {
  return Numeral(value).format(".[00000000]");
});

Vue.filter( 'getPrecisionNoDot', function (value) {
  return Numeral(value).format(".[00000000]").replace('.', '');
});

Vue.filter('naturalPartOfNumber', function (number) {
  return Math.floor(number);
});

Vue.filter('naturalPartOfNumberWithDot', function (number) {
  let value =  Math.floor(number);
  return Numeral(value).format("0,0") + '.';
});

Vue.filter('to2Precision', function (value) {
  return Numeral(value).format("0.00");
});
Vue.filter('changedPercent', function (value) {
  let pattern = '0.00';
  if (value > 0) {
    pattern = `+${pattern}`;
  }
  return Numeral(value).format(pattern) + "%";
});
Vue.filter('floatToPercent', function (value) {
  return Numeral(value*100).format("0.00") + "%";
});
Vue.filter('changedPercentFee', function (value) {
  return Numeral(value*100).format("0.[0000]") + ' %';
});
Vue.filter('percentWithOneDecimal', function (value) {
  return Numeral(value).format("0.0") + "%";
});
Vue.filter('currencyName', function (value) {
  return Utils.getCurrencyName(value);
});
Vue.filter('fullName', function (value) {
  return window.i18n.t('currency.' + value + '.fullname');
});

Vue.filter('upperCaseFirstLetter', function (value) {
  switch (value.toLowerCase()) {
    case 'usd' :
    case 'ripple' :
      return value.toUpperCase();
    default :
      return value.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
  }
});

Vue.filter('formatCurrencyAmount', function(amount, currency, zeroValue) {
  return Utils.formatCurrencyAmount(amount, currency, zeroValue);
});

Vue.filter('formatOrderPrice', function(amount, currency, zeroValue) {
  if (amount) {
    return Utils.formatCurrencyAmount(amount, currency, zeroValue);
  } else {
    return window.i18n.t('order_list.market_price');
  }
});

Vue.filter('create_order_label', function(orderType, tradeType) {
  if (orderType && tradeType) {
    return window.i18n.t('create_order.button_texts.' + tradeType + '.' + orderType);
  } else {
    return '';
  }
});

Vue.filter('dateFormat', function (date) {
  return moment(date, 'YYYY-MM-DD HH:mm:ss').format('YYYY.MM.DD');
});

Vue.filter('dateTimeFormat', function (date) {
  return moment(date, 'YYYY-MM-DD HH:mm:ss').format('YYYY.MM.DD HH::mm A');
});

Vue.filter('timeFormat', function(date) {
  return moment(date, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss');
});

Vue.filter('dateFormatSupport', function (date) {
  return moment(date, 'YYYY-MM-DD HH:mm:ss').format('YYYY-M-DD');
})

Vue.filter('date', function (date) {
  return moment(date, 'YYYY-MM-DD HH:mm:ss').format('MM.DD');
});

Vue.filter('time', function (date) {
  return moment(date, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss');
});

Vue.filter('orderTime', function (timestamp) {
  return moment(timestamp, 'x').format('HH:mm:ss');
});

Vue.filter('orderDateTime', function (timestamp) {
  return moment(timestamp, 'x').format('MM.DD HH:mm:ss');
});

Vue.filter('orderDate', function (timestamp) {
  return moment(timestamp, 'x').format('MM.DD');
});

Vue.filter('timestampToDate', function (timestamp) {
  return moment(timestamp, 'x').format('YYYY-MM-DD HH:mm:ss');
});

Vue.filter('YearMonthDate', function (timestamp) {
  return moment(timestamp, 'x').format('YYYY-MM-DD');
});

Vue.filter('fourNumberId', function (id) {
  if (id != null) {
    let tmpId = "00000000" + id;
    return tmpId.substring(tmpId.length, tmpId.length - 4)
  }
});

Vue.filter('user_type', function (type) {
  switch(type) {
    case 'normal':
      return '일반';
    case 'company':
      return '회사';
    case 'referrer':
      return '추천인';
  }
  return type;
});

Vue.filter('order_status', function (order) {
  if (order.status == 'canceled') {
    return window.i18n.t('common.order_status.canceled');
  }

  if (order.executed_quantity == order.quantity) {
    return window.i18n.t('common.order_status.filled');
  }

  if (order.status == 'pending') {
    return window.i18n.t('common.order_status.pending');
  }

  return window.i18n.t('common.order_status.partial_filled');
});

Vue.filter('securityLevel', function (level) {
  switch (level) {
    case 1:
      return window.i18n.t('security_level.email');
    case 2:
      return window.i18n.t('security_level.sms');
    case 3:
      return window.i18n.t('security_level.id');
    case 4:
      return window.i18n.t('security_level.bank');
    case 5:
      return window.i18n.t('security_level.otp');
  }
});

Vue.filter('tradeType', function (trade_type) {
  switch (trade_type) {
    case 'buy' :
      return window.i18n.t('common.trade_type.buy');
    case 'sell':
      return window.i18n.t('common.trade_type.sell');
  }
});

Vue.filter('number', function (number, round = 2) {
  if (!number || !isFinite(number)) {
    return 0;
  }
  return Number(parseFloat(number).toFixed(round));
});

Vue.filter('formatTimeStamp', function (timestamp, format) {
  return moment(timestamp, 'x').format(format);
});

Vue.filter('abs', function (number) {
  return Math.abs(number);
});

Vue.filter('transactionStatus', function (status) {
  return window.i18n.t('common.transaction_status.' + status);
});

Vue.filter('convertToBigNumber', function (number) {
  if (!number) return "0";
  return (new BigNumber(number)).toString();
});

Vue.filter('mulBigNumber', function (number1, number2) {
  if (!number1 || !number2) return "0";
  return (new BigNumber(number1)).mul(number2).toString();
});

Vue.filter('divBigNumber', function (number1, number2) {
  if (!number1) return "0";
  if (!number2) return "1";
  return (new BigNumber(number1)).div(number2).toString();
});

Vue.filter('convertConditionStatus', function (condition) {
  if (condition == 'le') return '<=';
  if (condition == 'ge') return '>=';
  return '';
});

Vue.filter('paddingRight', function (value, length, character) {
  const number = parseFloat(value);
  if (isNaN(number) || length <= 0) {
    return value;
  }
  const strValue = `${value}`;
  const [strNaturalPart, strDecimalPart] = strValue.split('.');
  return `${strNaturalPart}.${window._.padEnd(strDecimalPart, length, character)}`;
});

Vue.filter('toNumber', function (value) {
  const number = parseFloat(value);
  if (isNaN(number)) {
    return value;
  }
  // is e number (Ex: 1e-7)
  if (number.toString().includes('e')) {
    return Utils.trimEndZero(new BigNumber(`${value || 0}`).toFixed(20));
  }
  return number;
});
