<?php

namespace App\Http\Controllers;
use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;


class BlogController extends Controller
{
     public function index(Request $request){
        $keyword=$request->input('keyword');
        $sort=$request->input('sort');
        $sort_name=$request->input('sort_name');
        $query= new Blog;
        if($keyword){
            $query=$query->where('title','like','%'.$keyword.'%');
        }
        if($sort){
            $query=$query->orderBy('created_at',$sort);
        }
         if($sort_name){
             $query=$query->orderBy('title',$sort_name);
        }
        $blog=$query->get();
        if ($blog->all()) {
            return response()->json([
                'success' => true,
                'data' => $blog
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }

    }
    public function store(BlogRequest $request){
         $model = new Blog();
         $model->fill($request->all());
         $model->save();
         return response()->json([
         'success' => true,
         'data' => $model
         ]);
    }
    public function update(BlogRequest $request,$id){
         $blog=Blog::query()->find($id);
         if($blog){
             $blog->fill($request->all());
             $blog->save();
             return response()->json([
                     'success'=>true,
                     'data'=>$blog
                 ]);
         }
         return response()->json([
                 'success'=>false,
             ]);
    }
    public function show($id){
         $blog = Blog::query()->find($id);
         if($blog){
             return response()->json([
                'success' => true,
                'data' => $blog
            ]);
         }return response()->json([
              'success'=>false,
         ]);
    }
    //xoa mem
    public function destroy($id){
        $blog=Blog::find($id);

        if($blog){
            $blog->delete();
            return response()->json([
                'success' => true,
                'data' => $blog
            ]);
        } return response()->json([
                'success' => false,
                'data' => 'no data'
            ]);

    }
    //xoa vv 1
    public function forceDelete($id){
        $blog=Blog::withTrashed()->find($id);
        if($blog){
            $blog->forceDelete();
            return response()->json([
                'success' => true,
                'data' => $blog
            ]);
        }return response()->json([
              'success'=>false,
         ]);
    }
    //xoa vv all
    public function forceDeleteAll(){
        $blog=Blog::onlyTrashed()->get();
        foreach($blog as $blog){
            $blog->forceDelete();
        }
         return response()->json([
                'success' => true,
                'data' => $blog
            ]);
    }
    //list da bi xoa mem
    public function trashed(){
        $blog=Blog::onlyTrashed()->get();
        if($blog->all()){
           return response()->json([
                'success' => true,
                'data' => $blog
            ]);
        }return response()->json([
             'success' => false,
             'data' => 'no data'

        ]);

    }
    //restore 1
    public function backupOne($id){
         $blog=Blog::onlyTrashed()->find($id);
         if($blog){
             $blog->restore();
             return response()->json([
                'success' => true,
                'data' => $blog
            ]);
         }return response()->json([
              'success'=>false,
         ]);
    }
    //restore all
    public function backupAll(){
         $blog=Blog::onlyTrashed()->get();
        foreach($blog as $bl){
            $bl->restore();
        }
         return response()->json([
                'success' => true,
                'data' => $blog
            ]);

    }

}
