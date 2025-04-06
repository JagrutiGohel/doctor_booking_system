@extends('layouts.app')
@section('content')
<div class="flex">
  @include('partials.sidebar')
  <div class="w-full p-8">
    <h2 class="text-2xl font-bold mb-4 border-b pb-2">Booked Slots</h2>
    <div id="calendar" class="max-w-lg mx-auto mb-6"></div>

    <h3 class="text-xl font-semibold mb-2">Available Time Slots</h3>
    <div id="slots" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
  </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const slotsEl = document.getElementById('slots');
    let selectedDate = null;
    const today = new Date();
    const oneYearFromToday = new Date();
    oneYearFromToday.setFullYear(today.getFullYear() + 1);
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      initialDate: today.toISOString().split('T')[0], // set today as default selected date
      validRange: {
        start: today.toISOString().split('T')[0], // disable past dates
        end: oneYearFromToday.toISOString().split('T')[0] // limit up to 1 year from today
      },
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: ''
      },
      selectable: true,
      dateClick: function (info) {
        selectedDate = info.dateStr;
        fetch(`/calendar/slots?date=${selectedDate}`)
          .then(res => res.json())
          .then(data => {
            slotsEl.innerHTML = '';
            if (data.length === 0) {
              slotsEl.innerHTML = '<p class="text-gray-500 col-span-full">No available slots.</p>';
              return;
            }

            data.forEach(slot => {
              const btn = document.createElement('button');
              btn.innerText = `${slot.from} - ${slot.to}`;
              btn.className = `text-center px-4 py-2 rounded border text-sm font-semibold transition ${
                slot.booked >= 2
                  ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                  : 'bg-white text-teal-600 border-teal-500 hover:bg-teal-500 hover:text-white'
              }`;
              btn.disabled = slot.booked >= 2;
              debugger;
              btn.addEventListener('click', () => {
                if (confirm(`Book slot: ${slot.from} - ${slot.to} on ${selectedDate}?`)) {
                  fetch('/calendar/book', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                      from: slot.from,
                      to: slot.to,
                      date: selectedDate,
                    }),
                  })
                  .then(res => res.json())
                  .then(response => {
                    alert(response.message);
                    btn.classList.remove('bg-white', 'text-teal-600', 'hover:bg-teal-500', 'hover:text-white');
                    btn.classList.add('bg-gray-200', 'text-gray-500');
                    btn.disabled = true;
                  });
                }
              });

              slotsEl.appendChild(btn);
            });
          });
      },
    });

    calendar.render();
  });
</script>
@endsection
