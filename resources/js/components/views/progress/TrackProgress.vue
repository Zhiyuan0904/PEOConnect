<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 p-10 overflow-y-auto">
      <h1 class="text-3xl font-bold text-[#1e3a5f] mb-6">📊 Track Survey Progress</h1>

      <div v-if="loading" class="text-center text-gray-500">Loading progress data...</div>

      <div v-else-if="progress.length === 0" class="text-center text-gray-500">
        No progress data available.
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="item in progress"
          :key="item.survey_title"
          class="bg-white shadow rounded-lg p-6"
        >
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-[#1e3a5f]">{{ item.survey_title }}</h2>
            <button
              @click="fetchDetails(item.survey_title)"
              class="text-sm bg-[#1e7c99] text-white px-4 py-1 rounded hover:bg-[#156a84]"
            >
              View Details
            </button>
          </div>
          <p class="text-gray-700">
            <strong>Responded:</strong> {{ item.responded }} / {{ item.total }}
            <span class="ml-2 text-sm text-gray-500">({{ item.percentage }}%)</span>
          </p>

          <div class="mt-4 w-full bg-gray-200 rounded-full h-5">
            <div
              class="bg-[#1e7c99] h-5 rounded-full transition-all duration-500"
              :style="{ width: item.percentage + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Details Modal -->
      <transition name="modal-fade">
        <div v-if="showDetail" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
          <div class="bg-white w-full max-w-4xl p-6 rounded-xl shadow-2xl relative overflow-y-auto max-h-[90vh]">
            <h2 class="text-2xl font-bold text-[#1e3a5f] mb-6">{{ selectedTitle }} – Respondents</h2>

            <!-- Filters -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <input
                v-model="filter"
                type="text"
                placeholder="🔍 Search by name or email..."
                class="flex-1 p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#1e7c99] focus:outline-none"
              />
              <select
                v-model="responseFilter"
                class="p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#1e7c99] focus:outline-none"
              >
                <option value="">All</option>
                <option value="responded">✅ Responded</option>
                <option value="not_responded">❌ Not Responded</option>
              </select>
            </div>

            <!-- Table -->
            <table class="w-full table-auto text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
              <thead class="bg-[#f1f5f9] text-gray-600 uppercase text-xs">
                <tr>
                  <th class="py-3 px-4">Name</th>
                  <th class="py-3 px-4">Email</th>
                  <th class="py-3 px-4">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="student in filteredStudents"
                  :key="student.email"
                  class="border-t hover:bg-gray-50 transition"
                >
                  <td class="py-3 px-4 font-medium">{{ student.name }}</td>
                  <td class="py-3 px-4">{{ student.email }}</td>
                  <td class="py-3 px-4">
                    <span
                      :class="student.responded ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold'"
                    >
                      {{ student.responded ? 'Responded' : 'Not Responded' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>

            <button
              @click="showDetail = false"
              class="absolute top-4 right-4 text-gray-600 hover:text-black text-xl font-bold"
              aria-label="Close"
            >×</button>
          </div>
        </div>
      </transition>
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
th, td {
  padding: 0.5rem;
}
</style>
