<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected function search(Request $request)
    {
        $type_id = $request->type_id;
        $query = Category::query();

        if ($type_id)
            $query = $query->where('type_id', $type_id);

        return $query->get();
    }


    protected function create(Request $request)
    {
        $newItem = $request->new_item;
        $typeId = $request->type_id;

        $user = auth()->user();

        if (!$user->can('createItem', [User::class, Blog::class, false]))
            return response()->json('مجاز نیستید', 422);

        if (strlen($newItem) > 0 && strlen($newItem) < 100)
            if (!Category::where('name', $newItem)->exists())
                Category::create(['name' => $newItem, 'type_id' => $typeId]);


    }

}
