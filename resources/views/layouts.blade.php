<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SISTEM Monitoring Tanah</title>

  <!-- Optimized CDN usage -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome CDN with version pinning -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Alpine.js for lightweight interactivity -->
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.0/cdn.min.js" integrity="sha512-Y/8PQihZFQVR02Q9/JpuYltFV5R2Pg5nr6Lf0jLpP6uGzGj/9yxPm/tjAYXePFzwYlXLR+Vv++qs3yMVtyKSbg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Tailwind CSS with better caching -->
  <script src="https://cdn.tailwindcss.com?v=3.3.3"></script>

  <!-- AOS animations with integrity checks -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" integrity="sha512-1cK78a1o+ht2JcaW6g8OXYwqpev9+6GqOkz9xmBN9iUUhIndKtxwILGWYOSibOKjLsEdjyjZvYDq/cZwNeak0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@stack('css')
  <!-- Theme Configuration -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#00aa13',
              50: '#e6f7e8',
              100: '#ccf0d1',
              200: '#99e0a3',
              300: '#66d175',
              400: '#33c147',
              500: '#00aa13',
              600: '#00880f',
              700: '#00660b',
              800: '#004408',
              900: '#002204',
            },
            secondary: {
              DEFAULT: '#285943',
              50: '#e9efec',
              100: '#d3dfd9',
              200: '#a7bfb3',
              300: '#7b9f8d',
              400: '#4f7f67',
              500: '#285943',
              600: '#204736',
              700: '#183528',
              800: '#10241b',
              900: '#08120d',
            },
            accent: {
              DEFAULT: '#00C2CB',
              50: '#e6f9fa',
              100: '#ccf2f5',
              200: '#99e6eb',
              300: '#66d9e1',
              400: '#33cdd6',
              500: '#00C2CB',
              600: '#009ba3',
              700: '#00747a',
              800: '#004d52',
              900: '#002629',
            },
          },
          fontFamily: {
            sans: ['Poppins', 'sans-serif'],
            serif: ['Playfair Display', 'serif'],
          },
          boxShadow: {
            'card': '0 4px 20px rgba(0, 0, 0, 0.08)',
            'feature': '0 8px 30px rgba(0, 170, 19, 0.15)',
            'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1)',
          },
        }
      }
    }
  </script>

  <!-- Custom Styles -->
  <style>
    /* Better Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
      background: #00aa13;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #285943;
    }

    /* Smooth scrolling */
    html {
      scroll-behavior: smooth;
    }

    /* Animation Classes */
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .animate-float {
      animation: float 3s ease-in-out infinite;
    }

    @keyframes pulse-glow {
      0% { box-shadow: 0 0 8px rgba(0, 194, 203, 0.3); }
      50% { box-shadow: 0 0 16px rgba(0, 194, 203, 0.6); }
      100% { box-shadow: 0 0 8px rgba(0, 194, 203, 0.3); }
    }

    .pulse-glow {
      animation: pulse-glow 2s infinite;
    }

    /* Skip to content - accessibility */
    .skip-link {
      position: absolute;
      top: -40px;
      left: 0;
      background: #00aa13;
      color: white;
      padding: 8px;
      z-index: 100;
      transition: top 0.3s;
    }

    .skip-link:focus {
      top: 0;
    }

    /* Better focus styles */
    a:focus, button:focus {
      outline: 2px solid #00aa13;
      outline-offset: 2px;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col"
      x-data="{mobileMenuOpen: false, chatOpen: false}">

  <!-- Accessibility skip link -->
  <a href="#main-content" class="skip-link">Skip to content</a>

  <!-- Enhanced Navigation -->
  <header class="fixed top-0 left-0 w-full z-50 bg-white/95 backdrop-blur-md shadow-sm transition-all duration-300">
    <nav class="container mx-auto px-4 py-2">
      <div class="flex justify-between items-center h-14">
        <!-- Logo Section Kecil & Judul Minimalis -->
        <a href="/" class="flex items-center gap-2 group focus:outline-none focus:ring-2 focus:ring-primary/50 rounded-lg p-1">
          <img src="/images/LOGO PT. JBG.png" alt="Logo PT. JBG" class="h-10 w-14 object-contain" />
          <div class="font-bold leading-tight">
            <span class="text-gray-800 block text-base">SISTEM MONITORING</span>
            <span class="text-primary font-serif text-xs tracking-wide">TANAH</span>
          </div>
        </a>
        <div class="hidden md:flex items-center space-x-6">
          <a href="/monitoring" class="nav-link relative px-2 py-1 font-medium text-gray-700 hover:text-primary transition-all duration-300">
            Monitoring
          </a>
        </div>
      </div>
    </nav>
  </header>

  <!-- App-style Bottom Navigation for Mobile -->
  {{-- <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg py-2 px-3 z-40">
    <div class="flex justify-around items-center">
      <!-- <a href="/" class="flex flex-col items-center space-y-1 text-primary transition-colors">
        <div class="w-10 h-10 rounded-full bg-primary-50 flex items-center justify-center">
          <i class="fas fa-home text-primary"></i>
        </div>
        <span class="text-xs text-gray-700 font-medium">Beranda</span>
      </a> -->
      <a href="/monitoring" class="flex flex-col items-center space-y-1 text-gray-500 hover:text-primary transition-colors">
        <div class="w-10 h-10 rounded-full bg-gray-100 hover:bg-primary-50 flex items-center justify-center">
          <i class="fas fa-chart-line"></i>
        </div>
        <span class="text-xs text-gray-700 font-medium">Monitor</span>
      </a>
<!-- <a href="/contact" class="flex flex-col items-center space-y-1 text-gray-500 hover:text-primary transition-colors relative group">
  <!-- Icon bulat -->
  <div class="w-10 h-10 rounded-full bg-gray-100 group-hover:bg-primary-100 flex items-center justify-center relative">
    <i class="fas fa-envelope text-lg"></i>

    <!-- Badge notifikasi -->
    <!--<span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-md animate-pulse">-->
    <!--  1-->
    <!--</span>-->
  </div>

  <!-- Label teks -->
  <span class="text-xs text-gray-700 font-medium">Kontak</span>
</a>

      <a href="/about" class="flex flex-col items-center space-y-1 text-gray-500 hover:text-primary transition-colors">
        <div class="w-10 h-10 rounded-full bg-gray-100 hover:bg-primary-50 flex items-center justify-center">
          <i class="fas fa-info-circle"></i>
        </div>
        <span class="text-xs text-gray-700 font-medium">Tentang</span>
      </a> -->
    </div>
  </div> --}}

<div
  x-data="{ chatOpen: false }"
  x-cloak
  class="chat-widget"
>
  {{-- <!-- Chat Toggle Button -->
  <div class="fixed right-7 bottom-20 md:bottom-6 z-50">
    <button
      @click="chatOpen = true"
      x-show="!chatOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      class="bg-primary text-white p-4 rounded-full shadow-lg hover:bg-primary-600 transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2"
      aria-label="Open chat"
    >
      <i class="fas fa-comments text-xl"></i>
    </button>
  </div>

  <!-- Chat Window -->
  <div
    x-show="chatOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed right-6 bottom-20 md:bottom-6 w-80 md:w-96 z-50 bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col"
    style="height: 480px;"
  >
    <!-- Chat Header -->
    <div class="bg-gradient-to-r from-primary to-secondary p-4 flex items-center">
      <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center mr-3 shadow-md">
        <i class="fas fa-robot text-primary text-lg"></i>
      </div>
      <div class="text-white">
        <h3 class="font-bold text-lg">AquaBot</h3>
        <p class="text-xs text-primary-100">Online & siap membantu</p>
      </div>
      <button
        @click="chatOpen = false"
        class="ml-auto text-white hover:text-accent bg-white/20 w-8 h-8 rounded-full flex items-center justify-center"
        aria-label="Close chat"
      >
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Messages Area -->
    <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50" id="chatMessages">
      <!-- Welcome Message -->
      <div class="flex items-start space-x-2 animate-fade-in">
        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-robot text-primary"></i>
        </div>
        <div class="bg-white rounded-lg rounded-tl-none p-3 max-w-[80%] shadow-sm">
          <p class="text-sm">Halo! Saya AquaBot, asisten virtual untuk Smart Aquaponik Poliban. Ada yang bisa saya bantu terkait sistem aquaponik kami?</p>
        </div>
      </div>

      <div class="flex items-start space-x-2 animate-fade-in">
        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-robot text-primary"></i>
        </div>
        <div class="bg-white rounded-lg rounded-tl-none p-3 max-w-[80%] shadow-sm">
          <p class="text-sm">Saya dapat membantu Anda dengan informasi monitoring, data sensor, atau cara kerja sistem aquaponik kami.</p>
        </div>
      </div>
    </div>

    <!-- Typing indicator (hidden by default) -->
    <div class="hidden px-4 py-2" id="typingIndicator">
      <div class="flex items-center space-x-2">
        <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-robot text-primary text-xs"></i>
        </div>
        <div class="flex space-x-1">
          <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce"></div>
          <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.2s"></div>
          <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div class="p-3 border-t border-gray-200 bg-white">
      <form id="chatForm" class="flex space-x-2">
        <input
          type="text"
          id="chatInput"
          placeholder="Ketik pesan..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
          autocomplete="off"
        />
        <button
          type="submit"
          class="bg-primary text-white p-3 rounded-xl hover:bg-primary-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2"
          aria-label="Send message"
        >
          <i class="fas fa-paper-plane"></i>
        </button>
      </form>
    </div>
  </div>
</div> --}}


<!-- Script modifications for more reliable chat functionality -->
{{-- <script>
  document.addEventListener('DOMContentLoaded', () => {
    // Check if Alpine.js is initialized properly
    if (typeof Alpine === 'undefined') {
      console.warn('Alpine.js is not loaded. Falling back to vanilla JavaScript for chat functionality.');

      // Vanilla JavaScript fallback
      const chatToggleBtn = document.querySelector('[aria-label="Open chat"]');
      const chatCloseBtn = document.querySelector('[aria-label="Close chat"]');
      const chatWindow = document.querySelector('.fixed.right-6.bottom-20.md\\:bottom-6.w-80.md\\:w-96.z-50');

      // Ensure chat is closed initially
      if (chatWindow) {
        chatWindow.style.display = 'none';
      }

      if (chatToggleBtn) {
        chatToggleBtn.addEventListener('click', function() {
          if (chatWindow) {
            chatWindow.style.display = 'flex';
            chatToggleBtn.style.display = 'none';
          }
        });
      }

      if (chatCloseBtn) {
        chatCloseBtn.addEventListener('click', function() {
          if (chatWindow) {
            chatWindow.style.display = 'none';
            if (chatToggleBtn) chatToggleBtn.style.display = 'flex';
          }
        });
      }
    }

    // Enhanced chat functionality
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');
    const typingIndicator = document.getElementById('typingIndicator');

    if (chatForm) {
      chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();

        if (message) {
          // Add user message
          const userMessageElement = document.createElement('div');
          userMessageElement.className = 'flex items-end justify-end space-x-2 mb-4';
          userMessageElement.innerHTML = `
            <div class="bg-primary text-white rounded-lg rounded-br-none p-3 max-w-[80%] shadow-sm">
              <p class="text-sm">${escapeHTML(message)}</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-user text-accent"></i>
            </div>
          `;
          chatMessages.appendChild(userMessageElement);
          chatMessages.scrollTop = chatMessages.scrollHeight;
          chatInput.value = '';

          // Show typing indicator
          typingIndicator.classList.remove('hidden');

          // Simulate response after delay
          setTimeout(() => {
            // Hide typing indicator
            typingIndicator.classList.add('hidden');

            // Add bot response based on message content
            let botResponse = '';

            // Enhanced response logic with identity information
            const messageLower = message.toLowerCase();

            if (messageLower.includes('siapa') && (messageLower.includes('kamu') || messageLower.includes('anda') || messageLower.includes('nama'))) {
              botResponse = 'Saya adalah AquaBot, asisten virtual untuk Smart Aquaponik Poliban. Saya dibuat oleh Muhammad Rasyad sebagai webdev ini. Ia juga yang menambahkan saya di sini. Semoga kehadiran saya dapat membantu Anda. Muhammad Rasyad adalah seorang mahasiswa Poliban prodi Teknik Informatika. Dia membuat bot ini dengan tujuan agar pengunjung bisa belajar dari web yang sedang ia kerjakan mengenai projek Smart Aquaponiks dan dia adalah peserta lulusan dari GoTo x Komdigi x Alibaba Gen AI.';
            } else if (messageLower.includes('halo') || messageLower.includes('hai') || messageLower.includes('hello')) {
              botResponse = 'Halo! Selamat datang di Smart Aquaponik Poliban. Ada yang bisa saya bantu terkait sistem aquaponik kami?';
            } else if (messageLower.includes('monitor') || messageLower.includes('pantau')) {
              botResponse = 'Anda dapat memantau sistem aquaponik secara real-time melalui halaman Monitoring. Data sensor diperbarui setiap 5 menit. Sistem kami memantau suhu air, pH, kadar oksigen terlarut, dan tingkat nutrisi untuk memastikan kondisi optimal untuk tanaman dan ikan.';
            } else if (messageLower.includes('suhu') || messageLower.includes('temperature')) {
              botResponse = 'Suhu ideal untuk sistem aquaponik berkisar antara 18-30°C. Sistem kami secara otomatis memberi peringatan jika suhu di luar kisaran aman. Pemantauan suhu secara real-time membantu pertumbuhan ikan dan tanaman dalam kondisi optimal.';
            } else if (messageLower.includes('ph') || messageLower.includes('keasaman')) {
              botResponse = 'Nilai pH optimal untuk sistem aquaponik adalah 6.8-7.2. Anda bisa mengecek nilai pH terkini di dashboard monitoring. Menjaga pH yang stabil sangat penting karena mempengaruhi penyerapan nutrisi oleh tanaman dan kesehatan ikan.';
            } else if (messageLower.includes('tim') || messageLower.includes('anggota') || messageLower.includes('kelompok')) {
              botResponse = 'Tim Smart Aquaponik Poliban terdiri dari: Muhammad Rasyad (Web Developer), Bagus Setiawan, dan Muhammad Raflli Ramadhan. Proyek ini dibimbing oleh Rahimi Fitri, S.Kom., M.Kom dan Dhiyaussalam, M.Kom dari Program Studi Teknik Informatika Poliban, yang juga berperan sebagai dosen pembimbing tugas akhir.';
            } else if ((messageLower.includes('rasyad') || messageLower.includes('bagus') || messageLower.includes('raflli')) && (messageLower.includes('muhammad') || messageLower.includes('pembuat'))) {
              botResponse = 'Tim pengembang Smart Aquaponik Poliban terdiri dari Muhammad Rasyad (Web Developer), Bagus Setiawan, dan Muhammad Raflli Ramadhan. Mereka adalah mahasiswa Poliban prodi Teknik Informatika. Muhammad Rasyad juga merupakan lulusan program GoTo x Komdigi x Alibaba Gen AI.';
            } else if (messageLower.includes('dosen') || messageLower.includes('pembimbing') || messageLower.includes('rahimi')) {
              botResponse = 'Proyek Smart Aquaponik ini dibimbing oleh Rahimi Fitri, S.Kom., M.Kom dan Dhiyaussalam, M.Kom. Beliau adalah dosen Teknik Informatika di Poliban yang membimbing proyek ini sekaligus berperan sebagai dosen pembimbing tugas akhir tim kami.';
            } else if (messageLower.includes('aquaponik') || messageLower.includes('aquaponic')) {
              botResponse = 'Aquaponik adalah sistem budidaya tanaman dan ikan dalam satu ekosistem yang saling berhubungan. Ikan menghasilkan nutrisi untuk tanaman, dan tanaman membersihkan air untuk ikan. Sistem Smart Aquaponik kami dilengkapi dengan sensor IoT untuk monitoring otomatis kondisi air dan lingkungan, serta sistem kontrol otomatis untuk menjaga parameter kualitas air tetap optimal.';
            } else if (messageLower.includes('manfaat') || messageLower.includes('keuntungan')) {
              botResponse = 'Smart Aquaponik memiliki banyak manfaat, di antaranya: 1) Hemat air hingga 90% dibanding pertanian konvensional, 2) Menghasilkan dua produk (ikan dan tanaman) dalam satu sistem, 3) Bebas pestisida dan pupuk kimia, 4) Otomatisasi mengurangi kebutuhan tenaga kerja, dan 5) Monitoring real-time memastikan kondisi optimal untuk tanaman dan ikan.';
            } else if (messageLower.includes('iot') || messageLower.includes('sensor')) {
              botResponse = 'Sistem IoT pada Smart Aquaponik kami menggunakan berbagai sensor untuk memantau parameter kualitas air seperti suhu, pH, kadar oksigen terlarut, dan konduktivitas listrik (EC). Data dari sensor dikirim ke server untuk dianalisis dan ditampilkan pada dashboard monitoring. Sistem juga dapat memberikan notifikasi jika ada parameter yang di luar batas normal.';
            } else {
              botResponse = 'Terima kasih atas pertanyaan Anda. Saya AquaBot, asisten virtual untuk Smart Aquaponik Poliban. Untuk informasi lebih detail, silakan kunjungi halaman FAQ atau hubungi tim kami di +62-812-3456-7890.';
            }

            const botMessageElement = document.createElement('div');
            botMessageElement.className = 'flex items-start space-x-2';
            botMessageElement.innerHTML = `
              <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-robot text-primary"></i>
              </div>
              <div class="bg-white rounded-lg rounded-tl-none p-3 max-w-[80%] shadow-sm">
                <p class="text-sm">${botResponse}</p>
              </div>
            `;
            chatMessages.appendChild(botMessageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
          }, 1500);
        }
      });
    }

    // Escape HTML to prevent XSS
    function escapeHTML(str) {
      return str.replace(/[&<>"']/g, function(match) {
        return {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;'
        }[match];
      });
    }
  });
</script> --}}
 --}}
  <!-- Main Content Area -->
  <main class="flex-grow pt-20 px-4" id="main-content">
    <!-- This is where your page content will go -->
    @yield('content')
