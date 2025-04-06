@extends('layouts.app')
@section('content')
<div class="flex">
  @include('partials.sidebar')
  <div class="w-full p-8">
    <h2 class="text-2xl font-bold mb-4">Available Slots</h2>
    <form action="{{ route('available-slots.store') }}" method="POST">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($weekDays as $day)
        <div>
          <label class="block font-semibold">{{ $day }}</label>
          <div class="flex items-center gap-2">
            <input type="time" name="available[{{ $day }}][from]" value="{{ $available[$day]['from'] ?? '' }}" class="form-input w-full">
            <input type="time" name="available[{{ $day }}][to]" value="{{ $available[$day]['to'] ?? '' }}" class="form-input w-full">
          </div>
        </div>
        @endforeach
      </div>
      <button type="submit" class="mt-4 px-6 py-2 bg-black text-white rounded">Save</button>
    </form>
  </div>
</div>
@endsection