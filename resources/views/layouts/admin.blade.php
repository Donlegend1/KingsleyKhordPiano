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

  <div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="hidden md:block w-64 bg-black text-white p-6 flex-shrink-0">
      <div class="mb-10">
        <a href="/" class="text-2xl font-bold">
          <img src="/logo/logo.png" alt="Logo">
        </a>
      </div>

      <nav class="space-y-4">
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
    </aside>

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

    <div class="flex-1 flex flex-col">

      <!-- Mobile Top Bar -->
      <header class="md:hidden flex justify-between items-center bg-black text-white p-4">
        <a href="/home"><img src="/logo/logo.png" alt="Logo" class="h-8"></a>
        <button id="mobile-open" class="text-2xl">
          <i class="fa fa-bars"></i>
        </button>
      </header>

      <!-- Main Page Content -->
      <main class="flex-1 p-4">
        @yield('content')
      </main>

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
