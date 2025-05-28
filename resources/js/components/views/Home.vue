<template>
  <div class="min-h-screen flex flex-col bg-[#dcd0f7] font-sans">
    <!-- Header -->
    <header class="bg-[#f4f4f4] shadow-sm py-5">
      <nav class="container mx-auto px-20 flex items-center justify-between">
        <div class="font-extrabold text-3xl text-[#4072bc]">PEOConnect</div>
        <ul class="flex space-x-6 text-sm text-[#6c6f73] items-center font-medium">
          <li><a href="#" class="hover:text-[#f07ba3] transition">About Us</a></li>
          <li><a href="#" class="hover:text-[#f07ba3] transition">Contact Us</a></li>
          <li>
            <a href="/login" class="bg-gradient-to-r 
                  from-[#8475d2] to-[#a7c8f8] 
                  hover:from-[#f07ba3] to-[#c4a8e3] 
                  text-white font-semibold px-7 py-3 rounded-full 
                  transition duration-300 shadow-md">
              Login
            </a>
          </li>
        </ul>
      </nav>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow bg-[#f4f4f4]">
      <section class="container mx-auto px-20 py-20 flex flex-col-reverse md:flex-row items-center justify-between gap-12 relative">
        <!-- Left Text -->
        <div class="md:w-1/2 text-center md:text-left z-10">
          <h2 class="text-sm uppercase tracking-widest text-[#f07ba3] font-extrabold mb-3">
            Welcome to PEOConnect
          </h2>
          <h1 class="text-6xl font-bold text-[#0c163d] leading-tight mb-6">
            Connecting <br />
            Education<br />
            with Excellence
          </h1>
          <p class="text-[#6c6f73] text-lg mb-8">
            Empower your learning journey with our cutting-edge <br />
            tools and connected educational insights.
          </p>
          <button class="bg-gradient-to-r 
                  from-[#f07ba3] to-[#c4a8e3]
                  hover:from-[#8475d2] hover:to-[#a7c8f8] 
                  text-white font-semibold px-7 py-3 rounded-full 
                  transition duration-300 shadow-md"
          >
            Get Started Now
          </button>
        </div>

        <!-- Right Image with Animated Glow and Hover Zoom -->
        <div class="md:w-1/2 relative flex justify-center z-10 group">
          <!-- Glow Background -->
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
            <div class="w-[400px] h-[400px] md:w-[500px] md:h-[500px] rounded-full blur-3xl opacity-80 
                        bg-gradient-to-br from-[#e6c752] via-[#d98ab3] to-[#7caee6] 
                        animate-glow"></div>
          </div>

          <!-- Image with Hover Zoom -->
          <img
            src="@/assets/homepage.png"
            alt="Education Illustration"
            class="w-full max-w-md md:max-w-lg transition-transform duration-500 group-hover:scale-105 z-10"
          />
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-12 border-t border-[#e3e3e3]">
      <div class="container mx-auto px-20 max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr_1fr] gap-10 text-sm text-[#6c6f73]">
          <!-- Footer Content remains unchanged -->
          ...
        </div>

        <div class="text-center text-xs text-gray-400 mt-10">
          © 2025 PEOConnect. All rights reserved.
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup>
</script>

<style scoped>
body {
  font-family: 'Inter', sans-serif;
}

@keyframes glowMove {
  0%, 100% {
    transform: scale(1) translate(0, 0);
  }
  50% {
    transform: scale(1.05) translate(10px, -10px);
  }
}

.animate-glow {
  animation: glowMove 8s ease-in-out infinite;
}

</style>
