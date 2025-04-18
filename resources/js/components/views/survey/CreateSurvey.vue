<template>
    <main class="flex items-center justify-center min-h-screen bg-gray-100">
      <div class="bg-white p-10 rounded-xl shadow-xl w-full max-w-2xl">
        <h1 class="text-3xl font-bold text-[#1e7c99] mb-4">Create New Survey 📋</h1>
        <p class="text-gray-500 mb-8">Design a new survey to collect important insights.</p>
  
        <form @submit.prevent="createSurvey">
          <!-- Survey Title -->
          <div class="mb-6">
            <label class="block mb-2 text-sm font-medium text-gray-700" for="title">Survey Title</label>
            <input
              v-model="form.title"
              id="title"
              type="text"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
              placeholder="Enter survey title"
              required
            />
          </div>
  
          <!-- Survey Description -->
          <div class="mb-6">
            <label class="block mb-2 text-sm font-medium text-gray-700" for="description">Description</label>
            <textarea
              v-model="form.description"
              id="description"
              rows="4"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
              placeholder="Describe the purpose of this survey..."
            ></textarea>
          </div>
  
          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold py-3 rounded-lg transition duration-300"
            :disabled="loading"
          >
            {{ loading ? 'Creating...' : 'Create Survey' }}
          </button>
        </form>
      </div>
    </main>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from '@/axios'  // make sure you configured your axios instance
  
  const form = ref({
    title: '',
    description: '',
  })
  
  const loading = ref(false)
  
  const createSurvey = async () => {
    try {
      loading.value = true
  
      await axios.post('/surveys', form.value)
  
      alert('Survey created successfully!')
      form.value.title = ''
      form.value.description = ''
    } catch (error) {
      console.error(error)
      alert('Failed to create survey. Please try again.')
    } finally {
      loading.value = false
    }
  }
  </script>
  