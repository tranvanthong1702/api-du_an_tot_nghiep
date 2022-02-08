<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // danh sách các sp chưa bị xóa mềm
    public function index()
    {
        $products = Product::all();
        if ($products->all()) {
            $products->load('category');
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Chưa có sản phẩm nào trong dữ liệu'
        ]);
    }
    // filter product admin
    public function filterAdmin(Request $request)
    {
        $product = new Product();
        if ($request->keyword) {
            $product = $product->where('name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->cate_id) {
            $product = $product->where('cate_id', $request->cate_id);
        }
        if ($request->sale != null && $request->sale == 0) {
            $product = $product->whereNull('sale');
        }
        if ($request->sale != null && $request->sale == 1) {
            $product = $product->whereNotNull('sale');
        }
        if ($request->expiration_date != null && $request->expiration_date == 0) {
            $product = $product->whereDate('expiration_date', '<', Carbon::now()->toDateString());
        }
        if ($request->expiration_date != null && $request->expiration_date == 1) {
            $product = $product->whereDate('expiration_date', '>=', Carbon::now()->toDateString());
        }
        if ($request->sort == 1) {
            $product = $product->orderByDesc('id'); //mới nhất
        }
        if ($request->sort == 2) {
            $product = $product->orderBy('id'); //cũ nhất
        }
        if ($request->sort == 3) {
            $product = $product->orderBy('name'); //tăng theo anpha
        }
        if ($request->sort == 4) {
            $product = $product->orderByDesc('name'); //giảm theo anpha
        }
        $products = $product->get();
        if ($products->all()) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'ko có sản phẩm phù hợp'
            ]);
        }
    }

    // filter product user
    public function filterUser(Request $request)
    {
        $product = new Product();
        if ($request->keyword) {
            $product = $product->where('name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->cate_id) {
            $product = $product->where('cate_id', $request->cate_id);
        }
        if ($request->sale != null && $request->sale == 0) {
            $product = $product->whereNull('sale');
        }
        if ($request->sale != null && $request->sale == 1) {
            $product = $product->whereNotNull('sale');
        }
        if ($request->sort == 1) {
            $product = $product->orderByDesc('id'); //mới nhất
        }
        if ($request->sort == 2) {
            $product = $product->orderBy('id'); //cũ nhất
        }
        if ($request->sort == 3) {
            $product = $product->orderBy('price'); //tăng theo giá
        }
        if ($request->sort == 4) {
            $product = $product->orderByDesc('price'); //giảm theo giá
        }
        $products = $product->get();
        if ($products->all()) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'ko có sản phẩm phù hợp'
            ]);
        }
    }

    //list_comments
    public function list_comments($pro_id)
    {
        $product = Product::query()->find($pro_id);
        if ($product) {
            $product->load('comments');
            return response()->json([
                'success' => true,
                'data' => $product->comments,

            ]);
        }
    }

    // thêm mới 1 sp
    public function store(ProductRequest $request)
    {
        $product = new Product();
        $product->fill($request->all());
        $product->save();
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    // cập nhật 1 sp
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->fill($request->all());
            $product->save();
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'sản phẩm không tồn tại'
            ]);
        }
    }
    // chi tiết 1 sp
    public function show($id)
    {
        $product = Product::withTrashed()->find($id);
        if ($product) {
            $product->load('category');
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Sản phẩm chưa tồn tại'
            ]);
        }
    }

    // xóa mềm 1 sp
    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'sản phẩm không tồn tại'
            ]);
        }
    }

    // xóa vĩnh viễn 1 sp
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->find($id);
        if ($product) {
            $product->forceDelete();
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'sản phẩm không tồn tại'
            ]);
        }
    }

    // xóa vĩnh viễn tất cả các sp đã bị xóa mềm
    public function forceDeleteAll()
    {

        $products = Product::onlyTrashed()->get();
        foreach ($products as $product) {
            $product->forceDelete();
        }
        return response()->json([
            'success' => true,
            'data' => 'xóa thành công'
        ]);
    }


    // danh sách các sp đã bị xóa mềm
    public function trashed()
    {
        $products = Product::onlyTrashed()->get();
        if ($products->all()) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Chưa có sản phẩm bị xóa trong dữ liệu'
            ]);
        }
    }


    // backup 1 sp đã xóa mềm
    public function backupOne($id)
    {
        $product = Product::onlyTrashed()->find($id);
        if ($product) {
            $product->restore();
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Sản phẩm chưa tồn tại'
            ]);
        }
    }
    // backup tất cả các sp đã xóa mềm
    public function backupAll()
    {
        $products = Product::onlyTrashed()->get();
        foreach ($products as $pro) {
            $pro->restore();
        }
        return response()->json([
            'success' => true,
            'data' => 'backup thành công'
        ]);
    }
}
