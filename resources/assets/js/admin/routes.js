import Vue from 'vue';
import VueRouter from 'vue-router';
import NotFound from './pages/NotFound';
import WrapPage from './pages/WrapPage';

import Semester from './pages/semesters/Semester';
import SemesterClass from './pages/semesters/SemesterClass';

import Users from './pages/users/Users';

import Classes from './pages/classes/Classes';
import UserClass from './pages/classes/UserClass';

import Schedule from './pages/schedules/Schedule';
import ScheduleDetail from './pages/schedules/ScheduleDetail';

import Shift from './pages/shifts/Shift';

import Location from './pages/locations/Location';

Vue.use(VueRouter);

export default new VueRouter({
  mode: 'history',
  base: '/admin',
  routes: [
    {
      path: '/',
      redirect: {name: 'Users'},
      alias: '/home',
      meta: {}
    },
    {
      path: '/users',
      name: 'Users',
      component: Users,
      meta: {
        type: 'item',
        icon: 'icon-avatar',
        routerNameDisp: 'Account',
        hasPermission: true,
      }
    },
    {
      path: '/semesters',
      component: WrapPage,
      meta: {
        type: 'item',
        icon: 'fa fa-list-alt',
        routerNameDisp: 'Semesters',
        hasPermission: true,
      },
      children: [
        {
          path: '/',
          fullPath: '/semesters',
          name: 'Semester',
          component: Semester,
          meta: {
            type: 'item',
            hasPermission: true,
          },
        },
        {
          path: ':id/detail',
          fullpath: '/semesters/:id/detail',
          name: 'Semester Class',
          component: SemesterClass,
          meta: {
            prop: true,
            hasPermission: true,
          }
        },
      ]
    },
    {
      path: '/classes',
      component: WrapPage,
      meta: {
        type: 'item',
        icon: 'fa fa-home',
        routerNameDisp: 'Classes',
        hasPermission: true,
      },
      children: [
        {
          path: '/',
          fullPath: '/classes',
          name: 'Classes',
          component: Classes,
          meta: {
            type: 'item',
            hasPermission: true,
          },
        },
        {
          path: ':id/detail',
          fullpath: '/classes/:id/detail',
          name: 'User Class',
          component: UserClass,
          meta: {
            prop: true,
            hasPermission: true,
          }
        },
      ]
    },
    {
      path: '/schedules',
      component: WrapPage,
      meta: {
        type: 'item',
        icon: 'fa fa-calendar',
        routerNameDisp: 'Schedules',
        hasPermission: true,
      },
      children: [
        {
          path: '/',
          fullPath: '/schedules',
          name: 'Schedule',
          component: Schedule,
          meta: {
            type: 'item',
            hasPermission: true,
          },
        },
        {
          path: ':id/detail',
          fullpath: '/schedules/:id/detail',
          name: 'Schedule Detail',
          component: ScheduleDetail,
          meta: {
            prop: true,
            hasPermission: true,
          }
        },
      ]
    },
    // {
    //   path: '/schedules',
    //   name: 'Schedule',
    //   component: Schedule,
    //   meta: {
    //     type: 'item',
    //     icon: 'fa fa-calendar',
    //     routerNameDisp: 'Schedules',
    //     hasPermission: true,
    //   },
    // },
    {
      path: '/shifts',
      name: 'Shift',
      component: Shift,
      meta: {
        type: 'item',
        icon: 'fa fa-clock-o',
        routerNameDisp: 'Shifts',
        hasPermission: true,
      },
    },
    {
      path: '/locations',
      name: 'Location',
      component: Location,
      meta: {
        type: 'item',
        icon: 'fa fa-map-marker',
        routerNameDisp: 'Locations',
        hasPermission: true,
      },
    },
    { path: '*', name: 'Not Found', component: NotFound, meta: {} }
  ]
})
