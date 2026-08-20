import axios from 'axios'

const API = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
  }
})

// Add CSRF token to requests if available
API.interceptors.request.use(config => {
  const token = document.querySelector('meta[name="csrf-token"]')?.content
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token
  }
  return config
})

export const fetchAPI = (url, config = {}) => API.get(url, config)
export const postAPI = (url, data = {}, config = {}) => API.post(url, data, config)
export const putAPI = (url, data = {}, config = {}) => API.put(url, data, config)
export const deleteAPI = (url, config = {}) => API.delete(url, config)

export default API
