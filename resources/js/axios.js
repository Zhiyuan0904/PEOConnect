import axios from 'axios';

const instance = axios.create({
  baseURL: 'https://peoconnect.onrender.com/api',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
  },
});

// ✅ Attach Authorization header automatically
instance.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default instance;
