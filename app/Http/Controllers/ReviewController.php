<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;

class ReviewController extends Controller
{
    // レビュー一覧ページ
    public function index(Restaurant $restaurant)
    {  
        $user = Auth::user();

        if (Auth::user()->subscribed('premium_plan')) {
            $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->paginate(5);
        } else {
            $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->take(3)->get();
        }

        return view('reviews.index', compact('restaurant', 'reviews'));
    }

    // レビュー投稿ページ
    public function create(Restaurant $restaurant)
    {
        return view('reviews.create', compact('restaurant'));
    }

    // レビュー投稿機能
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'score' => 'required|integer|between:1,5',
            'content' => 'required',
        ]);

        Review::create([
            'score' => $request->score,
            'content' => $request->content,
            'restaurant_id' => $restaurant->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを投稿しました。');
    }

    // レビュー編集ページ
    public function edit(Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        return view('reviews.edit', compact('restaurant', 'review'));
    }

    // レビュー更新機能
    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $request->validate([
            'score' => 'required|integer|between:1,5',
            'content' => 'required',
        ]);

        $review->update([
            'score' => $request->score,
            'content' => $request->content,
        ]);

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを編集しました。');
    }

    // レビュー削除機能
    public function destroy(Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $review->delete();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを削除しました。');
    }
}
