<template>
  <div class="min-h-screen flex flex-col font-sans bg-gradient-to-b from-[#ffffff] via-[#faf9fe] via-[#f6f4fb] to-[#f2eff9]">

    <!-- Header -->
    <header class="bg-[#ffffff] shadow-sm py-5">
      <nav class="container mx-auto px-20 flex items-center justify-between">
        <div class="font-extrabold text-3xl text-[#4072bc] animate-logo-bounce">PEOConnect</div>
        <ul class="flex space-x-6 text-sm text-[#6c6f73] items-center font-medium">
          <li><a href="#about" class="hover:text-[#f07ba3] transition">About Us</a></li>
          <li><a href="#contact" class="hover:text-[#f07ba3] transition">Contact Us</a></li>
          <li>
            <a href="/login" class="bg-gradient-to-r from-[#8475d2] to-[#a7c8f8] hover:from-[#f07ba3] to-[#c4a8e3] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md">Login</a>
          </li>
        </ul>
      </nav>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow">
      <section class="container mx-auto px-20 py-20 flex flex-col-reverse md:flex-row items-center justify-between gap-12 relative">
        <!-- Left Text -->
        <div class="md:w-1/2 text-center md:text-left z-10">
          <h2 class="text-sm uppercase tracking-widest text-[#f07ba3] font-extrabold mb-3">Welcome to PEOConnect</h2>
          <h1 class="text-6xl font-bold text-[#0c163d] leading-tight mb-6">Connecting <br />Education<br />with Excellence</h1>
          <p class="text-[#6c6f73] text-lg mb-8">Empower your learning journey with our cutting-edge tools and connected educational insights.</p>
          <a href="/sign-up" class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md">Get Started Now</a>
        </div>

        <!-- Right Image -->
        <div class="md:w-1/2 relative flex justify-center z-10 group">
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
            <div class="w-[400px] h-[400px] md:w-[500px] md:h-[500px] rounded-full blur-3xl opacity-80 bg-gradient-to-br from-[#e6c752] via-[#d98ab3] to-[#7caee6] animate-glow"></div>
          </div>
          <img src="@/assets/homepage.png" alt="Education Illustration" class="w-full max-w-md md:max-w-lg transition-transform duration-500 group-hover:scale-105 z-10"/>
        </div>
      </section>

      <!-- Features Section -->
      <section class="container mx-auto px-20 py-24 bg-white">
        <h2 class="text-4xl font-bold text-center text-[#1e3a5f] mb-20 animate-fade-in">Why Choose PEOConnect?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

          <div class="p-8 rounded-3xl shadow-lg bg-gradient-to-br from-[#f4f4f4] to-[#e5e5e5] hover:scale-105 transition duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 mb-6 rounded-full bg-gradient-to-br from-[#f07ba3] to-[#c4a8e3] flex items-center justify-center text-white text-3xl font-bold shadow-md">📊</div>
            <h3 class="text-2xl font-bold mb-4 text-[#4072bc]">Survey Management</h3>
            <p class="text-[#6c6f73]">Easily create, distribute, and monitor surveys for continuous improvement.</p>
          </div>

          <div class="p-8 rounded-3xl shadow-lg bg-gradient-to-br from-[#f4f4f4] to-[#e5e5e5] hover:scale-105 transition duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 mb-6 rounded-full bg-gradient-to-br from-[#8475d2] to-[#a7c8f8] flex items-center justify-center text-white text-3xl font-bold shadow-md">📚</div>
            <h3 class="text-2xl font-bold mb-4 text-[#4072bc]">Curriculum Mapping</h3>
            <p class="text-[#6c6f73]">Align course content with Program Educational Objectives (PEOs) effortlessly.</p>
          </div>

          <div class="p-8 rounded-3xl shadow-lg bg-gradient-to-br from-[#f4f4f4] to-[#e5e5e5] hover:scale-105 transition duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 mb-6 rounded-full bg-gradient-to-br from-[#e6c752] to-[#f07ba3] flex items-center justify-center text-white text-3xl font-bold shadow-md">📈</div>
            <h3 class="text-2xl font-bold mb-4 text-[#4072bc]">Progress Tracking</h3>
            <p class="text-[#6c6f73]">Monitor student progress and ensure program effectiveness in real-time.</p>
          </div>

          <div class="p-8 rounded-3xl shadow-lg bg-gradient-to-br from-[#f4f4f4] to-[#e5e5e5] hover:scale-105 transition duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 mb-6 rounded-full bg-gradient-to-br from-[#4072bc] to-[#1e3a5f] flex items-center justify-center text-white text-3xl font-bold shadow-md">📑</div>
            <h3 class="text-2xl font-bold mb-4 text-[#4072bc]">Data Analytics</h3>
            <p class="text-[#6c6f73]">Generate detailed reports to support data-driven decision making.</p>
          </div>

        </div>
      </section>

      <!-- Our Mission -->
      <section class="relative h-screen">
        <!-- Background Image -->
        <img src="@/assets/graduation.jpg" alt="Faculty of Computing" class="absolute inset-0 object-cover w-full h-full z-0" />

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-30 z-10"></div>

        <!-- Content -->
        <div class="relative z-20 flex flex-col items-center justify-center h-full text-center text-white px-6">
          <h1 class="text-5xl font-bold mb-4">Our Mission</h1>
          <p class="text-lg mb-10 max-w-2xl">
            PEOConnect simplifies curriculum management, delivers real-time insights on learning outcomes, and empowers student success for a brighter academic future.
          </p>
          <a href="/sign-up" class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md">Get Started Now</a>
        </div>
      </section>

      <!-- About Section -->
      <section id="about" class="py-32">
        <div class="container mx-auto px-20 flex flex-col lg:flex-row items-center gap-16">
          <div class="grid grid-cols-2 gap-4 w-full lg:w-1/2">
            <div class="rounded-3xl overflow-hidden shadow-lg col-span-2 h-[550px]">
              <img src="@/assets/fakulti-komputeran.png" alt="Faculty of Computing" class="object-cover w-full h-full">
            </div>
          </div>

          <div class="w-full lg:w-1/2 text-center lg:text-left">
            <h2 class="text-4xl font-bold text-[#1e3a5f] mb-8">About PEOConnect</h2>
            <p class="text-lg text-[#6c6f73] leading-relaxed mb-8">
              PEOConnect is a web-based platform developed by students of <span class="font-semibold text-[#4072bc]">Faculty of Computing, UTM</span>. We empower institutions to manage Program Educational Objectives (PEOs), curriculum content, and survey tracking with real-time insights.
            </p>

            <div class="flex flex-wrap justify-center lg:justify-start gap-8">
              <div class="text-center"><div class="text-3xl font-bold text-[#4072bc]">{{ modules }}</div><div class="text-sm text-[#6c6f73]">Modules</div></div>
              <div class="text-center"><div class="text-3xl font-bold text-[#4072bc]">{{ surveys }}</div><div class="text-sm text-[#6c6f73]">Surveys Managed</div></div>
              <div class="text-center"><div class="text-3xl font-bold text-[#4072bc]">{{ institutions }}</div><div class="text-sm text-[#6c6f73]">Institutions</div></div>
              <div class="text-center"><div class="text-3xl font-bold text-[#4072bc]">{{ students }}</div><div class="text-sm text-[#6c6f73]">Student Data</div></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Contact -->
      <section id="contact" class="py-32">
        <div class="container mx-auto px-8">
          <div class="max-w-5xl mx-auto bg-white/40 backdrop-blur-xl border border-white/30 rounded-3xl shadow-xl p-16 text-center">
            <h2 class="text-4xl font-bold text-[#1e3a5f] mb-12">Contact Us</h2>

            <div class="flex flex-col md:flex-row justify-around items-center gap-12">
              <div class="text-lg text-[#6c6f73] text-left space-y-6">
                <div><b class="text-[#4072bc]">Address:</b><br>Faculty of Computing, UTM, Johor Bahru, Malaysia</div>
                <div><b class="text-[#4072bc]">Email:</b> computing@utm.my</div>
                <div><b class="text-[#4072bc]">Office Hours:</b> Mon–Fri (8:00am – 5:00pm)</div>
              </div>
              <div class="space-y-8">
                <img src="@/assets/utm-logo.png" alt="UTM Logo" class="h-24 mx-auto">
                <img src="@/assets/md-logo.png" alt="MD Logo" class="h-14 mx-auto">
                <p class="italic text-sm text-[#6c6f73]">"Inspiring Digital Talents"</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-5 border-t border-[#e3e3e3] text-center text-xs text-gray-400 mt-10">
      © 2025 PEOConnect. All rights reserved.
    </footer>

    <!-- Floating CTA -->
    <div class="fixed bottom-8 right-8 z-50">
      <a href="/sign-up" class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-6 py-3 rounded-full shadow-xl transition duration-300">🚀 Get Started</a>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const modules = ref(0)
const surveys = ref(0)
const institutions = ref(0)
const students = ref(0)

function animateCounter(target, value, duration = 1500) {
  let start = 0
  const stepTime = Math.abs(Math.floor(duration / value))
  const timer = setInterval(() => {
    start += 1
    target.value = start
    if (start >= value) clearInterval(timer)
  }, stepTime)
}

onMounted(() => {
  animateCounter(modules, 10)
  animateCounter(surveys, 300)
  animateCounter(institutions, 15)
  animateCounter(students, 1500)
})
</script>


<style scoped>
body {
  font-family: 'Inter', sans-serif;
}

/* ========== Glow Animation ========== */
@keyframes glowMove {
  0%, 100% { transform: scale(1) translate(0, 0); }
  50% { transform: scale(1.05) translate(10px, -10px); }
}
.animate-glow {
  animation: glowMove 8s ease-in-out infinite;
}

/* ========== Fade In Animation ========== */
@keyframes fadeIn {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 1s ease-out forwards;
}

/* ========== Slide Up Animation (with delay variants) ========== */
@keyframes slideUp {
  0% { opacity: 0; transform: translateY(30px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-slide-up {
  opacity: 0;
  animation: slideUp 1s ease-out forwards;
}
.animate-slide-up.delay-0  { animation-delay: 0s; }
.animate-slide-up.delay-100 { animation-delay: 0.2s; }
.animate-slide-up.delay-200 { animation-delay: 0.4s; }
.animate-slide-up.delay-300 { animation-delay: 0.6s; }

/* ========== Gradient Movement (Background floating) ========== */
@keyframes gradientMove {
  0% { transform: translate(0%, 0%); }
  50% { transform: translate(-10%, -10%); }
  100% { transform: translate(0%, 0%); }
}
.animate-gradient-move {
  animation: gradientMove 20s ease-in-out infinite;
}

/* ========== Logo Bounce Animation ========== */
@keyframes logoBounce {
  0% { transform: translateY(-10px); }
  50% { transform: translateY(5px); }
  100% { transform: translateY(0); }
}
.animate-logo-bounce {
  animation: logoBounce 1.5s ease;
}
</style>

