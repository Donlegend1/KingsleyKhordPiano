<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://js.paystack.co/v2/inline.js"></script>
     <script src="https://js.stripe.com/v3/"></script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 text-gray-800">

<div class="min-h-screen flex">
  <!-- Sidebar -->
  <aside class="w-64 bg-black shadow-lg p-6 text-white">
    <div class="mb-10">
      <a href="/home" class="text-2xl font-bold"><img src="/logo/logo.png" alt="KingsleyKhord logo"></a>
    </div>
    <nav class="space-y-4">
      <a href="/home" class="flex items-center border border-gray-600 gap-3 p-2 rounded-lg hover:bg-gray-200">
        <i class="fa fa-home"></i>
        <span>Dashboard</span>
      </a>
      <a href="/admin/users" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
        <i class="fa fa-user"></i>
        <span>Users</span>
      </a>
      <a href="/admin/courses" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
        <i class="fa fa-file"></i>
        <span>Courses</span>
      </a>
      <a href="/admin/ear-training" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
        <i class="fa fa-leanpub" aria-hidden="true"></i>
        <span>Ear Training</span>
      </a>
      <a href="/admin/live-shows" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
        <i class="fa fa-television" aria-hidden="true"></i>
        <span>Live Show</span>
      </a>
      <a href="/admin/uploads/list" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 hover:text-black">
        <i class="fa fa-upload"></i>
        <span>Others</span>
      </a>
      <a href="#" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200">
        <i class="fa fa-cog"></i>
        <span>Settings</span>
      </a>
       <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="flex items-center gap-3 p-2 rounded-lg hover:bg-yellow-400 hover:text-black" type="submit" >
           <i class="fa fa-sign-out-alt"></i> {{ __('Logout') }}
        </button>
    </form>
     
    </nav>
  </aside>

   @yield('content')
       
</div>

</body>
</html>
