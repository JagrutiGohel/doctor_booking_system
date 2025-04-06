<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Slot;
use App\Models\UnavailableDay;

class SlotController extends Controller
{
    protected $weekDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    public function showAvailableForm()
    {
        $slots = Slot::where('type', 'available')->get()->groupBy('day');
        return view('slots.available', [
            'available' => $this->mapSlots($slots),
            'weekDays' => $this->weekDays
        ]);
    }

    public function storeAvailable(Request $request)
    {
        Slot::where('type', 'available')->delete();

        foreach ($request->input('available', []) as $day => $range) {
            if (!empty($range['from']) && !empty($range['to'])) {
                Slot::create([
                    'day' => $day,
                    'from' => $range['from'],
                    'to' => $range['to'],
                    'type' => 'available',
                    'date' => now()
                ]);
            }
        }

        return redirect()->back()->with('success', 'Available slots updated successfully.');
    }

    public function showUnavailableForm()
    {
        $slots = Slot::where('type', 'unavailable')->get()->groupBy('day');
        return view('slots.unavailable', [
            'unavailable' => $this->mapSlots($slots),
            'weekDays' => $this->weekDays
        ]);
    }

    public function storeUnavailable(Request $request)
    {
        Slot::where('type', 'unavailable')->delete();

        foreach ($request->input('unavailable', []) as $day => $range) {
            if (!empty($range['from']) && !empty($range['to'])) {
                Slot::create([
                    'day' => $day,
                    'from' => $range['from'],
                    'to' => $range['to'],
                    'type' => 'unavailable',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Unavailable slots updated successfully.');
    }

    public function showUnavailableDays()
    {
        $days = UnavailableDay::pluck('date')->toArray();
        return view('slots.unavailable_days', [
            'unavailableDays' => $days,
            'weekDays' => $this->weekDays
        ]);
    }

    public function storeUnavailableDays(Request $request)
    {
        UnavailableDay::truncate();
        foreach ($request->input('unavailable_days', []) as $date) {
            if ($date) {
                UnavailableDay::create(['date' => $date]);
            }
        }
        return redirect()->back()->with('success', 'Unavailable days saved.');
    }

    public function getSlotsForDate(Request $request)
    {
        $date = Carbon::parse($request->date);
        $dayName = strtolower($date->format('l'));

        // Check for full-day unavailability
        if (UnavailableDay::where('date', $date->toDateString())->exists()) {
            return response()->json([]);
        }

        $available = Slot::where('type', 'available')->where('day', $dayName)->first();
        $unavailable = Slot::select('from','to')->whereIn('type', ['unavailable','booked'])->where('day', $dayName)->get();

        if (!$available) return response()->json([]);
        
        $slots = $this->generateSlots($available->from, $available->to, 30,$request->date);
        $response = [];
        foreach ($slots as $slot) {
            // Skip if in unavailable time
            foreach($unavailable as $un){
                if ($un && $this->slotInRange($slot, $un)) {
                    continue;
                }
            }
            
            $existing = Slot::where([
                'type' => 'booked',
                'day' => $dayName,
                'date' => $date,
                'from' => $slot['from'],
                'to' => $slot['to'],
            ])->first();
            
            
            $response[] = [
                'id' => md5($slot['from'].$slot['to'].$date),
                'from' => $slot['from'],
                'to' => $slot['to'],
                'booked' => (isset($existing->booked_count))?$existing->booked_count:0,
            ];
        }

        return response()->json($response);
    }

    public function bookSlot(Request $request)
    {
        $date = Carbon::parse($request->date ?? now())->toDateString();
        $dayName = Carbon::parse($date)->format('l'); // 'l' = full day name
        $from = $request->from;
        $to = $request->to;

        $exists = Slot::where([
            'date' => $date,
            'day' => $dayName,
            'from' => $from,
            'to' => $to,
        ])->first();

        if(!$exists) {
        $exists = Slot::create([
                        'type' => 'available',
                        'date' => $date,
                        'day' => $dayName,
                        'from' => $from,
                        'to' => $to,
                        'booked_count' => 0
                    ]);
        }
        $bookedCount = $exists->booked_count;
        $id = $exists->id;
        if ($bookedCount >= 2) {
            return response()->json(['message' => 'This slot is fully booked.'], 400);
        } else {
            if ($bookedCount < 1) {
                Slot::where('id',$id)
                ->update([
                'booked_count' => $bookedCount + 1  
                ]);
            } elseif($bookedCount == 1) {
                Slot::where('id',$id)
                ->update([
                'type' => 'booked',
                'booked_count' => $bookedCount + 1  
                ]);
            }
            
        }
        return response()->json(['message' => 'Booking successful.']);
    }

    private function mapSlots($grouped)
    {
        $output = [];
        foreach ($this->weekDays as $day) {
            if (isset($grouped[$day])) {
                $slot = $grouped[$day]->first();
                $output[$day] = ['from' => $slot->from, 'to' => $slot->to];
            }
        }
        return $output;
    }

    private function slotInRange($slot, $range)
    {
        return $slot['from'] >= $range->from && $slot['to'] <= $range->to;
    }

    private function generateSlots($from, $to, $duration, $date)
    {
        $slots = [];

        // Ensure the timezone is set
        $now = Carbon::now('Asia/Kolkata');
        $slotDate = Carbon::parse($date, 'Asia/Kolkata');

        // Parse full datetime with proper format
        $start = Carbon::parse($date . ' ' . $from, 'Asia/Kolkata');
        $end = Carbon::parse($date . ' ' . $to, 'Asia/Kolkata');

        while ($start->copy()->addMinutes($duration) <= $end) {
            // Skip if today and time has already passed
            if ($slotDate->isToday() && $start->lessThan($now)) {
                $start->addMinutes($duration);
                continue;
            }

            $next = $start->copy()->addMinutes($duration);

            $slots[] = [
                'from' => $start->format('H:i'),
                'to' => $next->format('H:i'),
            ];

            $start = $next;
        }

        return $slots;
    }




    public function calendar(){
        return view('calendar');
    }

    
}
