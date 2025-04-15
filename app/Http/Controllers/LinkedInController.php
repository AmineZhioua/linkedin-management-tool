<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\LinkedinUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ScheduledLinkedinPost;
use App\Jobs\ScheduleLinkedInPost;
use Carbon\Carbon;


class LinkedInController extends Controller
{
    public function index() {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();
        
        return view('linkedin-login', [
            'user' => $user,
            'linkedinUserList' => $linkedinUserList,
        ]);
    }


    public function publish(Request $request)
    {
        $validated = $request->validate([
            'linkedin_id' => 'required|exists:linkedin_users,id',
            'type' => 'required|in:text,image,video,article',
            'content' => 'required|array',
            'scheduled_date' => 'required|date|after:now'
        ]);

        // Additional validation based on post type
        switch ($validated['type']) {
            case 'text':
                $request->validate([
                    'content.text' => 'required|string|max:3000',
                ]);
                break;
            case 'image':
            case 'video':
                $request->validate([
                    'content.asset' => 'required|string',
                    'content.caption' => 'nullable|string|max:3000',
                ]);
                break;
            case 'article':
                $request->validate([
                    'content.url' => 'required|url',  // Article URL for article posts
                    'content.title' => 'required|string|max:200',  // Article title
                    'content.description' => 'required|string|max:500',  // Article description
                    'content.caption' => 'nullable|string|max:3000',  // Optional caption for article
                ]);
                break;
            default:
                abort(400, 'Invalid post type');
        }

        // Create the scheduled post
        $post = ScheduledLinkedinPost::create([
            'user_id' => Auth::id(),
            'linkedin_user_id' => $validated['linkedin_id'],
            'type' => $validated['type'],
            'content' => json_encode($validated['content']),
            'scheduled_time' => $validated['scheduled_date'],
            'status' => 'queued'
        ]);

        // Dispatch the job to post it at the scheduled time
        ScheduleLinkedInPost::dispatch($post)->delay(Carbon::parse($validated['scheduled_date']));

        return response()->json(['message' => 'Post scheduled successfully']);
    }



