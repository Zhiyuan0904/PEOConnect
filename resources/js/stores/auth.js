import { defineStore } from 'pinia';
import axios from '@/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: {
      name: '',
      email: '',
      role: '',
    },
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userRole: (state) => state.user.role,
  },

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('/login', { email, password });

        this.token = response.data.token;
        localStorage.setItem('token', this.token);

        console.log('Token saved. Now fetching user...');
        await this.fetchUser(); // Automatically fetch user info after login

        return this.user;
      } catch (error) {
        console.error('Login failed:', error.response?.data || error.message);
        throw error;
      }
    },

    async fetchUser() {
      if (!this.token) {
        console.warn('No token found, cannot fetch user.');
        this.user = { name: '', email: '', role: '' };
        return;
      }
    
      try {
        const response = await axios.get('/me');
        console.log('Fetched /me data:', response.data);
    
        if (response.data) { 
          this.user = {
            id: response.data.id || '',
            name: response.data.name || '',
            email: response.data.email || '',
            role: response.data.role || '',
            enroll_date: response.data.enroll_date || '',
            expected_graduate_date: response.data.expected_graduate_date || '',
            actual_graduate_date: response.data.actual_graduate_date || '',
          };
        } else {
          console.warn('Invalid /me response:', response.data);
          this.user = { name: '', email: '', role: '' };
          throw new Error('User not found in response.');
        }
      } catch (error) {
        if (error.response?.status === 401) {
          console.warn('Unauthorized (401). Logging out.');
          this.logout();
        } else {
          console.error('Failed to fetch user:', error);
        }
        throw error;
      }
    },
    
    logout() {
      this.token = null;
      this.user = { name: '', email: '', role: '' };
      localStorage.removeItem('token');
      console.log('Logged out successfully.');
    },

    setUser(userData) {
      this.user = {
        name: userData.name || '',
        email: userData.email || '',
        role: userData.role || '',
      };
    },
  },
  persist: true,
});
