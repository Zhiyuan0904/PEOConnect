<template>
  <div class="dashboard-container ml-[20%] flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />

    <main class="flex-1 p-8">
      <header class="flex justify-between items-center mb-10">
        <h1 class="text-4xl font-bold text-[#4072bc]">Dashboard</h1>
        <div class="text-sm text-[#6c6f73]">
          <div v-if="authStore.user">
         Logged in as <strong>{{ authStore.user?.name || 'Guest' }}</strong> ({{ capitalize(authStore.user?.role || 'Unknown') }})
          </div>
        </div>
      </header>

      <!-- Admin / Quality Team / Dean Dashboard -->
      <template v-if="['admin', 'quality team', 'dean'].includes(role)">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-10">
          <SummaryCard title="Total Surveys" :value="activeSurveys" icon="📝" color="primary" />
          <SummaryCard title="Respondents" :value="totalRespondents" icon="👥" color="secondary" />
          <SummaryCard title="Non-Respondents" :value="totalNonRespondents" icon="⏳" color="danger" />
          <SummaryCard title="Response Rate" :value="responseRate + '%'" icon="📈" color="success" />
        </div>

        <!-- Survey Progress -->
        <div class="bg-white p-6 rounded-2xl shadow-lg mb-10">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-[#4072bc]">Survey Response Progress</h3>
            <div class="flex gap-3">
              <select v-model="selectedSurvey" class="border border-gray-300 px-3 py-2 rounded-full text-sm">
                <option value="">All Surveys</option>
                <option v-for="s in surveyProgress" :key="s.survey_title" :value="s.survey_title">
                  {{ s.survey_title }}
                </option>
              </select>
              <button @click="fetchSurveyProgress" class="flex items-center gap-1 px-4 py-2 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md text-center">
                <span class="text-lg">🗘</span>
                <span class="text-sm font-medium">Refresh</span>
              </button>
            </div>
          </div>

          <div v-if="loadingProgress" class="text-center text-gray-400 py-10">Loading progress data...</div>

          <div v-else class="space-y-6">
            <div v-for="item in filteredSurveyProgress" :key="item.survey_title" class="mb-4">
              <p class="mb-1 text-sm text-[#6c6f73]">
                {{ item.survey_title }}
                <span class="ml-2 text-xs text-gray-400">({{ item.percentage }}% Completed)</span>
              </p>
              <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-gradient-to-r from-[#59a8f7] to-[#a7c8f8] h-4 rounded-full transition-all duration-500"
                  :style="{ width: item.percentage + '%' }">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Latest Submissions -->
        <!----<div class="bg-white p-6 rounded-2xl shadow-lg">
          <h3 class="text-xl font-bold text-[#4072bc] mb-4">Latest Submissions</h3>
          <table class="w-full text-[#6c6f73] text-sm">
            <thead>
              <tr>
                <th class="text-left py-2">User</th>
                <th class="text-left py-2">Survey</th>
                <th class="text-left py-2">Submitted At</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="submission in latestSubmissions" :key="submission.id">
                <td class="py-2">{{ submission.user }}</td>
                <td class="py-2">{{ submission.survey_title }}</td>
                <td class="py-2">{{ submission.submitted_at }}</td>
              </tr>
            </tbody>
          </table>
        </div> !-->
      </template>

      <!-- Lecturer Dashboard -->
      <template v-else-if="role === 'lecturer'">
        <div class="grid grid-cols-1 gap-6">

          <!-- Welcome Message -->
          <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
            <h2 class="text-2xl font-bold text-[#4072bc] mb-4">Welcome Lecturer 👨‍🏫</h2>
            <p class="text-gray-600">You can manage curriculum content and view assigned PEOs.</p>
          </div>

          <!-- Task Quick Access -->
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <!-- Quick Links -->
            <div class="bg-white p-6 rounded-2xl shadow-md">
              <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Quick Access</h3>
              <ul class="text-sm text-[#1f2937] space-y-2 list-disc list-inside">
                <li><a @click="$router.push('/manage-curriculum-content')" class="hover:text-[#4072bc] cursor-pointer">Manage Curriculum</a></li>
                <li><a @click="$router.push('/manage/PEOs')" class="hover:text-[#4072bc] cursor-pointer">View Assigned PEOs</a></li>
                <li><a @click="$router.push('/update/profile')" class="hover:text-[#4072bc] cursor-pointer">Update Profile</a></li>
              </ul>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Lecturer Notices</h3>
            <div class="scroll-announcement">
              <ul class="text-sm text-[#6c6f73] space-y-2 text-left">
                  <li>New curriculum submission window opens 1 August.</li>
                  <li>Ensure PEOs are linked for all new content.</li>
                  <li>Deadline for content updates is 30 July.</li>
                  <li>Workshops for curriculum mapping begin 10 July.</li>
                  <li>Refer to PEO Mapping Guide before submission.</li>
              </ul>
            </div>
          </div>

            <!-- Download Resources -->
            <div class="bg-white p-6 rounded-2xl shadow-md">
              <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Download Resources</h3>
              <ul class="text-sm text-[#4072bc] underline list-disc ml-5">
                <li><a href="https://fest.utm.my/educationpg/staff-resources/download-forms-academic/">Curriculum Template 2025</a></li>
                <li><a href="https://fke.utm.my/programme-objectives-skee/">PEO Mapping Guide</a></li>
              </ul>
            </div>

          </div>

          <!-- Tips / Message -->
          <div class="bg-white p-6 rounded-2xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">💡 Tip</h3>
            <p class="text-sm text-[#6c6f73]">Keep your curriculum content updated to reflect current industry needs.</p>
          </div>
        </div>
      </template>


      <!-- Student Dashboard -->
      <template v-else-if="role === 'student'">
        <div class="grid grid-cols-1 gap-6">

          <!-- ✅ Welcome Message -->
          <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
            <h2 class="text-2xl font-bold text-[#4072bc] mb-4">Welcome Student 🎓</h2>
            <p class="text-gray-600">Please respond to your assigned surveys from the sidebar.</p>
          </div>

          <!-- ⏰ Countdown to Survey Deadline -->
          <div class="bg-white p-6 rounded-2xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Survey Deadline Countdown</h3>
            <p class="text-sm text-[#6c6f73] mb-2">
              You have <strong>{{ twoDigits(days) }} days {{ twoDigits(hours) }} hours</strong> left to complete your active surveys.
            </p>
            <div class="text-3xl font-mono font-bold text-[#f07ba3]">
              {{ twoDigits(days) }}d : {{ twoDigits(hours) }}:{{ twoDigits(minutes) }}:{{ twoDigits(seconds) }}
            </div>
          </div>

          <!-- 📌 Quick Access Links -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 📌 Quick Access Links -->
            <div class="bg-white p-6 rounded-2xl shadow-md">
              <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Quick Access</h3>
              <ul class="text-sm text-[#1f2937] space-y-2 list-disc list-inside">
                <li><a @click="$router.push('/respond/surveys')" class="hover:text-[#4072bc] cursor-pointer">Respond to Surveys</a></li>
                <li><a @click="$router.push('/update/profile')" class="hover:text-[#4072bc] cursor-pointer">Update Profile</a></li>
                <li><a @click="$router.push('/view/history')" class="hover:text-[#4072bc] cursor-pointer">View Survey History</a></li>
              </ul>
            </div>

            <!-- 📰 Announcements or News -->
            <div class="bg-white p-6 rounded-2xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">News & Announcements</h3>
            <div class="scroll-announcement">
              <ul class="text-sm text-[#6c6f73] space-y-2 text-left">
                <li>System maintenance scheduled on 1 July.</li>
                  <li>New feature: Survey history available soon on 31 July.</li>
                  <li>Reminder: Course Feedback Survey closes on 30 June.</li>
                  <li>Semester 1 survey results will be published in August.</li>
                  <li>New UI update rolling out next month.</li>
                  <li>Alumni module enhancements coming in Q4.</li>
                  <li>Check your email for important survey invitations.</li>
              </ul>
            </div>
          </div>

            <!-- 📥 Downloadable Resources -->
            <div class="bg-white p-6 rounded-2xl shadow-md">
              <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Download Resources</h3>
              <ul class="text-sm text-[#4072bc] underline list-disc ml-5">
                <li><a href="https://amd.utm.my/amacs/wp-content/uploads/sites/472/2024/04/ACADEMIC-CALENDAR-DEGREE-AND-POSTGRADUATE-2024_2025.pdf">Academic Calendar 2025</a></li>
              </ul>
            </div>
          </div>
        </div>
      </template>

      <!-- Alumni Dashboard -->
      <template v-else-if="role === 'alumni'">
        <div class="grid grid-cols-1 gap-6">

          <!-- Welcome Message -->
          <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
            <h2 class="text-2xl font-bold text-[#4072bc] mb-4">Welcome Alumni 🎓</h2>
            <p class="text-gray-600">Thank you for participating in the graduate tracer survey!</p>
          </div>

          <!-- Survey Deadline Countdown -->
          <div class="bg-white p-6 rounded-2xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Survey Deadline Countdown</h3>
            <p class="text-sm text-[#6c6f73] mb-2">
              You have <strong>{{ twoDigits(days) }} days {{ twoDigits(hours) }} hours</strong> left to complete your surveys.
            </p>
            <div class="text-3xl font-mono font-bold text-[#f07ba3]">
              {{ twoDigits(days) }}d : {{ twoDigits(hours) }}:{{ twoDigits(minutes) }}:{{ twoDigits(seconds) }}
            </div>
          </div>

          <!-- Grid: Quick Access | News | Downloads -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Upcoming Deadlines -->
          <div class="bg-white p-6 rounded-2xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Upcoming Deadlines</h3>
            <ul class="text-sm text-[#6c6f73] list-disc ml-5 text-left">
              <li>Tracer Survey – Due: 27 July</li>
              <li>Alumni Feedback – Due: 15 July</li>
            </ul>
          </div>

          <!-- News & Announcements -->
          <div class="bg-white p-6 rounded-2xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">News & Announcements</h3>
            <div class="scroll-announcement">
              <ul class="text-sm text-[#6c6f73] space-y-2 text-left">
                <li>Reminder: Course Feedback Survey closes on 30 June.</li>
                <li>Semester 1 survey results will be published in August.</li>
                <li>New UI update rolling out next month.</li>
                <li>Alumni module enhancements coming in Q4.</li>
                <li>Check your email for important survey.</li>
              </ul>
            </div>
          </div>




            <!-- Downloadable Resources -->
            <div class="bg-white p-6 rounded-2xl shadow-md text-center">
              <h3 class="text-lg font-semibold text-[#4072bc] mb-2">Download Resources</h3>
              <ul class="text-sm text-[#4072bc] underline list-disc ml-5 text-left">
                <li><a href="https://registrar.utm.my/bsm/career-utm/">UTM Career</a></li>
              </ul>
            </div>
          </div>

          <!-- Tips / Reminder -->
          <div class="bg-white p-6 rounded-2xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-[#4072bc] mb-2">💡 Your Voice Matters</h3>
            <p class="text-sm text-[#6c6f73]">Your feedback helps improve the experience for future students. Thank you for contributing!</p>
          </div>
          
        </div>
      </template>




      <!-- Fallback -->
      <template v-else>
        <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
          <h2 class="text-2xl font-bold text-red-500 mb-4">Unknown Role</h2>
          <p class="text-gray-600">Your role is not assigned correctly. Please contact admin.</p>
        </div>
      </template>

    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Sidebar from '@/components/common/Sidebar.vue';
import SummaryCard from '@/components/common/SummaryCard.vue';
import axios from '@/axios';

const router = useRouter();
const authStore = useAuthStore();
const role = authStore.user?.role;

const surveyProgress = ref([]);
const latestSubmissions = ref([]);
const loadingProgress = ref(true);
const selectedSurvey = ref('');

const days = ref(0);
const hours = ref(0);
const minutes = ref(0);
const seconds = ref(0);

const deadline = new Date('2025-07-27T23:59:59');

// Format helper
const twoDigits = (n) => n.toString().padStart(2, '0');

// Timer logic
const updateTimer = () => {
  const now = new Date();
  let diff = deadline - now;

  if (diff <= 0) {
    days.value = hours.value = minutes.value = seconds.value = 0;
    return;
  }

  days.value = Math.floor(diff / (1000 * 60 * 60 * 24));
  diff %= (1000 * 60 * 60 * 24);
  hours.value = Math.floor(diff / (1000 * 60 * 60));
  diff %= (1000 * 60 * 60);
  minutes.value = Math.floor(diff / (1000 * 60));
  diff %= (1000 * 60);
  seconds.value = Math.floor(diff / 1000);
};

// Survey fetch
const fetchSurveyProgress = async () => {
  loadingProgress.value = true;
  try {
    const res = await axios.get('/track/progress');
    surveyProgress.value = res.data;
    const res2 = await axios.get('/track/latest-submissions');
    latestSubmissions.value = res2.data;
  } catch (error) {
    console.error('Failed to load dashboard data', error);
  } finally {
    loadingProgress.value = false;
  }
};

// Computed stats
const activeSurveys = computed(() => surveyProgress.value.length);
const totalRespondents = computed(() => 
  surveyProgress.value.filter(user => user.responded).length
);
const totalExpected = computed(() => surveyProgress.value.length);
const totalNonRespondents = computed(() => totalExpected.value - totalRespondents.value);
const responseRate = computed(() => totalExpected.value > 0 ? ((totalRespondents.value / totalExpected.value) * 100).toFixed(1) : 0);

const filteredSurveyProgress = computed(() => {
  if (!selectedSurvey.value) return surveyProgress.value;
  return surveyProgress.value.filter(s => s.survey_title === selectedSurvey.value);
});

const capitalize = (text) => {
  return text
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

// Combined onMounted
onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }

  if (['admin', 'quality team', 'dean'].includes(role)) {
    fetchSurveyProgress();
  }

  updateTimer();                   // initialize countdown
  setInterval(updateTimer, 1000); // update every second
});
</script>

<style scoped>
.dashboard-container { font-family: 'Inter', sans-serif; }

@keyframes scrollAnnouncements {
  0%   { transform: translateY(0%); }
  100% { transform: translateY(-50%); }
}

.announcement-scroll {
  position: relative;
  height: 12rem; /* same as h-48 */
  overflow: hidden;
}

.announcement-list {
  animation: scrollAnnouncements 20s linear infinite;
}

</style>