    // Redirect function to redirect the user to LinkedIn
    public function redirect() {
        $client_id=config('services.linkedin.client_id');
        $url = 'https://www.linkedin.com/oauth/v2/authorization';
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $client_id,
            'redirect_uri' => route('linkedin.callback'),
            'state' => 'random_string',
            'scope' => 'email w_member_social profile openid r_basicprofile',
            'prompt' => 'login',
        ]);

        return redirect($url . '?' . $query);
    }


    // Callback function to handle the response from LinkedIn
    public function callback(Request $request) {
        try {
            // Retrieve authorization code from LinkedIn
            $code = $request->query('code');
            $state = $request->query('state');
    
            if (!$code || !$state) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', "Réponse d'autorisation LinkedIn non valide");
            }
    
            // Exchange authorization code for an access token
            $client_id = config('services.linkedin.client_id');
            $client_secret = config('services.linkedin.client_secret');
            $redirect_uri = route('linkedin.callback');
    
            $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirect_uri,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
            ]);
    
            if ($response->failed()) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite! Réessayer plus tard.');
            }
    
            $data = $response->json();
            $access_token = $data['access_token'] ?? null;
            $refresh_token = $data['refresh_token'] ?? null;
    
            if (!$access_token) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite! Veuillez réessayer plus tard.');
            }
    
            // Fetch LinkedIn user profile
            $profileResponse = Http::withHeaders([
                'Authorization' => "Bearer $access_token",
            ])->get('https://api.linkedin.com/v2/me?projection=(id,localizedFirstName,localizedLastName,profilePicture(displayImage~:playableStreams))');
    
            if ($profileResponse->failed()) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite lors de la récupération des données.');
            }
    
            $linkedinUser = $profileResponse->json();
            $linkedin_id = $linkedinUser['id'] ?? null;
            $linkedin_firstname = $linkedinUser['localizedFirstName'] ?? null;
            $linkedin_lastname = $linkedinUser['localizedLastName'] ?? null;
            $linkedin_picture = $linkedinUser['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] ?? null;
    
            if (!$linkedin_id || !$linkedin_firstname || !$linkedin_lastname) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite lors de la récupération des données.');
            }
    
            // Link LinkedIn account to the authenticated user
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->route('login');
            }

            // Check if the LinkedIn ID is already linked to another user
            $existingLinkedinUser = LinkedinUser::where('linkedin_id', $linkedin_id)->first();

            if ($existingLinkedinUser && $existingLinkedinUser->user_id !== $userId) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Ce compte LinkedIn est déjà lié à un autre utilisateur.');
            }

            if ($existingLinkedinUser) {
                // Update the existing LinkedIn record for this user
                $existingLinkedinUser->update([
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_picture' => $linkedin_picture,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                ]);
            } else {
                // Create a new LinkedIn account entry for the user
                LinkedinUser::create([
                    'user_id' => $userId,
                    'linkedin_id' => $linkedin_id,
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_picture' => $linkedin_picture,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                ]);
            }
    
            return redirect()->route('login-linkedin')
                ->with('linkedin_success', 'Votre compte LinkedIn a été lié avec succès !');
    
        } catch (\Exception $e) {
            return redirect()->route('login-linkedin')
                ->with('linkedin_error', 'Une erreur s\'est produite!');
        }
    }
    

    // Function to unlink the LinkedIn account from the user
    public function logout()
    {
        return redirect('https://www.linkedin.com/m/logout');
    }

    // Function to delete the LinkedIn account from the user
    public function delete(Request $request) {
        $linkedinId = $request->query('linkedin_id');
        $userId = Auth::id();

        if (!$linkedinId || !$userId) {
            return redirect()->route('login-linkedin')->with('linkedin_error', 'Invalid request.');
        }

        $linkedinUser = LinkedinUser::where('user_id', $userId)->where('linkedin_id', $linkedinId)->first();

        if ($linkedinUser) {
            $linkedinUser->delete();
            return redirect()->route('login-linkedin')->with('linkedin_success', 'Compte LinkedIn supprimé avec succès.');
        }

        return redirect()->route('login-linkedin')->with('linkedin_error', 'Compte LinkedIn introuvable.');
    }



    // Function to post only text in LinkedIn
    public function postTextOnly(array $data)
    {
        $postUrl = "https://api.linkedin.com/v2/ugcPosts";

        $validated = $data;
        $authorUrn = 'urn:li:person:' . $validated['linkedin_id'];
        
        $postData = [
            "author" => $authorUrn,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $validated['caption']
                    ],
                    "shareMediaCategory" => "NONE"
                ]
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC"
            ]
        ];

        $headers = [
            "Authorization: Bearer " . $validated['token'],
            "Content-Type: application/json",
            "X-Restli-Protocol-Version: 2.0.0"
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $postUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $errorMsg = curl_error($curl);
        curl_close($curl);

        return [
            'status' => $httpCode,
            'data' => json_decode($response),
            'error' => $errorMsg,
        ];
    }

    // Function to Register Media (Image/Video) in LinkedIn
    public function registerMedia(Request $request) {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'linkedin_id' => 'required|string',
                'type' => 'required|in:image,video',
                'media' => 'required|file|max:50000', // 50MB max file size
                'caption' => 'nullable|string|max:3000'
            ]);
    
            // WE CAN DELETE THIS 
            // #####################
            $file = $request->file('media');
            $mimeType = $file->getMimeType();
    
            // Validate file type
            $allowedMimeTypes = [
                'image' => ['image/jpeg', 'image/png', 'image/gif'],
                'video' => ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/webm']
            ];
    
            if (!in_array($mimeType, $allowedMimeTypes[$validated['type']])) {
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid file type for the selected media type'
                ], 400);
            }
            // #####################
    
            $authorUrn = 'urn:li:person:' . $validated['linkedin_id'];
    
            // Determine recipe based on media type
            $recipe = $validated['type'] === 'image' 
                ? "urn:li:digitalmediaRecipe:feedshare-image"
                : "urn:li:digitalmediaRecipe:feedshare-video";
    
            $registerData = [
                "registerUploadRequest" => [
                    "recipes" => [$recipe],
                    "owner" => $authorUrn,
                    "serviceRelationships" => [
                        [
                            "relationshipType" => "OWNER",
                            "identifier" => "urn:li:userGeneratedContent"
                        ]
                    ]
                ]
            ];
    
            $headers = [
                "Authorization: Bearer " . $validated['token'],
                "Content-Type: application/json",
                "X-Restli-Protocol-Version: 2.0.0"
            ];
    
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.linkedin.com/v2/assets?action=registerUpload",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($registerData),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30
            ]);
    
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $responseData = json_decode($response, true);
    
            curl_close($curl);
    
            Log::info('LinkedIn Asset Registration Response', [
                'http_code' => $httpCode,
                'response' => $responseData
            ]);
    
            // Validate successful response
            if ($httpCode == 200 && isset($responseData['value'])) {
                $uploadMechanism = $responseData['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest'] ?? null;
                
                if ($uploadMechanism && isset($uploadMechanism['uploadUrl'])) {
                    return response()->json([
                        'status' => 200,
                        'uploadUrl' => $uploadMechanism['uploadUrl'],
                        'asset' => $responseData['value']['asset'],
                        'mediaArtifact' => $responseData['value']['mediaArtifact']
                    ]);
                }
            }
    
            // Handle unsuccessful response
            return response()->json([
                'status' => $httpCode,
                'error' => 'Failed to register media',
                'raw_response' => $responseData
            ], 400);
    
        } catch (\Exception $e) {
            Log::error('LinkedIn Media Registration Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'status' => 500,
                'error' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }


    // Function to Upload Binary File Before Posting Media
    public function uploadMediaBinary(Request $request) {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'upload_url' => 'required|url',
                'media' => 'required|file|max:50000' // 50MB max file size
            ]);
    
            $file = $request->file('media');
    
            $curl = curl_init();
    
            curl_setopt_array($curl, [
                CURLOPT_URL => $validated['upload_url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => file_get_contents($file->getRealPath()),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $validated['token'],
                    'Content-Type: ' . $file->getMimeType(),
                    'Content-Length: ' . $file->getSize()
                ],
                CURLOPT_HEADER => true
            ]);
    
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
    
            curl_close($curl);
    
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Media uploaded successfully',
                    'headers' => $headers
                ]);
            }
    
            // Handle upload failure
            return response()->json([
                'status' => $httpCode,
                'error' => 'Media upload failed',
                'response' => $body
            ], $httpCode);
    
        } catch (\Exception $e) {
            Log::error('LinkedIn Media Binary Upload Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'status' => 500,
                'error' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }


    // Function To Share Media on LinkedIn
    public function shareMedia(array $data)
    {
        // Validate only if called from an HTTP POST request
        if (request()->isMethod('post')) {
            $validated = validator($data, [
                'token' => 'required|string',
                'linkedin_id' => 'required|string',
                'asset' => 'required|string',
                'caption' => 'nullable|string',
                'media_type' => 'required|in:IMAGE,VIDEO'
            ])->validate();
        } else {
            // Use the provided data directly (e.g., from a job)
            $validated = $data;
        }

        // LinkedIn API endpoint
        $postUrl = "https://api.linkedin.com/v2/ugcPosts";
        $authorUrn = 'urn:li:person:' . $validated['linkedin_id'];

        // Construct the post data
        $postData = [
            "author" => $authorUrn,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $validated['caption'] ?? ''
                    ],
                    "shareMediaCategory" => $validated['media_type'],
                    "media" => [
                        [
                            "status" => "READY",
                            "description" => [
                                "text" => $validated['caption'] ?? ''
                            ],
                            "media" => $validated['asset'],
                            "title" => [
                                "text" => "Shared Media" // You can make this dynamic if needed
                            ]
                        ]
                    ]
                ]
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC"
            ]
        ];

        // Set up headers
        $headers = [
            "Authorization: Bearer " . $validated['token'],
            "Content-Type: application/json",
            "X-Restli-Protocol-Version: 2.0.0"
        ];

        // Make the API call using cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $postUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseData = json_decode($response, true);
        curl_close($curl);

        // Handle the response
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'status' => 'success',
                'http_code' => $httpCode,
                'data' => $responseData,
            ];
        } else {
            Log::error('LinkedIn Share Media Error', [
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);
            return [
                'status' => 'error',
                'http_code' => $httpCode,
                'error' => $responseData['message'] ?? 'Unknown error',
            ];
        }
    }


    // Function to Share Article on LinkedIn
    public function shareArticle(array $data)
    {
        // Validate only if called from an HTTP POST request
        if (request()->isMethod('post')) {
            $validated = validator($data, [
                'token' => 'required|string',
                'linkedin_id' => 'required|string',
                'caption' => 'nullable|string|max:3000',
                'article_url' => 'required|url',
                'article_title' => 'required|string|max:200',
                'article_description' => 'required|string|max:500',
            ])->validate();
        } else {
            // Use the provided data directly (e.g., from the job)
            $validated = $data;
        }

        $postUrl = "https://api.linkedin.com/v2/ugcPosts";
        $authorUrn = 'urn:li:person:' . $validated['linkedin_id'];

        $postData = [
            "author" => $authorUrn,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $validated['caption'] ?? ''
                    ],
                    "shareMediaCategory" => "ARTICLE",
                    "media" => [
                        [
                            "status" => "READY",
                            "description" => [
                                "text" => $validated['article_description'] ?? ''
                            ],
                            "originalUrl" => $validated['article_url'],
                            "title" => [
                                "text" => $validated['article_title'] ?? ''
                            ],
                        ]
                    ]
                ]
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC"
            ]
        ];

        $headers = [
            "Authorization: Bearer " . $validated['token'],
            "Content-Type: application/json",
            "X-Restli-Protocol-Version: 2.0.0"
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $postUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseData = json_decode($response, true);
        curl_close($curl);

        if ($httpCode >= 200 && $httpCode < 300) {
            $result = [
                'status' => 'success',
                'http_code' => $httpCode,
                'data' => $responseData,
            ];
        } else {
            Log::error('LinkedIn Share Article Error', [
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);
            $result = [
                'status' => 'error',
                'http_code' => $httpCode,
                'error' => $responseData['message'] ?? 'Unknown error',
            ];
        }

        // If called from an HTTP request, return a JSON response
        if (request()->isMethod('post')) {
            return response()->json($result, $httpCode >= 200 && $httpCode < 300 ? 200 : 400);
        }

        // Otherwise, return the array for the job to process
        return $result;
    }
}