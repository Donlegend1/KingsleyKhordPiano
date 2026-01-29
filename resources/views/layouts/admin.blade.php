<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  @viteReactRefresh
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">

  <div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside id="sidebar" class="hidden md:block w-64 bg-black text-white flex-shrink-0 overflow-y-auto">
      <div class="h-full flex flex-col">
        <div class="p-6">
          <div class="mb-10">
            <a href="/" class="text-2xl font-bold">
              <img src="/logo/logo.png" alt="Logo">
            </a>
          </div>

          <nav class="space-y-2">
            <a href="/admin/dashboard" class="flex items-center gap-3 p-2 rounded-lg border border-gray-600 hover:bg-gray-200 hover:text-black">
              <i class="fa fa-home"></i> <span>Dashboard</span>
            </a>
            <a href="/admin/users" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <i class="fa fa-user"></i> <span>Users</span>
            </a>
            <a href="/admin/courses" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <i class="fa fa-map" aria-hidden="true"></i> <span>Course Road Map</span>
            </a>
            <a href="/admin/ear-training" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <i class="fa fa-leanpub"></i> <span>Ear Training</span>
            </a>
            <a href="/admin/live-shows" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <i class="fa fa-television"></i> <span>Live Show</span>
            </a>
            <a href="/admin/uploads/piano-exercise" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-piano-icon lucide-piano"><path d="M18.5 8c-1.4 0-2.6-.8-3.2-2A6.87 6.87 0 0 0 2 9v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-8.5C22 9.6 20.4 8 18.5 8"/><path d="M2 14h20"/>
                <path d="M6 14v4"/><path d="M10 14v4"/><path d="M14 14v4"/><path d="M18 14v4"/></svg> <span>Piano Exercise</span>
            </a>
            <a href="/admin/uploads/extra-courses" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-school-icon lucide-school"><path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M18 5v16"/><path d="m4 6 7.106-3.79a2 2 0 0 1 1.788 0L20 6"/><path d="m6 11-3.52 2.147a1 1 0 0 0-.48.854V19a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-5a1 1 0 0 0-.48-.853L18 11"/>
                <path d="M6 5v16"/><circle cx="12" cy="9" r="2"/></svg> <span>Extra Courses</span>
            </a>
            <a href="/admin/uploads/quick-lessons" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open-check-icon lucide-book-open-check"><path d="M12 21V7"/><path d="m16 12 2 2 4-4"/><path d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3"/>
              </svg> <span>Quick Lessons</span>
            </a>
            <a href="/admin/uploads/learn-songs" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-disc-icon lucide-disc">
                <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="2"/></svg> <span>Learn Songs</span>
            </a>
            <a href="/admin/website-video" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-video-icon lucide-list-video"><path d="M21 5H3"/><path d="M10 12H3"/><path d="M10 19H3"/><path d="M15 12.003a1 1 0 0 1 1.517-.859l4.997 2.997a1 1 0 0 1 0 1.718l-4.997 2.997a1 1 0 0 1-1.517-.86z"/>
              </svg> <span>Website Videos</span>
            </a>
            <a href="/admin/midi-file" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-video-camera-icon lucide-file-video-camera"><path d="M4 12V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.706.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="m10 17.843 3.033-1.755a.64.64 0 0 1 .967.56v4.704a.65.65 0 0 1-.967.56L10 20.157"/>
                <rect width="7" height="6" x="3" y="16" rx="1"/></svg><span>Midi File</span>
            </a>
             <a href="/admin/pdf-download" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text-icon lucide-file-text">
                <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                <span>PDF Download</span>
            </a>
            <a href="/admin/audio-download" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-music-icon lucide-file-music"><path d="M11.65 22H18a2 2 0 0 0 2-2V8a2.4 2.4 0 0 0-.706-1.706l-3.588-3.588A2.4 2.4 0 0 0 14 2H6a2 2 0 0 0-2 2v10.35"/>
                <path d="M14 2v5a1 1 0 0 0 1 1h5"/>
                <path d="M8 20v-7l3 1.474"/><circle cx="6" cy="20" r="2"/></svg><span>Audio Download</span>
            </a>
            <a href="/admin/email-campaign" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <i class="fa fa-file"></i> <span>Email Campaign</span>
            </a>
            <a href="/member/profile" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
              <i class="fa fa-cog"></i> <span>Settings</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="flex items-center gap-3 p-2 rounded-lg hover:bg-yellow-400 hover:text-black">
                <i class="fa fa-sign-out-alt"></i> Logout
              </button>
            </form>
          </nav>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

      <!-- Mobile Top Bar -->
      <header class="md:hidden flex justify-between items-center bg-black text-white p-4">
        <a href="/home"><img src="/logo/logo.png" alt="Logo" class="h-8"></a>
        <button id="mobile-open" class="text-2xl">
          <i class="fa fa-bars"></i>
        </button>
      </header>

      <!-- Content Area -->
      <main class="flex-1 overflow-y-auto bg-gray-50">
        @yield('content')
      </main>
    </div>

    <!-- Mobile Sidebar (Overlay) -->
    <div id="mobile-sidebar" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
      <div class="w-64 bg-black text-white h-full p-6">
        <div class="mb-10 flex justify-between items-center">
          <a href="/home" class="text-2xl font-bold">
            <img src="/logo/logo.png" alt="Logo">
          </a>
          <button id="mobile-close" class="text-white text-2xl">&times;</button>
        </div>
        <nav class="space-y-4">
          <!-- same nav links as above -->
        
        <a href="/home" class="flex items-center gap-3 p-2 rounded-lg border border-gray-600 hover:bg-gray-200 hover:text-black">
          <i class="fa fa-home"></i> <span>Dashboard</span>
        </a>
        <a href="/admin/users" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
          <i class="fa fa-user"></i> <span>Users</span>
        </a>
        <a href="/admin/courses" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
          <i class="fa fa-map" aria-hidden="true"></i> <span>Course Road Map</span>
        </a>
        
        <a href="/admin/ear-training" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
          <i class="fa fa-leanpub"></i> <span>Ear Training</span>
        </a>
        <a href="/admin/live-shows" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
          <i class="fa fa-television"></i> <span>Live Show</span>
        </a>
        <a href="/admin/uploads/list" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
          <i class="fa fa-upload"></i> <span>Others</span>
        </a>
        <a href="/member/profile" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
          <i class="fa fa-cog"></i> <span>Settings</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex items-center gap-3 p-2 rounded-lg hover:bg-yellow-400 hover:text-black">
            <i class="fa fa-sign-out-alt"></i> Logout
          </button>
        </form>
      </nav>
        </nav>
      </div>
    </div>
  </div>

  <script>
    const openBtn = document.getElementById('mobile-open');
    const closeBtn = document.getElementById('mobile-close');
    const mobileSidebar = document.getElementById('mobile-sidebar');

    openBtn?.addEventListener('click', () => mobileSidebar.classList.remove('hidden'));
    closeBtn?.addEventListener('click', () => mobileSidebar.classList.add('hidden'));
  </script>

</body>
</html>
