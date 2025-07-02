<template>
  <div class="flex ml-[20%] min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 p-10">
      <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
          <h1 class="text-4xl font-bold text-[#4072bc] flex items-center">
            Survey Report
          </h1>
          <div class="flex gap-4">
            <select
              v-model="selectedYear"
              @change="fetchReportSummary"
              class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#4072bc]">
              <option :value="null">All Years</option>
              <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
            </select>
            <button 
              @click="exportReport" 
              class="px-6 py-3 rounded-full bg-gradient-to-r from-[#34d399] to-[#059669] text-white font-semibold shadow hover:brightness-110 transition">
              Export Full Report
            </button>
          </div>
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

        <!-- Yearly Completion Comparison Chart -->
        <div class="bg-white rounded-2xl shadow p-8 mb-10">
          <h2 class="text-2xl font-bold text-[#4072bc] mb-6">Yearly Completion Comparison</h2>
          <canvas ref="barChartCanvas" class="w-full" height="100"></canvas>
        </div>

        <!-- Most Selected PEOs -->
        <div v-if="topPeos.length" class="bg-white rounded-2xl shadow p-8 mb-10">
          <h2 class="text-2xl font-bold text-[#4072bc] mb-6">Most Selected PEOs</h2>
          <ul class="list-disc list-inside text-gray-700 space-y-2">
            <li v-for="peo in topPeos" :key="peo.code">
              <span class="font-semibold text-[#4072bc]">{{ peo.code }}:</span>
              {{ peo.description }}
              <span class="text-sm text-gray-500">({{ peo.count }} selections)</span>
            </li>
          </ul>
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
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
} from 'chart.js'

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend)

// State
const reportData = ref([])
const totalSurveys = ref(0)
const totalResponses = ref(0)
const avgCompletion = ref(0)
const selectedYear = ref(null)
const availableYears = ref([])
const topPeos = ref([])
const barChartCanvas = ref(null)
let barChartInstance = null

const fetchAvailableYears = async () => {
  try {
    const res = await axios.get('/track/available-years')
    availableYears.value = res.data
  } catch (err) {
    console.error('Error fetching available years', err)
  }
}

const fetchReportSummary = async () => {
  try {
    const res = await axios.get('/track/progress', {
      params: { year: selectedYear.value }
    })
    reportData.value = res.data
    totalSurveys.value = res.data.length
    totalResponses.value = res.data.reduce((acc, item) => acc + item.responded, 0)
    const totalCompletion = res.data.reduce((acc, item) => acc + item.percentage, 0)
    avgCompletion.value = res.data.length ? Math.round(totalCompletion / res.data.length) : 0

    fetchTopPEOs()
  } catch (err) {
    console.error('Error fetching report summary', err)
  }
}

const fetchTopPEOs = async () => {
  try {
    const res = await axios.get('/track/top-peos', {
      params: { year: selectedYear.value }
    })
    topPeos.value = res.data
  } catch (err) {
    console.error('Error fetching top PEOs', err)
  }
}

const exportReport = async () => {
  try {
    const response = await axios.get('/reports', {
      params: { year: selectedYear.value },
      responseType: 'blob'
    })

    const yearLabel = selectedYear.value ? `_year_${selectedYear.value}` : ''
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `survey_report${yearLabel}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (err) {
    console.error('Export failed', err)
    alert('Failed to export report.')
  }
}

const renderYearlyChart = async () => {
  try {
    const res = await axios.get('/track/yearly-comparison')
    const labels = res.data.map(item => item.year)
    const data = res.data.map(item => item.avg_completion)

    if (barChartInstance) {
      barChartInstance.destroy()
    }

    barChartInstance = new Chart(barChartCanvas.value, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Avg Completion (%)',
          data,
          backgroundColor: '#3b82f6'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => `${ctx.raw}%`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            title: {
              display: true,
              text: 'Completion %'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Year'
            }
          }
        }
      }
    })
  } catch (err) {
    console.error('Error rendering chart', err)
  }
}

onMounted(() => {
  fetchAvailableYears()
  fetchReportSummary()
  renderYearlyChart()
})
</script>

<style scoped></style>
