import { defineStore } from 'pinia';
import axios from '@/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: {
      id: '',
      name: '',
      email: '',
      role: '',
      enroll_date: '',
      expected_graduate_date: '',
      actual_graduate_date: '',
      must_reset_password: false // Flag for first-time password reset
    },
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: state => !!state.token,
    userRole: state => state.user.role,
    mustResetPassword: state => state.user.must_reset_password,
  },

  actions: {
    async login(email, password) {
      try {
        // 1️⃣ Send login request
        const response = await axios.post('/login', { email, password });
        const { token, user } = response.data;

        // 2️⃣ Store token
        this.token = token;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        // 3️⃣ Assign user from initial login payload
        this.user = {
          id: user.id,
          name: user.name,
          email: user.email,
          role: user.role,
          enroll_date: user.enroll_date || '',
          expected_graduate_date: user.expected_graduate_date || '',
          actual_graduate_date: user.actual_graduate_date || '',
          must_reset_password: !!user.must_reset_password
        };

        return this.user;
      } catch (error) {
        console.error('Login failed:', error.response?.data || error.message);
        throw error;
      }
    },

    async fetchUser() {
      if (!this.token) {
        this.resetStore();
        return;
      }

      try {
        const response = await axios.get('/me');
        const data = response.data;
        this.user = {
          id: data.id,
          name: data.name,
          email: data.email,
          role: data.role,
          enroll_date: data.enroll_date || '',
          expected_graduate_date: data.expected_graduate_date || '',
          actual_graduate_date: data.actual_graduate_date || '',
          must_reset_password: !!data.must_reset_password
        };
      } catch (error) {
        if (error.response?.status === 401) this.logout();
        else console.error('fetchUser error:', error);
        throw error;
      }
    },

    logout() {
      this.token = null;
      this.resetStore();
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
      console.log('Logged out');
    },

    resetStore() {
      this.user = {
        id: '',
        name: '',
        email: '',
        role: '',
        enroll_date: '',
        expected_graduate_date: '',
        actual_graduate_date: '',
        must_reset_password: false
      };
    },
  },

  persist: true,
});
