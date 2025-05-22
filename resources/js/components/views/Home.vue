<template>
  <div class="min-h-screen flex flex-col font-sans">
    <!-- Header -->
    <header class="bg-[#1e3a5f] py-4">
      <nav class="container mx-auto flex items-center justify-between px-10">
        <div class="text-white font-bold text-xl">
          PEOConnect
        </div>
        <ul class="flex space-x-6 text-sm text-white items-center">
          <li><a href="#">About Us</a></li>
          <li><a href="#">Contact Us</a></li>
          <li>
            <a href="/login" class="bg-white text-[#1e3a5f] px-4 py-1 rounded-md font-semibold shadow-sm">Login</a>
          </li>
        </ul>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-8 py-12 text-center">
      <section class="mb-6">
        <h2 class="text-sm text-gray-500 uppercase tracking-widest mb-2">
          Welcome to PEOConnect
        </h2>
        <h1 class="text-4xl font-semibold mb-3 text-[#1e7c99] tracking-wide">
          Connecting Education with Excellence
        </h1>
        <p class="italic text-gray-600 leading-5 mb-6">
          "Education is the most powerful weapon which you can use to change the world."<br>― Nelson Mandela
        </p>
        <img src="@/assets/homepage.png" alt="Books and magnifying glass" class="mx-auto mb-8 w-[1000px]" />

        <div class="max-w-3xl mx-auto text-left">
          <h3 class="text-3xl font-semibold mb-5">
            <span class="text-[#1e7c99]">Discover.</span>
            <span class="text-[#1e3a5f]">Connect.</span>
            <span class="text-[#1e7c99]">Achieve.</span>
          </h3>
          <p class="text-gray-600 leading-7 tracking-wide mb-6">
            With PEOConnect, your educational objectives are within reach.<br>
            PEOConnect is dedicated to bridging the gap between educational goals and achievements.<br>
            Our platform provides tools and resources to support continuous improvement and excellence in education.<br>
            Find us more!
          </p>
          <button class="bg-gradient-to-r from-blue-500 to-teal-400 hover:bg-[#156a84] text-white font-medium px-6 py-3 rounded-lg shadow transition duration-300">
            About Us
          </button>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 py-10 border-t">
      <div class="container mx-auto px-8 max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr_1fr] gap-8 text-sm text-gray-600">
          <!-- First Column (Wider) -->
          <div class="text-left">
            <h4 class="font-semibold mb-2">Contact Us</h4>
            <p><strong>Email:</strong> computing@utm.my</p>
            <p><strong>Phone:</strong> +607-5532813</p>
            <p><strong>Address:</strong> 3rd Floor, Block N28a, Faculty of Computing</p>
            <!-- Optional: Social Media Icons -->
            <div class="flex space-x-4 mt-4">
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
          </div>

          <!-- Second Column -->
          <div class="text-left">
            <h4 class="font-semibold mb-2">Quick Links</h4>
            <ul class="space-y-1">
              <li><a href="#" class="hover:text-[#1e7c99]">Home</a></li>
              <li><a href="#" class="hover:text-[#1e7c99]">About Us</a></li>
              <li><a href="#" class="hover:text-[#1e7c99]">Services</a></li>
            </ul>
          </div>

          <!-- Third Column -->
          <div class="text-left">
            <h4 class="font-semibold mb-2">Quick Links</h4>
            <ul class="space-y-1">
              <li><a href="#" class="hover:text-[#1e7c99]">Contact Us</a></li>
              <li><a href="#" class="hover:text-[#1e7c99]">Privacy Policy</a></li>
              <li><a href="#" class="hover:text-[#1e7c99]">Terms of Service</a></li>
            </ul>
          </div>
        </div>

        <!-- Copyright -->
        <div class="text-center text-xs text-gray-500 mt-8">
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
</style>
