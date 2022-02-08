<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $keyword=$request->input('keyword');
        $sort=$request->input('sort');
        $pro_id=$request->input('pro_id');
        $query= new Comment();
        if($keyword){
            $query=$query->where('content','like','%'.$keyword.'%');
        }
        if($sort){
            $query=$query->orderBy('created_at',$sort);
        }
         if($pro_id){
             $query=$query->orderBy('pro_id',$pro_id);
        }
        $comment=$query->get();

        if ($comment->all()) {
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }

    public function store(CommentRequest $request)
    {
        $comment = new Comment();
        $comment->fill($request->all());
        $comment->save();
        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }


    public function update(CommentRequest $request, $id)
    {
        $comment = Comment::find($id);
        if ($comment) {
            $comment->fill($request->all());
            $comment->save();
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }

    public function show($id)
    {
        $comment = Comment::withTrashed()->find($id);
        if ($comment) {
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }

    public function delete($id)
    {
        $comment = Comment::find($id);
        if ($comment) {
            $comment->delete();
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }


    public function forceDelete($id)
    {
        $comment = Comment::withTrashed()->find($id);
        if ($comment) {
            $comment->forceDelete();
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }

    public function forceDeleteAll()
    {

        $comments = Comment::onlyTrashed()->get();
        foreach ($comments as $comment) {
            $comment->forceDelete();
        }
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }


    public function trashed()
    {
        $comment = Comment::onlyTrashed()->get();
        if ($comment->all()) {
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }

    public function backupOne($id)
    {
        $comment = Comment::onlyTrashed()->find($id);
        if ($comment) {
            $comment->restore();
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }

    public function backupAll()
    {
        $comment = Comment::onlyTrashed()->get();
        foreach ($comment as $cate) {
            $cate->restore();
        }
        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }
}
