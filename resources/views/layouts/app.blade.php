<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Booking System</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- FullCalendar Styles (only needed in pages using FullCalendar) -->
  @yield('calendar-css')

  <!-- Optional: Custom CSS -->
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .form-input {
      @apply border border-gray-300 rounded px-3 py-2 w-full;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navigation -->
  <nav class="bg-white shadow mb-4">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
      <div class="text-xl font-bold">Doctor Booking</div>
      <div>
        <!-- Add user info / logout here if needed -->
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <main>
    @yield('content')
  </main>

  <!-- Scripts -->
  @yield('calendar-js')
</body>
</html>
