import Layout from './Layout';
import Login from './pages/auth/Login';
import Landing from './pages/Landing';
import Account from './pages/Account';
import NotFound from './pages/NotFound';
import Profile from './pages/Profile';
import Schedule from './pages/MySchedule';
import ScheduleDetail from './pages/ScheduleDetail';
import WrapPage from './pages/WrapPage';
import ExamRegister from './pages/ExamRegister';

export default {
  mode: 'history',
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: Login,
    },
    {
      path: '/',
      name: 'Layout',
      component: Layout,
      children: [
        {
          path: '/',
          name: 'Landing',
          component: Landing,
        },
        {
          path: '/',
          name: 'Account',
          component: Account,
          children :[
            {
              path: '',
              component: WrapPage,
              children : [
                {
                  path: 'profile',
                  name: 'Profile',
                  component: Profile,
                },
                {
                  path: 'schedule',
                  component: WrapPage,
                  children: [
                    {
                      path: '/',
                      name: 'Schedule',
                      component: Schedule,
                    },
                    {
                      path: ':id/detail',
                      name: 'Schedule Detail',
                      component: ScheduleDetail,
                      meta: {
                        prop: true,
                      },
                    }
                  ]
                },
              ]
            }
          ]
        },
        {
          path: '/exam-register',
          name: 'Exam Register',
          component: ExamRegister,
        },
        { path: '*', name: '404', component: NotFound }
      ]
    }
  ]
}
