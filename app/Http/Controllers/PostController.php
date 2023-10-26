<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //
    public function getPosts(Request $request) {
        try {
            // return all posts present
            $posts = Post::query();

            // posts can be filtered by user id
            if($request->query('user') && !empty($request->query('user'))) {
                $user = User::find($request->query('user'));
                
                if(!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User not found'
                    ], 404);
                }

                if(count($user->posts) == 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'There are no posts for this user.'
                    ], 404);
                }

                return response($user->posts); // query and display posts according to a specific user.
            }

            // posts can also be searched by their title or content
            if ($request->has('search') && !empty($request->input('search'))) {
                $posts->where(function ($posts) use ($request) {
                    $posts->where('title', 'LIKE', '%' . $request->input('search') . '%')
                        ->orWhere('content', 'LIKE', '%' . $request->input('search') . '%');
                });
            }

            // display a message if there are no posts.
            if(empty($posts->get())) {
                return response()->json([
                    'status' => false,
                    'message' => 'There are no posts.'
                ], 404);
            }
            
            return response($posts->get());
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }    
    }

    public function getSpecificPost(Request $request, $id) {
        try {
            // return a message if a post ID does not exist
            if(!Post::find($id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found.'
                ], 404);
            }
            else {
                // display the post that has the ID 
                return response(Post::find($id), 200);
            }
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }        
    }

    public function createPost(Request $request) {
        try {
            // validate the incoming request data before creating a post
            $validateRequest = Validator::make($request->all(), [
                'title' => 'required|max:256',
                'content' => 'required|max:1048'
            ]);

            // return a message if validation fails
            if($validateRequest->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request validation error.',
                    'errors' => $validateRequest->errors()
                ], 401);
            }

            // create post and store it in the database
            $createPost = Post::create([
                'user_email' => Auth::user()->email,
                'user_id' => Auth::user()->id,
                'title' => $request->input('title'),
                'content' => $request->input('content')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Post created successfully.',
                'created_post' => $createPost
            ], 200);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }            
    }

    public function updatePost(Request $request, $id) {
        try {
            // check if desired post to be updated is present. display message if not
            $post = Post::find($id);

            if(!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found.'
                ], 404);
            }
            else {
                // check if the post is owned by the current logged in user. 
                if($post->user_id != Auth::user()->id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'The post is not yours. You can only edit your own posts.'
                    ], 401);
                }

                // validate the incoming request data. display message if validation fails
                $validatedRequest = Validator::make($request->all(), [
                    'title' => 'required|max:256',
                    'content' => 'required|max:1048'
                ]);

                if($validatedRequest->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Request validation error.',
                        'errors' => $validatedRequest->errors()
                    ], 401);
                };

                // update post in the database
                $post->update([
                    'title' => $request->input('title'),
                    'content' => $request->input('content')
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Post has been updated successfully.'
                ], 200);            
            }
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }    
    }

    public function deletePost(Request $request, $id) {
        try {
            // check if the post to be deleted is present. 
            $post = Post::find($id);        

            if(!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found.'
                ], 404);
            }
            else {            
                $post->delete(); // delete post

                return response()->json([
                    'status' => true,
                    'message' => 'Post deleted successfully.'
                ], 200);
            }
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }    
    }
}
