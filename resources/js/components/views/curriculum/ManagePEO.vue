<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 p-10 overflow-y-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-[#1e3a5f]">Manage PEOs 🎓</h1>
        <button
          @click="openAddModal"
          class="bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold px-5 py-3 rounded-lg transition duration-300"
        >
          + Add PEO
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center text-gray-500">Loading PEOs...</div>

      <!-- Empty state -->
      <div v-else-if="peos.length === 0" class="text-center text-gray-500">
        No PEOs found.
      </div>

      <!-- PEO List -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="peo in peos"
          :key="peo.id"
          class="bg-white rounded-xl shadow-md p-6 space-y-3"
        >
          <div>
            <h2 class="text-xl font-bold text-[#1e3a5f]">{{ peo.code }}</h2>
            <p class="text-gray-700">{{ peo.description }}</p>
          </div>
          <div class="flex justify-between mt-4">
            <button
              @click="openEditModal(peo)"
              class="bg-[#1e7c99] hover:bg-[#156a84] text-white px-4 py-2 rounded"
            >
              Edit
            </button>
            <button
              @click="deletePEO(peo.id)"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
          <h3 class="text-xl font-semibold mb-4">
            {{ editingPEO ? 'Edit PEO' : 'Add PEO' }}
          </h3>
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">PEO Code</label>
              <input
                v-model="form.code"
                type="text"
                class="mt-1 w-full border border-gray-300 rounded px-3 py-2"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="mt-1 w-full border border-gray-300 rounded px-3 py-2"
              ></textarea>
            </div>
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-[#1e7c99] hover:bg-[#156a84] text-white rounded"
              >
                {{ editingPEO ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
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
      form: {
        code: '',
        description: '',
      },
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
  },
};
</script>

<style scoped>
/* Add modal and transition styling here if needed */
</style>
