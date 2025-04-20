<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\LinkedinUser;
use Illuminate\Http\Request;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Support\Facades\Storage;

class LinkedinPostController extends Controller
{
    public function index() {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();

        return view('linkedin-post', ['linkedinUserList' => $linkedinUserList]);
    }


    // FUNCTION TO DELETE A POST
    public function deletePost(Request $request) {
        $validated = $request->validate([
            'post_id' => 'required|integer|exists:scheduled_linkedin_posts,id',
            'linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
        ]);

        $postToDelete = ScheduledLinkedinPost::where('id', $validated['post_id'])
            ->where('linkedin_user_id', $validated['linkedin_user_id'])
            ->first();
        
        if($postToDelete) {
            // Delete associeted media if it's an image or video
            if ($postToDelete->type === 'image' || $postToDelete->type === 'video') {
                $content = json_decode($postToDelete->content, true);
                if (isset($content['file_path'])) {
                    Storage::disk('linkedin_media')->delete($content['file_path']);
                }
            }

            $postToDelete->delete();
            
            return response()->json([
                'message' => 'Post supprimé avec succès !',
                'success' => true
            ], 200); 
        }

        return response()->json(['success' => false], 404);
    }


    // FUNCTION TO UPDATE A POST
    public function updatePost(Request $request) {
        $validated = $request->validate([
            'post_id' => 'required|integer|exists:scheduled_linkedin_posts,id',
            'linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
            'type' => 'required|string|in:text,image,video,article',
            'scheduled_time' => 'required|date|after:now',
            'content' => 'required|string',
        ]);
    
        $postToUpdate = ScheduledLinkedinPost::where('id', $validated['post_id'])
            ->where('linkedin_user_id', $validated['linkedin_user_id'])
            ->first();
    
        if ($postToUpdate) {
            $postToUpdate->type = $validated['type'];
            $postToUpdate->scheduled_time = $validated['scheduled_time'];
            $content = $validated['content']; // Start with the validated content
    
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('', 'linkedin_media');
                $contentArray = json_decode($content, true); // Decode to modify
                $contentArray['file_path'] = $filePath;
                $content = json_encode($contentArray); // Update content with file path
            }
    
            $postToUpdate->content = $content; // Set content once
            $postToUpdate->save();
    
            return response()->json([
                'message' => 'Post mis à jour avec succès !',
                'success' => true
            ], 200);
        }
    
        return response()->json([
            'message' => 'Post non trouvé !',
            'success' => false
        ], 404);
    }
}
