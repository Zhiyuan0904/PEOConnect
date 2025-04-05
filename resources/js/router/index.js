import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Home.vue';
import SignUp from '../components/SignUp.vue';
import Login from '../components/Login.vue';
import ForgotPassword from '../components/ForgotPassword.vue';
import ResetPassword from '../components/ResetPassword.vue';

const routes = [
  { 
    path: '/', 
    name: 'home',  // Added name
    component: Home 
  },
  { 
    path: '/sign-up', 
    name: 'sign-up',  // Added name
    component: SignUp 
  },
  { 
    path: '/login', 
    name: 'login',  // Added name
    component: Login 
  },
  { 
    path: '/forgot-password', 
    name: 'forgot-password',  // Added name
    component: ForgotPassword 
  },
  {
    path: '/reset-password',
    name: 'reset-password',  // Added name
    component: ResetPassword,
    props: route => ({
      token: route.query.token,
      email: route.query.email,
    }),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;