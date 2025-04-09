import { defineStore } from 'pinia';
import axios from '@/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('/login', { email, password });

        // Save token and user from response
        this.token = response.data.token;
        this.user = response.data.user;

        localStorage.setItem('token', this.token);
        console.log('Login successful:', this.user);

        return this.user;
      } catch (error) {
        console.error('Login failed:', error.response?.data || error.message);
        throw error;
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/me');
        console.log('Raw /me response:', response.data);

        if (response.data && response.data.user) {
          this.user = response.data.user;
          return this.user;
        } else {
          console.warn('No user object in /me response:', response.data);
          this.user = null;
          throw new Error('User not found in response');
        }
      } catch (error) {
        if (error.response?.status === 401) {
          console.warn('Unauthorized. Logging out.');
          this.logout();
        } else {
          console.error('Failed to fetch user:', error);
        }
        throw error;
      }
    },

    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      console.log('Logged out');
    },

    setUser(userData) {
      this.user = userData;
    },
  },
});
