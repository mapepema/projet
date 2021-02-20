import Vue from 'vue'
import VueRouter from 'vue-router'
import Store from '../store/index'
import Dashboard from '../views/Dashboard.vue'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '',
    name: 'Home',
    component: Home
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('../views/About.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    meta: { requireAuth : true },
    component: Dashboard,
    children: [
      {
        path: '/overview',
        name: 'Overview',
        component: () => import('../views/Overview.vue'),
        meta: { requireAuth : true }
      },
      {
        path: '/test',
        name: 'Test',
        component: () => import('../views/Test.vue'),
        meta: { requireAuth : true }
      },
      {
        path: '/client',
        name: 'Client',
        component: () => import('../views/Client.vue'),
        meta: { requireAuth : true }
      },
      {
        path: '/vm',
        name: 'Vm',
        component: () => import('../views/Vm.vue'),
        meta: { requireAuth : true }
      }
    ]
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.requireAuth) {
    Store.dispatch("ValideToken").then( res => {
      if(res.data.result == 1) {
        next();
      } else {
        Store.dispatch("SetPrevious", to.fullPath);
        next('/login');
      }
    });
  } else {
    next();
  }
})

export default router
