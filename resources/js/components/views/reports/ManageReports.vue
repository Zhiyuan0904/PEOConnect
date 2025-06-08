<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 p-10">
      <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
          <h1 class="text-4xl font-bold text-[#4072bc] flex items-center">
            Survey Report
          </h1>
          <button 
            @click="exportReport" 
            class="px-6 py-3 rounded-full bg-gradient-to-r from-[#34d399] to-[#059669] text-white font-semibold shadow hover:brightness-110 transition">
            Export Full Report
          </button>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
          <div class="bg-white shadow rounded-2xl p-8 text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-3">Total Surveys</h3>
            <p class="text-4xl font-bold">{{ totalSurveys }}</p>
          </div>

          <div class="bg-white shadow rounded-2xl p-8 text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-3">Total Responses</h3>
            <p class="text-4xl font-bold">{{ totalResponses }}</p>
          </div>

          <div class="bg-white shadow rounded-2xl p-8 text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-3">Avg Completion</h3>
            <p class="text-4xl font-bold">{{ avgCompletion }}%</p>
          </div>
        </div>

        <!-- Table Preview -->
        <div class="bg-white rounded-2xl shadow p-8">
        <h2 class="text-2xl font-bold text-[#4072bc] mb-6">Survey Completion Summary</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
            <thead class="bg-[#f1f5f9] text-[#4072bc]">
                <tr>
                <th class="py-3 px-6 text-left">Survey Title</th>
                <th class="py-3 px-6 text-center">Total Students</th>
                <th class="py-3 px-6 text-center">Responded</th>
                <th class="py-3 px-6 text-center">Completion (%)</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in reportData" :key="item.survey_title" class="border-t">
                <td class="py-3 px-6 text-left">{{ item.survey_title }}</td>
                <td class="py-3 px-6 text-center">{{ item.total }}</td>
                <td class="py-3 px-6 text-center">{{ item.responded }}</td>
                <td class="py-3 px-6 text-center">{{ item.percentage }}%</td>
                </tr>
            </tbody>
            </table>
        </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '@/components/common/Sidebar.vue'
import axios from '@/axios'
import { ref, onMounted } from 'vue'

const reportData = ref([])
const totalSurveys = ref(0)
const totalResponses = ref(0)
const avgCompletion = ref(0)

const fetchReportSummary = async () => {
  try {
    const res = await axios.get('/track/progress')
    reportData.value = res.data

    totalSurveys.value = res.data.length
    totalResponses.value = res.data.reduce((acc, item) => acc + item.responded, 0)
    const totalCompletion = res.data.reduce((acc, item) => acc + item.percentage, 0)
    avgCompletion.value = res.data.length ? Math.round(totalCompletion / res.data.length) : 0

  } catch (err) {
    console.error('Error fetching report summary', err)
  }
}

// ✅ Fully fixed download function
const exportReport = async () => {
  try {
    const response = await axios.get('/reports', {
      responseType: 'blob' // Important for file downloading
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'survey_report.pdf')
    document.body.appendChild(link)
    link.click()
    link.remove()

  } catch (err) {
    console.error('Export failed', err)
    alert('Failed to export report.')
  }
}

onMounted(fetchReportSummary)
</script>

<style scoped></style>
