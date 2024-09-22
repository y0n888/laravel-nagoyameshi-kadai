<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword', '');

        $category_id = $request->input('category_id', null);

        $price = $request->input('price', null);

        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
        ];

        $sorted = "created_at desc";
        $sort_query = [];

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        $query = Restaurant::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword){
                $q->where('name', 'LIKE', "%{$keyword}%")
                ->orWhere('address', 'LIKE', "%{$keyword}%")
                ->orWhereHas('categories', function($query) use ($keyword) {
                    $query->where('categories_name', 'LIKE', "%{$keyword}%");
                });
            });
        }

        if ($category_id) {
            $query->whereHas('categories', function($query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        }

        if ($price) {
            $query->where('lowest_price', '<=', $price);
        }

        $restaurants = $query->sortable($sort_query)
                            ->orderBy(
                                key($sort_query) ?? 'created_at',
                                $sort_query ? reset($sort_query) : 'desc'
                                )
                            ->paginate(15);

        $categories = Category::all();
        $total = $restaurants->total();

        return view('restaurants.index',compact('keyword', 'category_id', 'price', 'sorts', 'sorted', 'restaurants', 'categories', 'total'));
    }

    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', compact('restaurant'));
    }
}
