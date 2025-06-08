<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 p-10">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
          <h1 class="text-4xl font-bold text-[#4072bc]">Manage PEOs</h1>
          <div class="flex gap-4">
            <button @click="openAddModal"
              class="px-6 py-3 rounded-full bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md">
              + Add PEO
            </button>
            <button @click="openCsvModal"
              class="px-6 py-3 rounded-full bg-gradient-to-r from-[#34d399] to-[#059669] text-white font-semibold shadow hover:brightness-110 transition">
              Bulk Upload
            </button>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center text-gray-500 text-lg py-10 animate-pulse">
          Loading PEOs...
        </div>

        <!-- Empty state -->
        <div v-else-if="peos.length === 0" class="text-center text-gray-400 text-lg py-10">
          No PEOs found.
        </div>

        <!-- Cards -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="peo in peos" :key="peo.id"
            class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col justify-between">
            <img src="@/assets/peo.jpg" class="w-full h-40 object-cover" />
            <div class="p-6 flex-1">
              <h2 class="text-xl font-bold text-[#4072bc] mb-2">{{ peo.code }}</h2>
              <p class="text-gray-600 mb-2">{{ peo.description }}</p>
            </div>
            <div class="flex justify-around border-t px-4 py-3 bg-[#f9fafb]">
              <button @click="openEditModal(peo)"
                class="bg-[#59a8f7] text-white w-24 py-2 rounded-lg shadow">Edit</button>
              <button @click="deletePEO(peo.id)"
                class="bg-red-400 text-white w-24 py-2 rounded-lg shadow">Delete</button>
            </div>
          </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
          <div class="bg-white p-10 rounded-2xl w-full max-w-xl shadow-xl">
            <h2 class="text-2xl font-bold text-[#4072bc] mb-6">
              {{ editingPEO ? 'Edit PEO' : 'Add PEO' }}
            </h2>
            <form @submit.prevent="submitForm" class="space-y-6">
              <div>
                <label class="block mb-2 font-semibold text-[#4072bc]">PEO Code</label>
                <input v-model="form.code" type="text" class="w-full border p-3 rounded-lg" required />
              </div>
              <div>
                <label class="block mb-2 font-semibold text-[#4072bc]">Description</label>
                <textarea v-model="form.description" rows="3" class="w-full border p-3 rounded-lg"></textarea>
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" @click="closeModal" class="text-gray-600 font-semibold">Cancel</button>
                <button type="submit" class="bg-[#4072bc] text-white px-6 py-2 rounded-lg">
                  {{ editingPEO ? 'Update' : 'Create' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- CSV Upload Modal -->
        <div v-if="showCsvModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
          <div class="bg-white p-10 rounded-2xl w-full max-w-xl shadow-xl">
            <h2 class="text-2xl font-bold text-[#059669] mb-6">Bulk Upload CSV</h2>
            <input type="file" @change="handleCsvFile" accept=".csv" class="mb-6 w-full" />
            <div class="flex justify-end gap-2">
              <button type="button" @click="closeCsvModal" class="text-gray-600 font-semibold">Cancel</button>
              <button @click="uploadCsv" :disabled="csvUploading"
                class="bg-[#059669] text-white px-6 py-2 rounded-lg">
                {{ csvUploading ? 'Uploading...' : 'Upload' }}
              </button>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '@/components/common/Sidebar.vue';
import axios from '@/axios';

export default {
  name: 'ManagePEO',
  components: { Sidebar },
  data() {
    return {
      peos: [],
      loading: false,
      showModal: false,
      editingPEO: null,
      showCsvModal: false,
      csvFile: null,
      csvUploading: false,
      form: { code: '', description: '' },
    };
  },
  mounted() {
    this.fetchPEOs();
  },
  methods: {
    async fetchPEOs() {
      this.loading = true;
      try {
        const res = await axios.get('/peos');
        this.peos = res.data;
      } catch (err) {
        console.error('Error loading PEOs:', err);
        alert('Failed to fetch PEOs.');
      } finally {
        this.loading = false;
      }
    },
    openAddModal() {
      this.editingPEO = null;
      this.form = { code: '', description: '' };
      this.showModal = true;
    },
    openEditModal(peo) {
      this.editingPEO = peo;
      this.form = { code: peo.code, description: peo.description };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    async submitForm() {
      try {
        if (this.editingPEO) {
          await axios.put(`/peos/${this.editingPEO.id}`, this.form);
        } else {
          await axios.post('/peos', this.form);
        }
        this.closeModal();
        this.fetchPEOs();
      } catch (err) {
        console.error('Submit failed:', err);
        alert('Failed to save PEO.');
      }
    },
    async deletePEO(id) {
      if (!confirm('Are you sure you want to delete this PEO?')) return;
      try {
        await axios.delete(`/peos/${id}`);
        this.fetchPEOs();
      } catch (err) {
        console.error('Delete failed:', err);
        alert('Failed to delete PEO.');
      }
    },
    openCsvModal() {
      this.showCsvModal = true;
    },
    closeCsvModal() {
      this.showCsvModal = false;
    },
    handleCsvFile(e) {
      this.csvFile = e.target.files[0];
    },
    async uploadCsv() {
      if (!this.csvFile) {
        alert('Please select a CSV file.');
        return;
      }
      try {
        this.csvUploading = true;
        const formData = new FormData();
        formData.append('csv', this.csvFile);
        const response = await axios.post('/peos/bulk-upload', formData);
        alert(`CSV uploaded: ${response.data.imported} imported, ${response.data.skipped} skipped.`);
        this.fetchPEOs();
        this.closeCsvModal();
      } catch (err) {
        console.error('CSV upload failed:', err);
        alert('Failed to upload CSV.');
      } finally {
        this.csvUploading = false;
      }
    }
  }
};
</script>

<style scoped></style>
