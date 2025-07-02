<template>
  <div class="flex ml-[20%] min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 p-10">
      <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-10">
          <h1 class="text-4xl font-bold text-[#4072bc]">Manage Curriculum Content</h1>
          <button @click="startForm"
                  class="px-6 py-3 rounded-full bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold shadow-md">
            + Add Curriculum Content
          </button>
        </div>

        <div v-if="loading" class="text-center text-gray-500 text-lg py-10 animate-pulse">
          Loading curriculum content...
        </div>
        <div v-else-if="contents.length === 0" class="text-center text-gray-400 text-lg py-10">
          No curriculum content found.
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="item in contents" :key="item.id"
               class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col justify-between">
            <img src="@/assets/curriculum.png" class="w-full h-40 object-cover" />
            <div class="p-6 flex-1">
              <h2 class="text-xl font-bold text-[#4072bc] mb-2 truncate">{{ item.title }}</h2>
              <p class="text-gray-600 mb-2 break-words">{{ item.description }}</p>
            </div>
            <div class="flex justify-around border-t px-4 py-3 bg-[#f9fafb]">
              <button @click="openForm(item)" class="bg-[#59a8f7] text-white w-24 py-2 rounded-lg shadow">
                Edit
              </button>
              <button @click="deleteContent(item.id)" class="bg-red-400 text-white w-24 py-2 rounded-lg shadow">
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Form -->
        <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
          <div class="bg-white p-10 rounded-2xl w-full max-w-xl shadow-xl">
            <h2 class="text-2xl font-bold text-[#4072bc] mb-6">
              {{ step === 1
                  ? (form.id ? 'Edit Curriculum Content' : 'Create Curriculum Content')
                  : 'Select Related PEO(s)' }}
            </h2>

            <div v-if="step === 1">
              <label class="block mb-1 text-[#4072bc] font-semibold">
                Title <span class="text-red-500">*</span>
              </label>
              <input type="text" v-model="form.title"
                     class="w-full border p-3 rounded-lg mb-4" />

              <label class="block mb-1 text-[#4072bc] font-semibold">
                Description <span class="text-red-500">*</span>
              </label>
              <textarea v-model="form.description"
                        class="w-full border p-3 rounded-lg mb-4" rows="4"></textarea>

              <div v-if="form.id && form.existing_files.length > 0" class="mb-4 mt-4">
                <p class="font-semibold text-[#4072bc]">Uploaded Files:</p>
                <ul class="list-disc pl-5 text-sm mt-1 text-[#4072bc]">
                  <li v-for="(file, index) in form.existing_files" :key="index">
                    <a :href="file" target="_blank" download class="underline">
                      Download File {{ index + 1 }}
                    </a>
                  </li>
                </ul>
              </div>

              <label class="block mb-4 text-[#4072bc] font-semibold">Upload New Files</label>
              <FilePond ref="pond" name="file" :allowMultiple="true" :maxFiles="20"
                        :maxFileSize="'8000MB'" :acceptedFileTypes="acceptedTypes"
                        label-idle='Drag & Drop files or <span class="filepond--label-action">Browse</span>'
                        @updatefiles="handleFilePond" />

              <div class="flex justify-between gap-2 mt-6">
                <button @click="closeForm"
                        class="w-32 py-3 rounded-full bg-gray-300 text-gray-800 font-semibold shadow-lg hover:brightness-110">
                  Cancel
                </button>
                <button @click="submitForm"
                        class="w-32 py-3 bg-gradient-to-r from-[#4072bc] to-[#59a8f7] text-white font-semibold rounded-full shadow-lg hover:brightness-110">
                  Next
                </button>
              </div>
            </div>

            <div v-else>
              <label class="block mb-1 text-[#4072bc] font-semibold">
                Select Related PEO(s): <span class="text-red-500">*</span>
              </label>
              <div class="border p-4 rounded-lg max-h-60 overflow-y-auto mb-6">
                <div v-for="peo in peos" :key="peo.id" class="mb-2">
                  <label class="inline-flex items-center">
                    <input type="checkbox" :value="peo.id" v-model="form.peo_ids"
                           class="text-[#4072bc]" />
                    <span class="ml-2 font-medium">{{ peo.code }} - {{ peo.description }}</span>
                  </label>
                </div>
              </div>

              <div class="flex justify-between">
                <button @click="step = 1"
                        class="w-32 py-3 rounded-full bg-gray-300 text-gray-800 font-semibold shadow-lg hover:brightness-110">
                  Back
                </button>
                <button @click="submitForm"
                        :disabled="formLoading"
                        class="w-32 py-3 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] text-white font-semibold rounded-full shadow-lg hover:brightness-110">
                  {{ form.id ? 'Update' : 'Create' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import Sidebar from '@/components/common/Sidebar.vue'
import axios from '@/axios'
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize
)
const pond = ref(null)

const acceptedTypes = [
  'application/pdf',
  'image/*',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]

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
  existing_files: []
})

const fetchData = async () => {
  loading.value = true
  try {
    const [peoRes, contentRes] = await Promise.all([
      axios.get('/peos'),
      axios.get('/curriculum-content')
    ])
    peos.value = peoRes.data
    contents.value = contentRes.data
  } catch {
    alert('Failed to load curriculum content or PEOs.')
  } finally {
    loading.value = false
  }
}

const startForm = () => {
  form.value = {
    id: null,
    title: '',
    description: '',
    peo_ids: [],
    files: [],
    existing_files: []
  }
  step.value = 1
  showForm.value = true
  nextTick(() => pond.value?.removeFiles())
}

const openForm = item => {
  form.value = {
    id: item.id,
    title: item.title,
    description: item.description,
    peo_ids: item.peo_ids,
    files: [],
    existing_files: item.files || []
  }
  step.value = 1
  showForm.value = true
  nextTick(() => pond.value?.removeFiles())
}

const closeForm = () => {
  showForm.value = false
  step.value = 1
  formLoading.value = false
}

const handleFilePond = fileItems => {
  form.value.files = fileItems.map(fi => fi.file)
}

const submitForm = async () => {
  // Step 1 validation
  if (step.value === 1) {
    if (!form.value.title.trim()) {
      return alert('Title is required.')
    }
    if (!form.value.description.trim()) {
      return alert('Description is required.')
    }
    step.value = 2
    return
  }

  // Step 2 validation
  if (form.value.peo_ids.length === 0) {
    return alert('Please select at least one PEO.')
  }

  formLoading.value = true
  try {
    const payload = new FormData()
    payload.append('title', form.value.title)
    payload.append('description', form.value.description)
    form.value.peo_ids.forEach(id => payload.append('peo_ids[]', id))
    form.value.files.forEach(file => payload.append('files[]', file))

    if (form.value.id) {
      await axios.post(`/curriculum-content/${form.value.id}?_method=PUT`, payload)
      alert('Curriculum content successfully updated!')
    } else {
      await axios.post('/curriculum-content', payload)
      alert('Curriculum content successfully created!')
    }

    await fetchData()
    closeForm()
  } catch {
    alert('Error saving content.')
  } finally {
    formLoading.value = false
  }
}

const deleteContent = async id => {
  if (!confirm('Are you sure you want to delete this content?')) return
  try {
    await axios.delete(`/curriculum-content/${id}`)
    await fetchData()
  } catch {
    alert('Error deleting content.')
  }
}

onMounted(fetchData)
</script>

<style>
@import "filepond/dist/filepond.min.css";
</style>
