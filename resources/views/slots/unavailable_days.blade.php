@extends('layouts.app')
@section('content')
<div class="flex">
  @include('partials.sidebar')
  <div class="w-full p-8">
    <h2 class="text-2xl font-bold mb-4">Unavailable Days</h2>
    <form action="{{ route('unavailable-days.store') }}" method="POST">
      @csrf
      <div id="unavailable-days-container" class="space-y-2">
      @php
        $today = date('Y-m-d');
      @endphp
        @foreach($unavailableDays as $day)
        <div class="flex items-center gap-2">
          <input type="date" name="unavailable_days[]" value="{{ $day }}" min="{{$today}}" class="form-input" >
          <button type="button" class="remove-date text-red-600">&times;</button>
        </div>
        @endforeach
        <div class="flex items-center gap-2">
          <input type="date" name="unavailable_days[]" class="form-input" min="{{$today}}">
          <button type="button" class="add-date text-teal-600">+</button>
        </div>
      </div>
      <button type="submit" class="mt-4 px-6 py-2 bg-teal-600 text-white rounded">Save</button>
    </form>
  </div>
</div>
<script>
  document.querySelector('.add-date').addEventListener('click', function () {
    const container = document.getElementById('unavailable-days-container');
    const newField = document.createElement('div');
    newField.className = 'flex items-center gap-2';
    newField.innerHTML = `<input type="date" name="unavailable_days[]" class="form-input">
    <button type="button" class="remove-date text-red-600">&times;</button>`;
    container.appendChild(newField);
  });
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-date')) {
      e.target.closest('div').remove();
    }
  });
</script>
@endsection
