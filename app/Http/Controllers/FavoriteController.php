<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Restaurant;

class FavoriteController extends Controller
{
    // お気に入り一覧ページ
    public function index() 
    {
        $user = Auth::user();

        $favorite_restaurants = $user->restaurants()
            ->orderBy('restaurant_user.created_at', 'desc')
            ->paginate(15);

        return view('favorites.index', compact('favorite_restaurants'));
    }

    // お気に入り追加機能
    public function store(Request $request, Restaurant $restaurant) 
    {
        $user = Auth::user();

        // $user->restaurants()->attach($restaurant->id);
        if (!$user->restaurants()->where('restaurant_id', $restaurant->id)->exists()) {
            $user->restaurants()->attach($restaurant->id);
        }

        dd($user->restaurants()->get());

        return redirect()->back()->with('flash_message', 'お気に入りに追加しました。');
    }

    // お気に入り解除機能
    public function destroy(Request $request, Restaurant $restaurant) 
    {
        $user = Auth::user();

        $user->restaurants()->detach($restaurant->id);

        return redirect()->back()->with('flash_message', 'お気に入りを解除しました。');
    }
}
