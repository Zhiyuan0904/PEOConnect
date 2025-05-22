import { createRouter, createWebHistory } from 'vue-router';

// Lazy-loaded components for better performance
const Home = () => import('../components/views/Home.vue');
const SignUp = () => import('../components/views/auth/SignUp.vue');
const Login = () => import('../components/views/auth/Login.vue');
const ForgotPassword = () => import('../components/views/auth/ForgotPassword.vue');
const ResetPassword = () => import('../components/views/auth/ResetPassword.vue');
const Dashboard = () => import('../components/views/dashboard/Dashboard.vue');
const UpdateProfile = () => import('../components/views/profile/UpdateProfile.vue');
const ViewProfile = () => import('../components/views/profile/ViewProfile.vue');
const ManageSurveys = () =>import('../components/views/survey/ManageSurveys.vue');
const CreateSurveys = () => import('../components/views/survey/CreateSurveys.vue');
const EditSurveys = () => import('../components/views/survey/EditSurveys.vue');
const RespondSurveys = () => import('../components/views/survey/RespondSurveys.vue');
const SurveyResponses = () => import('../components/views/survey/ManageResponses.vue');
const SurveyDistributions = () => import('../components/views/distribution/ManageDistributions.vue');

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
  {
    path: '/manage/surveys',
    name: 'manage-surveys',
    component: ManageSurveys,
    meta: {
      title: 'Manage Surveys',
      requiresAuth: true,
      requiresRole: 'admin'
    }
  },  
  { 
    path: '/create/surveys',
    name: 'create-surveys',
    component: CreateSurveys,
    meta: {
      title: 'Create Surveys',
      requiresAuth: true
    }
  },
  { 
    path: '/edit/surveys/:id',
    name: 'edit-survey',
    component: EditSurveys,
    meta: {
      title: 'Edit Survey',
      requiresAuth: true
    }
  }, 
  {
    path: '/respond/surveys', //Respond Survey
    name: 'respond-surveys',
    component: RespondSurveys,
    meta: {
      title: 'Respond to Surveys',
      requiresAuth: true,
      requiresRole: 'student'
    }
  },  
  {
    path: '/manage/responses',  //Manage Survey
    name: 'survey-responses',
    component: SurveyResponses,
    meta: {
      title: 'Survey Responses',
      requiresAuth: true,
      requiresRole: 'admin'
    }
  }, 
  {
    path: '/manage/distributions',  //Manage Distributions Survey
    name: '/survey-distributions',
    component: SurveyDistributions,
    meta: {
      title: 'Survey Distributions',
      requiresAuth: true,
      requiresRole: 'admin'
    }
  },  
  // catch-all 404 route
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  },
];

import { useAuthStore } from '@/stores/auth';

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

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const isAuthenticated = authStore.isAuthenticated;

  // Set page title
  document.title = to.meta.title ? `${to.meta.title} | PEOConnect` : 'PEOConnect';

  // Auth protection
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } });
  }

  // Guest-only routes (like login/signup)
  if (to.meta.guestOnly && isAuthenticated) {
    return next({ name: 'dashboard' });
  }

  // 🚀 Role-based protection (NEW!)
  if (to.meta.requiresRole) {
    const userRole = authStore.user?.role;
    if (userRole !== to.meta.requiresRole) {
      console.warn(`Access denied: Required ${to.meta.requiresRole}, you have ${userRole}`);
      return next({ name: 'dashboard' }); // Redirect student to dashboard
    }
  }

  next();
});

export default router;