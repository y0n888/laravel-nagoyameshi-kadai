<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // 予約一覧ページ
    public function index() 
    {
        $reservations = Reservation::where('user_id', auth()->id())
            ->orderBy('reservation_date', 'desc')
            ->paginate(15);

        return view('reservation.index', compact('reservations'));
    }

    // 予約ページ
    public function create(Restaurant $restaurant) 
    {
        return view('reservation.create', compact('restaurant'));
    }

    // 予約機能
    public function store(Request $request, Restaurant $restaurant) 
    {
        $request->validate([
            'reservation_date' => 'required|date_format:Y-m-d',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|integer|min:1|max:50',
        ]);

        Reservation::create([
            'reserved_datetime' => $request->input('reservation_date'). ' ' . $request->input('reservation_time'),
            'number_of_people' => $request->input('number_of_people'),
            'restaurant_id' => $restaurant->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('reservation.index')->with('flash_message', '予約が完了しました。');
    }

    // 予約キャンセル機能
    public function destroy() 
    {
        if ($reservation->user_id !== auth()->id()) {
            return redirect()->route('reservation.index')->with('error_message', '不正なアクセスです。');
        }

        $reservation->delete();

        return redirect()->route('reservation.index')->with('flash_message', '予約をキャンセルしました。');
    }
}
