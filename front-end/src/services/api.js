class ApiService {
  constructor() {
    this.baseURL = '/API/v1';
    this.token = localStorage.getItem('auth_token');
  }

  static getInstance() {
    if (!ApiService.instance) {
      ApiService.instance = new ApiService();
    }
    return ApiService.instance;
  }

  setToken(token) {
    this.token = token;
    if (token) {
      localStorage.setItem('auth_token', token);
    } else {
      localStorage.removeItem('auth_token');
    }
  }

  getToken() {
    return this.token;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const headers = {
      'Content-Type': 'application/json',
      ...options.headers,
    };

    if (this.token) {
      headers.Authorization = `Bearer ${this.token}`;
    }

    const config = {
      ...options,
      headers,
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Request failed');
      }

      return data;
    } catch (error) {
      throw error;
    }
  }

  async login(credentials) {
    try {
      const loginData = {
        username: credentials.username,
        password: credentials.password
      };

      const response = await this.request('/login', {
        method: 'POST',
        body: JSON.stringify(loginData),
      });

      if (response.token) {
        this.setToken(response.token);
      }

      return response;
    } catch (error) {
      throw error;
    }
  }

  async register(credentials) {
    try {
      const registerData = {
        username: credentials.username,
        password: credentials.password
      };

      const response = await this.request('/register', {
        method: 'POST',
        body: JSON.stringify(registerData),
      });

      if (response.token) {
        this.setToken(response.token);
      }

      return response;
    } catch (error) {
      throw error;
    }
  }

  logout() {
    this.setToken(null);
  }
}

export default ApiService.getInstance();