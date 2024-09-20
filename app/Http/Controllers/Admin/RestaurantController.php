<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegularHoliday;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    // 一覧ページ
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $query = Restaurant::query();

        if ($keyword !== null && $keyword !== '') {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        $restaurants = $query->paginate(15);

        $total = $query->count();

        return view ('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    // 詳細ページ
    public function show(Restaurant $restaurant)
    {
        return view ('admin.restaurants.show', compact('restaurant'));
    }

    // 登録ページ
    public function create()
    {
        $categories = Category::all();

        $regular_holidays = RegularHoliday::all();

        return view('admin.restaurants.create', compact('categories', 'regular_holidays'));
    }

    // 登録機能
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
            'description' => 'required|string',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|numeric|digits:7',
            'address' => 'required|string',
            'opening_time' => 'required|date_format:H:i|before:closing_time',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        $restaurant = new Restaurant($validated);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        } else {
            $restaurant->image = '';
        }

        $restaurant->save();

        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);

        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids'));
        $restaurant->regularHolidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.index')
                            ->with('flash_message', '店舗を登録しました。');
    }

    // 編集ページ
    public function edit(Restaurant $restaurant)
    {
        $categories = Category::all();
        $category_ids = $restaurant->categories->pluck('id')->toArray();

        $regular_holidays = RegularHoliday::all();

        return view('admin.restaurants.edit', compact('restaurant', 'categories', 'category_ids', 'regular_holidays'));
    }

    // 更新機能
    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
            'description' => 'required|string',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|numeric|digits:7',
            'address' => 'required|string',
            'opening_time' => 'required|date_format:H:i|before:closing_time',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        }

        // $restaurant->update($request->all());

        // $category_ids = array_filter($request->input('category_ids'));
        // $restaurant->categories()->sync($category_ids);

        $restaurant->update($request->except('category_ids'));
        $category_ids = $request->input('category_ids', []);
        $restaurant->categories()->sync(array_filter($category_ids));

        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids'));
        $restaurant->regularHolidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.show', $restaurant->id)
                            ->with('flash_message', '店舗を編集しました。');
    }

    // 削除機能
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')
                            ->with('flash_message', '店舗を削除しました。');
    }
}
