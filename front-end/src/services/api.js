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

    const response = await fetch(url, config);
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Request failed');
    }

    return data;
  }

  async login(credentials) {
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
  }

  async register(credentials) {
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
  }

  logout() {
    this.setToken(null);
  }

  async fetchOrders() {
    const response = await this.request('/orders', {
      method: 'GET',
    });
    return response;
  }

  
}

export default ApiService.getInstance();