</main>

  <!-- Enhanced Footer -->
  {{-- <footer class="bg-gray-900 text-gray-300 pt-12 pb-6 mt-auto">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <!-- About Section -->
        <div>
          <div class="flex items-center space-x-3 mb-4">
            <div>
              <img src="/images/LOGO PT. JBG.png" alt="Logo PT. JBG" class="h-14 w-24 object-contain" />
            </div>
            <h3 class="text-2xl font-bold text-white">Sistem Monitoring Tanah</h3>
          </div>
          <p class="text-gray-400 mb-6">
            Inovasi sistem monitoring tanah berbasis IoT untuk pertanian berkelanjutan yang ramah lingkungan dan efisien.
          </p>
          <div class="flex space-x-4">
            <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" aria-label="Twitter" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" aria-label="Instagram" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" aria-label="YouTube" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors">
              <i class="fab fa-youtube"></i>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-lg font-semibold text-white mb-5 border-b border-gray-700 pb-2">Navigasi</h4>
          <ul class="space-y-3">
            <!-- <li><a href="/" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Beranda</a></li> -->
            <li><a href="/monitoring" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Monitoring</a></li>
            <!-- <li><a href="/about" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Tentang Kami</a></li>
            <li><a href="/contact" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Kontak</a></li> -->
          </ul>
        </div>

        <!-- Features -->
        <div>
          <h4 class="text-lg font-semibold text-white mb-5 border-b border-gray-700 pb-2">Fitur Sistem</h4>
          <ul class="space-y-3">
            <li><a href="#" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Dashboard IoT</a></li>
            <li><a href="#" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Monitoring Real-time</a></li>
            <li><a href="#" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Analisis Data</a></li>
            <li><a href="#" class="flex items-center text-gray-400 hover:text-primary transition-colors"><i class="fas fa-chevron-right mr-2 text-xs text-primary"></i> Notifikasi Otomatis</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div>
          <h4 class="text-lg font-semibold text-white mb-5 border-b border-gray-700 pb-2">Kontak Kami</h4>
          <ul class="space-y-4">
            <li class="flex items-start">
              <i class="fas fa-map-marker-alt text-primary mt-1 mr-3"></i>
              <span>Jl. Soekarno-Hatta No.378, Bandung, Jawa Barat, Indonesia</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-envelope text-primary mr-3"></i>
              <a href="mailto:info@aquaponikpoliban.id" class="hover:text-primary transition-colors">info@aquaponikpoliban.id</a>
            </li>
            <li class="flex items-center">
              <i class="fas fa-phone text-primary mr-3"></i>
              <a href="tel:+6281234567890" class="hover:text-primary transition-colors">+62 812 3456 7890</a>
            </li>
            <li class="flex items-center">
              <i class="fas fa-clock text-primary mr-3"></i>
              <span>Senin - Jumat: 08:00 - 16:00</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Newsletter -->
      <div class="py-6 border-t border-gray-800 border-b mb-6">
        <div class="max-w-xl mx-auto text-center">
          <h5 class="text-white font-semibold mb-3">Berlangganan Newsletter</h5>
          <p class="text-gray-400 mb-4">Dapatkan update terbaru tentang teknologi aquaponik dan tips budidaya langsung ke email Anda</p>
          <form class="flex flex-col sm:flex-row gap-2 justify-center">
            <input
              type="email"
              placeholder="Alamat email Anda"
              class="px-4 py-3 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-700 w-full sm:w-auto"
              required
            />
            <button
              type="submit"
              class="bg-primary hover:bg-primary-600 text-white font-medium px-6 py-3 rounded-lg transition-colors shadow-sm"
            >
              Langganan
            </button>
          </form>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="text-center">
        <div class="flex flex-wrap justify-center items-center gap-6 mb-6">
          <a href="#" class="text-gray-400 hover:text-white transition-colors">Kebijakan Privasi</a>
          <a href="#" class="text-gray-400 hover:text-white transition-colors">Syarat & Ketentuan</a>
          <a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a>
          <a href="#" class="text-gray-400 hover:text-white transition-colors">Karir</a>
        </div>
        <p class="text-gray-500">&copy; 2025 Sistem Monitoring Tanah. Hak Cipta Dilindungi.</p>
      </div>
    </div>
  </footer> --}}

  <!-- Back To Top Button -->
  <button
    id="backToTop"
    class="fixed bottom-24 right-6 z-30 bg-primary w-10 h-10 rounded-full text-white flex items-center justify-center shadow-lg transform transition-all duration-300 hover:scale-110 hover:bg-primary-600 opacity-0"
    aria-label="Back to top"
  >
    <i class="fas fa-arrow-up"></i>
  </button>
