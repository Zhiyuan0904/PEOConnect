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
const ManageCurriculumContent = () => import('../components/views/curriculum/ManageCurriculumContent.vue');
const ManagePEO = () => import('../components/views/curriculum/ManagePEO.vue');
const TrackProgress = () => import('../components/views/progress/TrackProgress.vue');
const ExportReports = () => import('../components/views/reports/ManageReports.vue');
const ManageRoles = () => import('../components/views/roles/ManageRoles.vue');
const EmailVerifiedSuccess = () => import('../components/views/auth/VerifiedSuccess.vue');
const VerifyEmail = () => import('../components/views/auth/VerifyEmail.vue');
const ResetPasswordFirstTime = () => import("../components/views/auth/ResetPasswordFirstTime.vue");


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
      requiresAuth: true,
      requiredRoles: ['admin', 'quality team', 'dean']
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
      requiresRole: ['admin', 'quality team', 'dean', 'lecturer']
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
      requiresRole: ['student', 'alumni']
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
    name: 'survey-distributions',
    component: SurveyDistributions,
    meta: {
      title: 'Survey Distributions',
      requiresAuth: true,
      requiresRole: ['admin', 'quality team']
    }
  }, 
  {
    path: '/manage-curriculum-content',
    name: 'manage-curriculum-content',
    component: ManageCurriculumContent,
    meta: {
      requiresAuth: true,
      requiresRole: ['admin', 'quality team', 'lecturer']
    }
  },
  {
    path: '/manage/PEOs',
    name: 'manage-peo',
    component: ManagePEO,
    meta: {
      title: 'Manage PEO',
      requiresAuth: true,
      requiresRole: ['admin', 'quality team', 'lecturer']
    }
  },
  {
    path: '/track/progress',
    name: 'TrackProgress',
    component: TrackProgress,
    meta: { 
      title: 'Track Progress',
      requiresAuth: true, 
      requiresRole: ['admin', 'quality team', 'dean'] 
    }
  },
  {
    path: '/reports',
    name: 'Export Reports',
    component: ExportReports,
    meta: { 
      title: 'Export Reports',
      requiresAuth: true, 
      requiresRole: ['admin', 'quality team', 'dean'] 
    }
  },
  {
    path: '/manage-roles',
    name: 'manage-roles',
    component: ManageRoles,
    meta: { 
      title: 'Manage Roles',
      requiresAuth: true,
      requiresRole: 'admin'
    }
  },
  {
    path: '/email/verify/:id/:hash',
    name: 'verify-email',
    component: VerifyEmail,
    meta: {
      title: 'Verify Email'
    }
  },
  {
    path: '/email/verified-success',
    name: 'email-verified-success',
    component: EmailVerifiedSuccess,
  },
  {
    path: '/reset-password-first-time',
    name: 'ResetPasswordFirstTime',
    component: ResetPasswordFirstTime,
    meta: { 
      requiresAuth: true 
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

  // ✅ Set dynamic page title
  document.title = to.meta.title ? `${to.meta.title} | PEOConnect` : 'PEOConnect';

  // ✅ Guest-only routes: allow access if not logged in
  if (to.meta.guestOnly) {
  if (isAuthenticated) {
    const user = authStore.user;

    if (!user || !user.role) {
      await authStore.logout(); // corrupted token or missing info
      return next();
    }

    if (to.name === 'login') {
      return next(); // ✅ Allow login page even if logged in
    }

    return next({ name: 'dashboard' }); // 🚫 Redirect others
  }
  return next();
}


  // ✅ Auth-required route check
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } });
  }

  // ✅ Role-based access check (only for logged-in users)
  if (to.meta.requiresRole && isAuthenticated) {
    const userRole = authStore.user?.role;
    const requiredRoles = Array.isArray(to.meta.requiresRole)
      ? to.meta.requiresRole
      : [to.meta.requiresRole];

    if (!requiredRoles.includes(userRole)) {
      console.warn(`Access denied: Required ${requiredRoles.join(', ')}, you have ${userRole}`);
      return next({ name: 'dashboard' });
    }
  }

  next();
});

export default router;