<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 p-10 overflow-y-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-[#1e3a5f]">Manage Curriculum Content 📘</h1>
        <button
          class="bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold px-5 py-3 rounded-lg"
          @click="startForm"
        >
          + Add Curriculum Content
        </button>
      </div>

      <div v-if="loading" class="text-center text-gray-500 py-10">Loading curriculum content...</div>

      <div v-else-if="contents.length === 0" class="text-center text-gray-500 py-10">
        No curriculum content found.
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in contents"
          :key="item.id"
          class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col"
        >
          <div class="flex-1 flex flex-col p-6">
            <h2 class="text-xl font-bold text-[#1e3a5f] mb-2">{{ item.title }}</h2>
            <p class="text-gray-600 flex-1">{{ item.description }}</p>
            <p class="text-gray-400 text-sm mt-2">Linked PEOs: {{ formatPeos(item.peo_ids) }}</p>
          </div>

          <div class="flex justify-around bg-gray-50 p-4 border-t">
            <button
              class="bg-[#1e7c99] hover:bg-[#156a84] text-white px-4 py-2 rounded-lg"
              @click="openForm(item)"
            >Edit</button>
            <button
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg"
              @click="deleteContent(item.id)"
            >Delete</button>
          </div>
        </div>
      </div>

      <!-- Modal Form -->
      <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg w-full max-w-xl shadow-lg">
          <h2 class="text-2xl font-bold text-[#1e3a5f] mb-6">
            {{ step === 1 ? 'Create Curriculum Content 📝' : 'Select Related PEO(s)' }}
          </h2>

          <!-- Step 1 -->
          <div v-if="step === 1">
            <label class="block mb-4">
              <span class="text-gray-700 font-medium">Title</span>
              <input type="text" v-model="form.title" class="w-full border p-2 rounded mt-1" required />
            </label>

            <label class="block mb-4">
              <span class="text-gray-700 font-medium">Description</span>
              <textarea v-model="form.description" class="w-full border p-2 rounded mt-1" required></textarea>
            </label>

            <label class="block mb-4">
              <span class="text-gray-700 font-medium">Upload Files:</span>
              <div class="border-2 border-dashed border-gray-300 p-6 rounded-lg mt-2 text-center text-gray-500">
                <input type="file" class="hidden" ref="fileInput" multiple @change="handleFileChange" />
                <div @click="$refs.fileInput.click()" class="cursor-pointer">
                  <div class="text-4xl">&#8682;</div>
                  <p>You can drag and drop files here or click to select them.</p>
                </div>
                <ul v-if="form.files.length" class="mt-2 text-sm text-gray-600">
                  <li v-for="(file, index) in form.files" :key="index">{{ file.name }}</li>
                </ul>
              </div>
            </label>

            <div class="flex justify-end gap-2 mt-6">
              <button class="text-gray-600" @click="closeForm">Cancel</button>
              <button class="bg-[#1e7c99] hover:bg-[#156a84] text-white px-4 py-2 rounded" @click="step = 2">
                Next
              </button>
            </div>
          </div>

          <!-- Step 2 -->
          <div v-else-if="step === 2">
            <div v-if="peos.length === 0" class="text-center text-gray-500 mb-4">No PEOs available.</div>
            <div v-else class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">Select Related PEO(s):</label>
              <div class="border p-4 rounded-lg max-h-48 overflow-y-auto">
                <div v-for="peo in peos" :key="peo.id" class="mb-2">
                  <label class="inline-flex items-center">
                    <input
                      type="checkbox"
                      :value="peo.id"
                      v-model="form.peo_ids"
                      class="form-checkbox text-[#1e7c99]"
                    />
                    <span class="ml-2">{{ peo.code }} - {{ peo.description }}</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="flex justify-between gap-2">
              <button class="text-gray-600" @click="step = 1">Back</button>
              <button
                class="bg-[#1e7c99] hover:bg-[#156a84] text-white px-4 py-2 rounded"
                @click="submitForm"
                :disabled="formLoading"
              >
                {{ form.id ? 'Update' : 'Create' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/components/common/Sidebar.vue'
import axios from '@/axios'

const contents = ref([])
const peos = ref([])
const loading = ref(true)
const showForm = ref(false)
const formLoading = ref(false)
const step = ref(1)

const form = ref({
  id: null,
  title: '',
  description: '',
  peo_ids: [],
  files: [],
})

const fetchData = async () => {
  loading.value = true
  try {
    const [peoRes, contentRes] = await Promise.all([
      axios.get('/peos'),
      axios.get('/curriculum-content'),
    ])
    peos.value = peoRes.data
    contents.value = contentRes.data
  } catch (err) {
    console.error('Error loading data:', err)
    alert('Failed to load curriculum content or PEOs.')
  } finally {
    loading.value = false
  }
}

const formatPeos = (ids) => {
  return peos.value.filter(p => ids.includes(p.id)).map(p => p.code).join(', ')
}

const startForm = () => {
  form.value = { id: null, title: '', description: '', peo_ids: [], files: [] }
  step.value = 1
  showForm.value = true
}

const openForm = (item) => {
  form.value = { ...item, files: [] }
  step.value = 1
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  step.value = 1
  formLoading.value = false
}

const handleFileChange = (e) => {
  form.value.files = Array.from(e.target.files)
}

const submitForm = async () => {
  formLoading.value = true
  try {
    const payload = new FormData()
    payload.append('title', form.value.title)
    payload.append('description', form.value.description)
    form.value.peo_ids.forEach(id => payload.append('peo_ids[]', id))
    form.value.files.forEach(file => payload.append('files[]', file))

    if (form.value.id) {
      await axios.post(`/curriculum-content/${form.value.id}?_method=PUT`, payload)
    } else {
      await axios.post('/curriculum-content', payload)
    }
    await fetchData()
    closeForm()
  } catch (err) {
    console.error('Form submit error:', err)
    alert('Error saving content.')
  } finally {
    formLoading.value = false
  }
}

onMounted(fetchData)
</script>