@stack('scripts')
  <!-- Scripts -->
  <script>
    // Initialize AOS
    document.addEventListener('DOMContentLoaded', () => {
      AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });

      // Back to top button
      const backToTopButton = document.getElementById('backToTop');

      window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
          backToTopButton.classList.add('opacity-100');
          backToTopButton.classList.remove('opacity-0');
        } else {
          backToTopButton.classList.add('opacity-0');
          backToTopButton.classList.remove('opacity-100');
        }
      });

      backToTopButton.addEventListener('click', () => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });

      // Chat Functionality
      {{-- const chatForm = document.getElementById('chatForm');
      const chatInput = document.getElementById('chatInput');
      const chatMessages = document.getElementById('chatMessages');
      const typingIndicator = document.getElementById('typingIndicator');

      if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const message = chatInput.value.trim();

          if (message) {
            // Add user message
            const userMessageElement = document.createElement('div');
            userMessageElement.className = 'flex items-end justify-end space-x-2 mb-4';
            userMessageElement.innerHTML = `
              <div class="bg-primary text-white rounded-lg rounded-br-none p-3 max-w-[80%] shadow-sm">
                <p class="text-sm">${escapeHTML(message)}</p>
              </div>
              <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-accent"></i>
              </div>
            `;
            chatMessages.appendChild(userMessageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatInput.value = '';

            // Show typing indicator
            typingIndicator.classList.remove('hidden');

            // Simulate response after delay
            setTimeout(() => {
              // Hide typing indicator
              typingIndicator.classList.add('hidden');

              // Add bot response
              let botResponse = '';

              // Simple response logic based on keywords
              if (message.toLowerCase().includes('halo') || message.toLowerCase().includes('hai') || message.toLowerCase().includes('hello')) {
                botResponse = 'Halo! Ada yang bisa saya bantu terkait sistem aquaponik kami?';
              } else if (message.toLowerCase().includes('monitor') || message.toLowerCase().includes('pantau')) {
                botResponse = 'Anda dapat memantau sistem aquaponik secara real-time melalui halaman Monitoring. Data sensor diperbarui setiap 5 menit.';
              } else if (message.toLowerCase().includes('suhu') || message.toLowerCase().includes('temperature')) {
                botResponse = 'Suhu ideal untuk sistem aquaponik berkisar antara 18-30°C. Sistem kami secara otomatis memberi peringatan jika suhu di luar kisaran aman.';
              } else if (message.toLowerCase().includes('ph') || message.toLowerCase().includes('keasaman')) {
                botResponse = 'Nilai pH optimal untuk sistem aquaponik adalah 6.8-7.2. Anda bisa mengecek nilai pH terkini di dashboard monitoring.';
              } else {
                botResponse = 'Terima kasih atas pertanyaan Anda. Tim kami akan merespons segera. Untuk informasi lebih cepat, silakan kunjungi halaman FAQ atau hubungi kami di +62-812-3456-7890.';
              }

              const botMessageElement = document.createElement('div');
              botMessageElement.className = 'flex items-start space-x-2';
              botMessageElement.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-robot text-primary"></i>
                </div>
                <div class="bg-white rounded-lg rounded-tl-none p-3 max-w-[80%] shadow-sm">
                  <p class="text-sm">${botResponse}</p>
                </div>
              `;
              chatMessages.appendChild(botMessageElement);
              chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 1500);
          }
        });
      }

      // Escape HTML to prevent XSS
      function escapeHTML(str) {
        return str.replace(/[&<>"']/g, function(match) {
          return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
          }[match];
        });
      } --}}

      // Navbar scroll effect
      const header = document.querySelector('header');

      window.addEventListener('scroll', () => {
        if (window.scrollY > 30) {
          header.classList.add('shadow-md');
          header.classList.remove('shadow-sm');
        } else {
          header.classList.remove('shadow-md');
          header.classList.add('shadow-sm');
        }
      });
    });
  </script>
</body>
</html>
