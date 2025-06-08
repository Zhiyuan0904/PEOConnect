<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 p-10">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
          <h1 class="text-4xl font-bold text-[#4072bc]">Track Survey Progress</h1>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center text-gray-500 text-lg py-10 animate-pulse">
          Loading progress data...
        </div>

        <!-- Empty -->
        <div v-else-if="progress.length === 0" class="text-center text-gray-400 text-lg py-10">
          No progress data available.
        </div>

        <!-- Progress Cards -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="item in progress"
            :key="item.survey_title"
            class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition flex flex-col justify-between"
          >
            <div class="p-6 flex-1">
              <h2 class="text-xl font-bold text-[#4072bc] mb-3">{{ item.survey_title }}</h2>
              <p class="text-gray-700 mb-3">
                <strong>Responded:</strong> {{ item.responded }} / {{ item.total }}
                <span class="ml-2 text-sm text-[#4072bc] font-bold">({{ item.percentage }}%)</span>
              </p>

              <div class="w-full bg-gray-200 rounded-full h-5 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-[#59a8f7] to-[#a7c8f8] h-5 rounded-full transition-all duration-500"
                  :style="{ width: item.percentage + '%' }"
                ></div>
              </div>
            </div>

            <div class="flex justify-center border-t px-4 py-4 bg-[#f9fafb]">
              <button
                @click="fetchDetails(item.survey_title)"
                class="w-60 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] transition text-white px-8 py-3 rounded-full shadow-lg"
              >
                View Details
              </button>
            </div>
          </div>
        </div>

        <!-- Detail Modal -->
        <transition name="modal-fade">
          <div v-if="showDetail" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-6xl p-10 rounded-2xl shadow-2xl relative overflow-y-auto max-h-[90vh]">
              <h2 class="text-3xl font-bold text-[#4072bc] mb-8">{{ selectedTitle }} – Respondents</h2>

              <!-- Filters -->
              <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <input
                  v-model="filter"
                  type="text"
                  placeholder="🔍 Search by name or email..."
                  class="flex-1 p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-[#4072bc] focus:outline-none"
                />
                <select
                  v-model="responseFilter"
                  class="p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-[#4072bc] focus:outline-none"
                >
                  <option value="">All</option>
                  <option value="responded">✅ Responded</option>
                  <option value="not_responded">❌ Not Responded</option>
                </select>
              </div>

              <!-- Table -->
              <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm text-left">
                  <thead class="bg-[#f1f5f9] text-[#4072bc] font-semibold">
                    <tr>
                      <th class="py-4 px-6">Name</th>
                      <th class="py-4 px-6">Email</th>
                      <th class="py-4 px-6">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="student in filteredStudents"
                      :key="student.email"
                      class="border-t hover:bg-gray-50 transition"
                    >
                      <td class="py-3 px-6">{{ student.name }}</td>
                      <td class="py-3 px-6">{{ student.email }}</td>
                      <td class="py-3 px-6">
                        <span :class="student.responded ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold'">
                          {{ student.responded ? 'Responded' : 'Not Responded' }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <button
                @click="showDetail = false"
                class="absolute top-4 right-4 text-gray-600 hover:text-black text-3xl font-bold"
                aria-label="Close"
              >×</button>
            </div>
          </div>
        </transition>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '@/axios'
import Sidebar from '@/components/common/Sidebar.vue'

const loading = ref(true)
const progress = ref([])
const showDetail = ref(false)
const selectedTitle = ref('')
const students = ref([])
const filter = ref('')
const responseFilter = ref('')

const fetchProgress = async () => {
  try {
    const response = await axios.get('/track/progress')
    progress.value = response.data
  } catch (err) {
    console.error('Failed to load progress:', err)
    alert('Failed to fetch progress data.')
  } finally {
    loading.value = false
  }
}

const fetchDetails = async (title) => {
  try {
    const response = await axios.get(`/track/progress/${encodeURIComponent(title)}`)
    students.value = response.data.students
    selectedTitle.value = title
    showDetail.value = true
  } catch (err) {
    console.error('Failed to load detail:', err)
    alert('Failed to fetch student list.')
  }
}

const filteredStudents = computed(() => {
  return students.value.filter(s => {
    const matchesSearch = s.name.toLowerCase().includes(filter.value.toLowerCase()) ||
                          s.email.toLowerCase().includes(filter.value.toLowerCase())

    const matchesStatus =
      responseFilter.value === '' ||
      (responseFilter.value === 'responded' && s.responded) ||
      (responseFilter.value === 'not_responded' && !s.responded)

    return matchesSearch && matchesStatus
  })
})

onMounted(fetchProgress)
</script>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}
</style>
