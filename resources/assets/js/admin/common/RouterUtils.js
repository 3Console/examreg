import rf from '../lib/RequestFactory';
import Const from './Const';

const ROUTER_NOT_FOUND_NAME = 'Not Found';
const ROUTER_HOME_PATH = '/orders/open-order';

export default class RouterUtils {

  static async validRouter(toRouter) {
    const response = await RouterUtils.getRouterPermission();

    if (toRouter.name === ROUTER_NOT_FOUND_NAME) {
      return false;
    }
    const validRouters = window._.filter(RouterUtils.routerPermissions, route => route.isHasPermission);
    if (window._.isEmpty(validRouters)) {
      return false;
    }
    const filtered = window._.filter(RouterUtils.flatItems(validRouters), item => {
        const isMatchPath = item.router && item.router.path === toRouter.path;
        const isIncludeSubRouter = item.router && item.router.subRoutes
            && (item.router.subRoutes.includes(toRouter.path) || item.router.subRoutes.includes(toRouter.name));
        return isMatchPath || isIncludeSubRouter;
      })

    if (window._.isEmpty(filtered)) {
      return RouterUtils.redirectAvailableRouterIfNeed(validRouters, toRouter);
    }
    return RouterUtils.redirectAvailableRouterIfNeed(filtered, toRouter);
  }

  static getRouterPermission(isPromise = true) {
    return new Promise((resolve, reject) => {
      rf.getRequest('UserRequest').getCurrentAdmin().then(res => {
        const currentAdmin = res.data;
        const permissions = window._.map(currentAdmin.permissions, item => item.name);
        window._.each(Const.MENU, item => {
          item.isHasPermission = window._.includes(permissions, item.name) || currentAdmin.role === Const.ROLE_SUPER_ADMIN;
        })
        RouterUtils.routerPermissions = Const.MENU;
        resolve(Const.MENU);
      })
      .catch(function (error) {
        reject(error);
      });
    });
  }

  static redirectAvailableRouterIfNeed(availableRouters, toRouter) {
    const filtered = window._.chain(availableRouters)
      .filter(route => route.name === toRouter.name)
      .value();
    if (window._.isEmpty(filtered)) {
      return window._.filter(RouterUtils.flatItems(availableRouters), item => item.router)[0].router.path;
    }
    return filtered[0].router.path;
  }

  static flatItems(data) {
    return window._.chain(data)
      .map(obj => window._.isEmpty(obj.items) ? [obj] : window._.concat(obj.items, obj))
      .reduce((a, b) => window._.concat(a, b))
      .value();
  }
};
