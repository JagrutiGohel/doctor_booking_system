<div class="w-64 bg-gray-100 h-screen p-6 shadow-md">
  <h3 class="text-xl font-bold mb-6">Doctor Panel</h3>
  <ul class="space-y-4">
    <li>
      <a href="{{ route('available-slots.index') }}"
         class="block px-4 py-2 rounded hover:bg-teal-100 {{ request()->routeIs('available-slots.index') ? 'bg-teal-200 font-semibold' : '' }}">
        Available Slots
      </a>
    </li>
    <li>
      <a href="{{ route('unavailable-slots.index') }}"
         class="block px-4 py-2 rounded hover:bg-red-100 {{ request()->routeIs('unavailable-slots.index') ? 'bg-red-200 font-semibold' : '' }}">
        Unavailable Slots
      </a>
    </li>
    <li>
      <a href="{{ route('unavailable-days.index') }}"
         class="block px-4 py-2 rounded hover:bg-yellow-100 {{ request()->routeIs('unavailable-days.index') ? 'bg-yellow-200 font-semibold' : '' }}">
        Unavailable Days
      </a>
    </li>
    <li>
      <a href="{{ route('calendar.index') }}"
         class="block px-4 py-2 rounded hover:bg-blue-100 {{ request()->routeIs('calendar.index') ? 'bg-blue-200 font-semibold' : '' }}">
        Booked Slots
      </a>
    </li>
  </ul>
</div>
