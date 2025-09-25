<template>
  <div class="login-container">
    <div class="login-card">
      <h2>{{ isRegisterMode ? 'Register' : 'Login' }}</h2>
      <form @submit.prevent="handleSubmit" class="login-form">
        <div class="form-group">
          <label for="usename">Username</label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            required
            :disabled="loading"
            class="form-input"
            placeholder="Enter your username"
          />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            :disabled="loading"
            class="form-input"
            placeholder="Enter your password"
          />
        </div>
        <div v-if="error" class="error-message">
          {{ error }}
        </div>
        <button type="submit" :disabled="loading" class="login-button">
          <span v-if="loading">
            {{ isRegisterMode ? 'Registering...' : 'Logging in...' }}
          </span>
          <span v-else>
            {{ isRegisterMode ? 'Register' : 'Login' }}
          </span>
        </button>
        <button
          type="button"
          @click="toggleMode"
          :disabled="loading"
          class="toggle-button"
        >
          {{ isRegisterMode ? 'Already have an account? Login' : "Don't have an account? Register" }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  password: '',
  username: ''
})

const loading = ref(false)
const error = ref('')
const isRegisterMode = ref(false)

const handleLogin = async () => {
  try {
    loading.value = true
    error.value = ''

    await authStore.login(form)

    // Redirect to dashboard or home after successful login
    alert("login successful")
  } catch (err) {
    error.value = err.message || 'Login failed. Please try again.'
  } finally {
    loading.value = false
  }
}

const handleRegister = async () => {
  try {
    loading.value = true
    error.value = ''

    await authStore.register(form)

    // Redirect to dashboard or home after successful registration
    alert("Registration successful")
  } catch (err) {
    error.value = err.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (isRegisterMode.value) {
    await handleRegister()
  } else {
    await handleLogin()
  }
}

const toggleMode = () => {
  isRegisterMode.value = !isRegisterMode.value
  error.value = ''
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
  padding: 1rem;
}

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

.login-card h2 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #333;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #555;
}

.form-input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #007bff;
}

.form-input:disabled {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  padding: 0.5rem;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
}

.login-button {
  padding: 0.75rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.login-button:hover:not(:disabled) {
  background-color: #0056b3;
}

.login-button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.toggle-button {
  padding: 0.5rem;
  background-color: transparent;
  color: #007bff;
  border: 1px solid #007bff;
  border-radius: 4px;
  font-size: 0.875rem;
  cursor: pointer;
  margin-top: 0.5rem;
  transition: all 0.2s;
}

.toggle-button:hover:not(:disabled) {
  background-color: #007bff;
  color: white;
}

.toggle-button:disabled {
  color: #6c757d;
  border-color: #6c757d;
  cursor: not-allowed;
}
</style>