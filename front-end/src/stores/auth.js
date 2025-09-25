import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiService from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(apiService.getToken())

  const isAuthenticated = computed(() => !!token.value)

  const login = async (credentials) => {
    try {
      const response = await apiService.login(credentials)

      token.value = response.token
      user.value = response.user || { email: credentials.email }

      return response
    } catch (error) {
      throw error
    }
  }

  const register = async (credentials) => {
    try {
      const response = await apiService.register(credentials)

      token.value = response.token
      user.value = response.user || {
        username: credentials.username,
        email: credentials.email
      }

      return response
    } catch (error) {
      throw error
    }
  }

  const logout = () => {
    apiService.logout()
    token.value = null
    user.value = null
  }

  const initializeAuth = () => {
    const storedToken = apiService.getToken()
    if (storedToken) {
      token.value = storedToken
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    initializeAuth
  }
})