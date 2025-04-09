import { createRouter, createWebHistory } from 'vue-router';

// Lazy-loaded components for better performance
const Home = () => import('../components/Home.vue');
const SignUp = () => import('../components/SignUp.vue');
const Login = () => import('../components/Login.vue');
const ForgotPassword = () => import('../components/ForgotPassword.vue');
const ResetPassword = () => import('../components/ResetPassword.vue');
const Dashboard = () => import('../components/Dashboard.vue');
const UpdateProfile = () => import('../components/UpdateProfile.vue');
const ViewProfile = () => import('../components/ViewProfile.vue');

const routes = [
  { 
    path: '/', 
    name: 'home',  
    component: Home,
    meta: { 
      title: 'Home', 
      hideNav: true // Example custom meta field
    } 
  },
  { 
    path: '/sign-up', 
    name: 'sign-up',  
    component: SignUp,
    meta: { 
      title: 'Sign Up',
      guestOnly: true 
    }
  },
  { 
    path: '/login', 
    name: 'login',  
    component: Login,
    meta: { 
      title: 'Login',
      guestOnly: true 
    } 
  },
  { 
    path: '/forgot-password', 
    name: 'forgot-password',  
    component: ForgotPassword,
    meta: { 
      title: 'Forgot Password',
      guestOnly: true 
    }
  },
  {
    path: '/reset-password',
    name: 'reset-password',  
    component: ResetPassword,
    props: route => ({
      token: route.query.token,
      email: route.query.email,
    }),
    meta: { 
      title: 'Reset Password',
      guestOnly: true 
    }
  },
  { 
    path: '/dashboard', 
    name: 'dashboard',  
    component: Dashboard,
    meta: { 
      title: 'Dashboard',
      requiresAuth: true // Auth protection
    }
  },
  { 
    path: '/update/profile', 
    name: 'update-profile',  
    component: UpdateProfile,
    meta: { 
      title: 'Update Profile',
      requiresAuth: true
    }
  },
  { 
    path: '/view/profile',
    name: 'view-profile',
    component: ViewProfile,
    meta: {
      title: 'View Profile',
      requiresAuth: true
    }
  },
  // Add a catch-all 404 route
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  // Smooth scroll behavior
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition;
    if (to.hash) return { el: to.hash, behavior: 'smooth' };
    return { top: 0, behavior: 'smooth' };
  }
});

// Route guards
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('token'); // Replace with your auth check
  
  // Set page title
  document.title = to.meta.title ? `${to.meta.title} | Your App` : 'Your App';

  // Auth protection
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } });
  }

  // Guest-only routes (like login/signup)
  if (to.meta.guestOnly && isAuthenticated) {
    return next({ name: 'dashboard' });
  }

  next();
});

export default router;