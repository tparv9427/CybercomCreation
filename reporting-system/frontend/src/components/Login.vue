<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-header">
        <div class="logo">🚀</div>
        <h1>Reporting System</h1>
        <p>Phase 2: Secure Access</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label>Email Address</label>
          <input 
            type="email" 
            v-model="email" 
            placeholder="admin@example.com" 
            required 
          />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input 
            type="password" 
            v-model="password" 
            placeholder="••••••••" 
            required 
          />
        </div>

        <div v-if="store.error" class="login-error">
          {{ store.error }}
        </div>

        <button type="submit" class="btn-login" :disabled="store.loading">
          {{ store.loading ? 'Authenticating...' : 'Sign In' }}
        </button>
      </form>

      <div class="login-footer">
        <p>Default credentials: admin@example.com / password</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useReportStore } from '../stores/reportStore'

const store = useReportStore()
const email = ref('admin@example.com')
const password = ref('password')

async function handleLogin() {
  store.error = null
  await store.login(email.value, password.value)
}
</script>

<style scoped>
.login-page {
  height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
  font-family: 'Inter', sans-serif;
}

.login-card {
  background: white;
  padding: 2.5rem;
  border-radius: 1.5rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  width: 100%;
  max-width: 400px;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.logo {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.login-header h1 {
  font-size: 1.75rem;
  font-weight: 800;
  color: #111827;
  margin: 0;
}

.login-header p {
  color: #6b7280;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #374151;
}

.form-group input {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.form-group input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.login-error {
  background: #fef2f2;
  color: #ef4444;
  padding: 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.85rem;
  text-align: center;
  font-weight: 500;
}

.btn-login {
  background: #6366f1;
  color: white;
  border: none;
  padding: 0.85rem;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 0.5rem;
}

.btn-login:hover:not(:disabled) {
  background: #4f46e5;
  transform: translateY(-1px);
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-footer {
  margin-top: 2rem;
  text-align: center;
}

.login-footer p {
  font-size: 0.75rem;
  color: #9ca3af;
}
</style>
