<template>
    <div v-if="visible" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative">
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-[#1e7c99]">📅 Schedule Survey Distribution</h2>
          <button @click="closeModal" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
        </div>
  
        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Survey -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Select Survey</label>
            <select v-model="form.survey_id" required class="w-full border px-3 py-2 rounded">
              <option disabled value="">-- Select a survey --</option>
              <option v-for="survey in surveys" :key="survey.id" :value="survey.id">
                {{ survey.title }}
              </option>
            </select>
          </div>
  
          <!-- Role -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Target Audience</label>
            <select v-model="form.target_role" required class="w-full border px-3 py-2 rounded">
              <option value="student">Student</option>
              <option value="alumni">Alumni</option>
            </select>
          </div>
  
          <!-- Date Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Date Field</label>
            <select v-model="form.date_field" required class="w-full border px-3 py-2 rounded">
              <option value="enroll_date">Enrolment Date</option>
              <option value="expected_graduate_date">Graduation Date</option>
            </select>
          </div>
  
          <!-- Start & End Date -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Start Date</label>
              <input v-model="form.start_date" type="date" class="w-full border px-3 py-2 rounded" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">End Date</label>
              <input v-model="form.end_date" type="date" class="w-full border px-3 py-2 rounded" required />
            </div>
          </div>
  
          <!-- Scheduled Active Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Scheduled Active Date</label>
            <input v-model="form.scheduled_active_date" type="date" class="w-full border px-3 py-2 rounded" required />
          </div>
  
          <!-- Buttons -->
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-[#1e7c99] text-white rounded hover:bg-[#156a84]">
              Create
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue'
  import axios from '@/axios'
  
  // Props
  const props = defineProps({
    visible: Boolean
  })
  const emit = defineEmits(['close', 'created'])
  
  const form = ref({
    survey_id: '',
    target_role: 'student',
    date_field: 'enrol_date',
    start_date: '',
    end_date: '',
    scheduled_active_date: ''
  })
  
  const surveys = ref([])
  
  const fetchSurveys = async () => {
    try {
      const { data } = await axios.get('/surveys')
      surveys.value = data
    } catch (error) {
      console.error('Failed to fetch surveys:', error)
    }
  }
  
  const submitForm = async () => {
    try {
      await axios.post('/survey-distributions', {
        ...form.value,
        is_active: true,  // 🔥 ADD this line
      });
      alert('Survey distribution created successfully!');
      emit('created');
      closeModal();
    } catch (error) {
      console.error('Failed to create distribution:', error);
      alert('Failed to create. Please try again.');
    }
  };

  
  const closeModal = () => {
    emit('close')
  }
  
  // Auto-fetch surveys on modal open
  watch(() => props.visible, (newVal) => {
    if (newVal) fetchSurveys()
  })
  </script>
  