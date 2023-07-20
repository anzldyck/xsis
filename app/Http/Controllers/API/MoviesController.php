<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Movies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MoviesController extends Controller
{
    public function getList()
    {
        try {
            $result = Movies::all();
            if(count($result) == 0) {
                return response()->json([
                    'message'   => 'movies unavailable',
                    'status'    => 204
                ]);
            }

            return response()->json([
                'message'   => 'success',
                'status'    => 200,
                'data'      => $result
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message'   => 'error',
                'status'    => 500,
                'error'     => $error
            ]);
        }
    }

    public function detailsMovie($id)
    {
        try {
            $result = Movies::find($id);
            if(!$result) {
                return response()->json([
                    'message'   => 'Movie not found',
                    'status'    => 404
                ]);
            }
            return response()->json([
                'message'   => 'success',
                'status'    => 200,
                'data'      => $result
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message'   => 'error',
                'status'    => 500,
                'error'     => $error
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'     => 'required|string|max:100',
                'description'   => 'string|max:255',
                'rating'    => 'required|numeric|min:1|max:50',
                'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
    
            $image = $request->file('image');
            $image ?  $image = $image->storeAs('public/posts', $image->hashName()) : '';
            DB::beginTransaction();
            $data = Movies::create([
                'title' => $request->title,
                'description' => $request->description,
                'rating'    => $request->rating,
                'image'     => $image
            ]);
            DB::commit();
        
            return response()->json([
                'message'   => 'success',
                'status'    => 201,
                'data'      => $data
            ]);

        } catch (Exception $error) {
            DB::rollBack();
            return response()->json([
                'message'   => 'error',
                'status'    => 500,
                'error'     => $error
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'     => 'string|max:100',
                'description'   => 'string|max:255',
                'rating'    => 'numeric|min:1|max:50',
                'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $movie = Movies::find($id);
            if(!$movie) {
                return response()->json([
                    'message'   => 'movie not found',
                    'status'    => 404,
                ]);
            }
            $image = $request->file('image');
            $image ?  $image->storeAs('public/posts', $image->hashName()) : '';
            $movie->image = $image;
            DB::beginTransaction();
            $movie->update($request->all());
            DB::commit();

            return response()->json([
                'message'   => 'success',
                'status'    => 201,
                'data'      => $movie
            ]);
        } catch (Exception $error) {
            DB::rollBack();
            return response()->json([
                'message'   => 'error',
                'status'    => 500,
                'error'     => $error
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $result = Movies::find($id);
            if(!$result) {
                return response()->json([
                    'message'   => 'Movie not found',
                    'status'    => 404
                ]);
            }
            Storage::delete('public/posts/'.basename($result->image));
            $result->delete();
            return response()->json([
                'message'   => 'Delete movie success',
                'status'    => 200
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message'   => 'error',
                'status'    => 500,
                'error'     => $error
            ]);
        }
    }
}
