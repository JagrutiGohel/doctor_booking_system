<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlotController;
Route::get('/', [SlotController::class, 'showAvailableForm'])->name('available-slots.index');
Route::get('slots/available', [SlotController::class, 'showAvailableForm'])->name('available-slots.index');
Route::post('slots/available', [SlotController::class, 'storeAvailable'])->name('available-slots.store');
Route::get('slots/unavailable', [SlotController::class, 'showUnavailableForm'])->name('unavailable-slots.index');
Route::post('slots/unavailable', [SlotController::class, 'storeUnavailable'])->name('unavailable-slots.store');
Route::get('slots/unavailable-days', [SlotController::class, 'showUnavailableDays'])->name('unavailable-days.index');
Route::post('slots/unavailable-days', [SlotController::class, 'storeUnavailableDays'])->name('unavailable-days.store');
Route::get('calendar/slots', [SlotController::class, 'getSlotsForDate']);
Route::get('calendar', [SlotController::class, 'calendar'])->name('calendar.index');
Route::post('calendar/book', [SlotController::class, 'bookSlot']);

