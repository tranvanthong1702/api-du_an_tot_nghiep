<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $sort = $request->input('sort');
        $sort_name = $request->input('sort_name');
        $status = $request->input('status');
        $query = new Category;
        if ($keyword) {
            $query = $query->where('name', 'like', '%' . $keyword . '%');
        }
        if ($sort) {
            $query = $query->orderBy('created_at', $sort);
        }
        if ($sort_name) {
            $query = $query->orderBy('name', $sort_name);
        }
        if ($status) {
            $query = $query->where('status', '=', $status);
        }
        $category = $query->get();
        if ($category->all()) {
            $category->load('products');
            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);
        }
    }
    //list_pro
    public function list_pro($cate_id)
    {
        $category = Category::query()->find($cate_id);
        if ($category) {
            $category->load('products');
            return response()->json([
                'success' => true,
                'data' => $category->products,
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }


    public function store(CategoryRequest $request)
    {
        $categories = new Category();
        $categories->fill($request->all());
        $categories->save();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function update(Request $request, $id)
    {
        $categories = Category::find($id);
        if ($categories) {
            $categories->fill($request->all());
            $categories->save();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);
        }
    }

    public function delete($id)
    {
        $categories = Category::find($id);
        if ($categories) {
            $categories->delete();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);
        }
    }

    public function show($id)
    {
        $categories = Category::withTrashed()->find($id);
        if ($categories) {
            $categories->load('products');
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);
        }
    }
    //xoas vv 1
    public function forceDelete($id)
    {
        $categories = Category::withTrashed()->find($id);
        if ($categories) {
            $categories->forceDelete();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);
        }
    }

    // xoa vv all
    public function forceDeleteAll()
    {

        $categoriess = Category::onlyTrashed()->get();
        foreach ($categoriess as $categories) {
            $categories->forceDelete();
        }
        return response()->json([
            'success' => true,
            'data' => 'xóa thành công'
        ]);
    }



    public function trashed()
    {
        $categories = Category::onlyTrashed()->get();
        if ($categories->all()) {
            $categories->load('products');
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);
        }
    }


    public function backupOne($id)
    {
        $categories = Category::onlyTrashed()->find($id);
        if ($categories) {
            $categories->restore();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);
        }
    }

    public function backupAll()
    {
        $categories = Category::onlyTrashed()->get();
        foreach ($categories as $cate) {
            $cate->restore();
        }
        return response()->json([
            'success' => true,
            'data' => 'back up thành công'
        ]);
    }
}
