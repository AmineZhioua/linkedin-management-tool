<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkedinUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ScheduledLinkedinPost;
use App\Jobs\ScheduleLinkedInPost;
use App\Models\LinkedinCampaign;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class LinkedInController extends Controller
{
    /**
     * Display the LinkedIn login page with the user's linked accounts.
     */
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

    public function createCampaign(Request $request) {
        try {
            $validated = $request->validate([
                'linkedin_id' => 'required|exists:linkedin_users,id',
                'name' => 'required|string',
                'description' => 'required|string',
                'target_audience' => 'nullable|string',
                'frequency_per_day' => 'required|integer|min:1',
                'couleur' => 'required|string',
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
            ]);
    
            $campaign = LinkedinCampaign::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'linkedin_user_id' => $validated['linkedin_id'],
                    'name' => $validated['name'] ?? 'Campaign ' . now()->toDateTimeString(),
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ],
                [
                    'description' => $validated['description'],
                    'target_audience' => $validated['target_audience'],
                    'frequency_per_day' => $validated['frequency_per_day'],
                    'color' => $validated['couleur'],
                    'status' => 'scheduled',
                ]
            );
    
            return response()->json([
                'id' => $campaign->id,
                'status' => 201,
                'message' => 'Campaign created or retrieved successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating campaign', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['error' => 'Une erreur s\'est produite lors de la création de cotre campagne ! ' . $e], 500);
        }
    }

    /**
     * Schedule a LinkedIn post with token expiration and validation.
     */
    public function publish(Request $request) {
        $user = Auth::user();
        if (!$user->post_perm) {
            return response()->json([
                'status' => 403,
                'error' => 'You don\'t have permission to schedule a post'
            ], 403);
        }
        
        $validated = $request->validate([
            'linkedin_id' => 'required|exists:linkedin_users,id',
            'type' => 'required|in:text,image,video,article',
            'scheduled_date' => 'required|date|after:now',
            'content' => 'required|array',
            'campaign_id' => 'required|exists:linkedin_campaigns,id',
        ]);
        
        switch ($validated['type']) {
            case 'text':
                $request->validate(['content.text' => 'required|string|max:3000']);
                $content = ['text' => $validated['content']['text']];
                break;

            case 'image':
            case 'video':
                $request->validate([
                    'content.file' => 'required|file|max:50000',
                    'content.caption' => 'nullable|string',
                    'content.original_filename' => 'required|string',
                ]);
                $file = $request->file('content.file');
                $path = $file->store('', 'linkedin_media');
                Log::info('Stored LinkedIn media', [
                    'path' => $path,
                    'full_path' => Storage::disk('linkedin_media')->path($path)
                ]);
                $content = [
                    'file_path' => $path,
                    'caption' => $validated['content']['caption'] ?? '',
                    'original_filename' => $validated['content']['original_filename'],
                ];
                break;

            case 'article':
                $request->validate([
                    'content.url' => 'required|url',
                    'content.title' => 'required|string|max:200',
                    'content.description' => 'nullable|string|max:500',
                    'content.caption' => 'nullable|string',
                ]);
                $content = [
                    'url' => $validated['content']['url'],
                    'title' => $validated['content']['title'],
                    'description' => $validated['content']['description'],
                    'caption' => $validated['content']['caption'] ?? '',
                ];
                break;
                
            default:
                return response()->json(['error' => 'Invalid post type'], 400);
        }

        $campaign = LinkedinCampaign::findOrFail($validated['campaign_id']);

        $post = ScheduledLinkedinPost::create([
            'user_id' => Auth::id(),
            'linkedin_user_id' => $validated['linkedin_id'],
            'campaign_id' => $campaign->id,
            'type' => $validated['type'],
            'content' => json_encode($content),
            'scheduled_time' => $validated['scheduled_date'],
            'status' => 'queued',
        ]);

        ScheduleLinkedInPost::dispatch($post)->delay(Carbon::parse($validated['scheduled_date']));
        
        if (config('queue.default') === 'database') {
            $jobRecord = DB::table('jobs')
                ->where('payload', 'like', '%ScheduleLinkedInPost%')
                ->where('payload', 'like', '%'.$post->id.'%')
                ->orderBy('id', 'desc')
                ->first();
                
            if ($jobRecord) {
                $post->update(['job_id' => $jobRecord->id]);
            }
        }

        return response()->json(['message' => 'Post planifié avec succès']);
    }
    /**
     * Redirect the user to LinkedIn for authorization.
     */
    public function redirect() {
        $client_id = config('services.linkedin.client_id');
        $url = 'https://www.linkedin.com/oauth/v2/authorization';
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $client_id,
            'redirect_uri' => route('linkedin.callback'),
            'state' => 'random_string',
            'scope' => 'email w_member_social profile openid r_basicprofile r_1st_connections_size',
            'prompt' => 'login',
        ]);

        return redirect($url . '?' . $query);
    }

    /**
     * Handle the callback from LinkedIn and link the account.
     */

    public function callback(Request $request) {
        try {
            $code = $request->query('code');
            $state = $request->query('state');

            if (!$code || !$state) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', "Réponse d'autorisation LinkedIn non valide");
            }

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

            $userId = Auth::id();
            if (!$userId) {
                return redirect()->route('login');
            }

            $existingLinkedinUser = LinkedinUser::where('linkedin_id', $linkedin_id)->first();

            if ($existingLinkedinUser && $existingLinkedinUser->user_id !== $userId) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Ce compte LinkedIn est déjà lié à un autre utilisateur.');
            }

            // Set expire_at to two months from now using Carbon
            $expireAt = Carbon::now()->addMonths(2);

            if ($existingLinkedinUser) {
                $existingLinkedinUser->update([
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_picture' => $linkedin_picture,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                    'expire_at' => $expireAt,
                ]);
            } else {
                LinkedinUser::create([
                    'user_id' => $userId,
                    'linkedin_id' => $linkedin_id,
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_picture' => $linkedin_picture,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                    'expire_at' => $expireAt,
                ]);
            }

            return redirect()->route('login-linkedin')
                ->with('linkedin_success', 'Votre compte LinkedIn a été lié avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('login-linkedin')
                ->with('linkedin_error', 'Une erreur s\'est produite!');
        }
    }


    /**
     * Log out from LinkedIn.
     */
    public function logout() {
        return redirect('https://www.linkedin.com/m/logout');
    }

    /**
     * Delete a linked LinkedIn account.
     */
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

    /**
     * Post text-only content to LinkedIn.
     */
    public function postTextOnly(array $data) {
        set_time_limit(1800);
        $postUrl = "https://api.linkedin.com/v2/ugcPosts";
        $authorUrn = 'urn:li:person:' . $data['linkedin_id'];

        $postData = [
            "author" => $authorUrn,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $data['caption']
                    ],
                    "shareMediaCategory" => "NONE"
                ]
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC"
            ]
        ];

        $headers = [
            "Authorization: Bearer " . $data['token'],
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
            CURLOPT_TIMEOUT => 1800
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $errorMsg = curl_error($curl);

        $responseData = json_decode($response, true);
        $apiData = $responseData['response'] ?? [];
        $urn = $apiData['data']['stdClass']['id'] ?? null;
        curl_close($curl);


        return [
            'status' => $httpCode,
            'http_code' => $httpCode,
            'data' => $responseData,
            'urn' => $urn,
            'error' => $errorMsg,
        ];
    }

    /**
     * Register media (image/video) for LinkedIn posts.
    */
    public function registerMedia(Request $request) {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'linkedin_id' => 'required|string',
                'type' => 'required|in:image,video',
                'media' => 'required|file|max:50000',
                'caption' => 'nullable|string|max:3000'
            ]);

            $file = $request->file('media');
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // Validate file type
            $allowedMimeTypes = [
                'image' => ['image/jpeg', 'image/png', 'image/gif'],
                'video' => ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/webm']
            ];

            if (!in_array($mimeType, $allowedMimeTypes[$validated['type']])) {
                return response()->json([
                    'status' => 400,
                    'error' => "Type du fichier invalide: {$mimeType}. Pour {$validated['type']} posts, seulement " . implode(", ", $allowedMimeTypes[$validated['type']]) . " sont acceptés."
                ], 400);
            }

            $maxSizes = [
                'image' => 10 * 1024 * 1024, // 5MB for images
                'video' => 50 * 1024 * 1024 // 50MB for videos
            ];

            if ($fileSize > $maxSizes[$validated['type']]) {
                $maxSizeMB = $maxSizes[$validated['type']] / (1024 * 1024);
                return response()->json([
                    'status' => 400,
                    'error' => "La taille du fichier ({$fileSize} octets) dépasse la limite de LinkedIn pour le téléchargement de {$validated['type']} ({$maxSizeMB}MB)."
                ], 400);
            }

            $authorUrn = 'urn:li:person:' . $validated['linkedin_id'];

            $recipe = $validated['type'] === 'image' ? "urn:li:digitalmediaRecipe:feedshare-image" : "urn:li:digitalmediaRecipe:feedshare-video";

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
                CURLOPT_TIMEOUT => 1800
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $responseData = json_decode($response, true);

            curl_close($curl);

            Log::info('LinkedIn Asset Registration Response', [
                'http_code' => $httpCode,
                'response' => $responseData
            ]);

            // Handle specific API error responses
            if ($httpCode == 401) {
                return response()->json([
                    'status' => 401,
                    'error' => 'Échec de l\'authentification LinkedIn. Votre jeton d\'accès a peut-être expiré. Veuillez reconnecter votre compte.',
                    'token_expired' => true
                ], 401);
            }

            // Handle rate limiting
            if ($httpCode == 429) {
                return response()->json([
                    'status' => 429,
                    'error' => 'Limite de débit de l\'API LinkedIn dépassée. Veuillez réessayer ultérieurement.',
                    'raw_response' => $responseData
                ], 429);
            }

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

            // Handle LinkedIn API specific errors if available
            $apiErrorMessage = $responseData['message'] ?? 'Échec de l\'enregistrement du média sur LinkedIn';
            $apiErrorDescription = $responseData['serviceErrorCode'] ?? 'Erreur Inconnue';

            // Handle unsuccessful response with more detail
            return response()->json([
                'status' => $httpCode,
                'error' => "Erreur de LinkedIn API : {$apiErrorMessage} ({$apiErrorDescription})",
                'raw_response' => $responseData
            ], $httpCode !== 200 ? $httpCode : 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $errorMessage = implode(', ', $errors);
            
            Log::error('LinkedIn Media Registration Validation Error', [
                'message' => $errorMessage,
                'errors' => $errors
            ]);

            return response()->json([
                'status' => 422,
                'error' => 'Erreur de Validation: ' . $errorMessage
            ], 422);
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

    /**
     * Upload the binary file for media posts.
    */
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
            $curlError = curl_error($curl);
            $curlErrno = curl_errno($curl);
            
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            curl_close($curl);

            // Handle cURL errors
            if ($curlErrno) {
                Log::error('LinkedIn cURL Error during media upload', [
                    'error' => $curlError,
                    'errno' => $curlErrno
                ]);
                
                // Map common cURL errors to user-friendly messages
                $errorMessage = match ($curlErrno) {
                    CURLE_OPERATION_TIMEDOUT => 'La connexion a expiré lors du téléchargement. Votre fichier est peut-être trop volumineux ou votre connexion réseau est instable.',
                    CURLE_COULDNT_CONNECT => 'Impossible de se connecter aux serveurs LinkedIn. Veuillez vérifier votre connexion Internet.',
                    default => "Erreur du connexion lors du téléchargement : {$curlError}"
                };
                
                return response()->json([
                    'status' => 500,
                    'error' => $errorMessage,
                    'curl_error' => $curlError,
                    'curl_errno' => $curlErrno
                ], 500);
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Media uploaded successfully',
                    'headers' => $headers
                ]);
            }

            // Handle specific HTTP errors
            $errorMessage = match ($httpCode) {
                401 => 'Échec de l\'authentification LinkedIn. Votre jeton d\'accès a peut-être expiré. Veuillez reconnecter votre compte.',
                403 => 'LinkedIn a refusé la publication de ce média. Veuillez vérifier les autorisations de votre compte.',
                413 => 'Le fichier est trop volumineux pour être accepté par LinkedIn.',
                429 => 'Limite de débit de l\'API LinkedIn dépassée. Veuillez réessayer ultérieurement.',
                default => "Le téléchargement du média a échoué avec le code HTTP {$httpCode}."
            };

            // Handle upload failure
            return response()->json([
                'status' => $httpCode,
                'error' => $errorMessage,
                'response' => $body
            ], $httpCode);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $errorMessage = implode(', ', $errors);
            
            Log::error('LinkedIn Media Binary Upload Validation Error', [
                'message' => $errorMessage,
                'errors' => $errors
            ]);

            return response()->json([
                'status' => 422,
                'error' => 'Erreur de Validation: ' . $errorMessage
            ], 422);
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


    /**
     * Share media (image/video) on LinkedIn.
     */
    public function shareMedia(array $data) {
        if (request()->isMethod('post')) {
            $validated = validator($data, [
                'token' => 'required|string',
                'linkedin_id' => 'required|string',
                'asset' => 'required|string',
                'caption' => 'nullable|string',
                'media_type' => 'required|in:IMAGE,VIDEO'
            ])->validate();
        } else {
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
                    "shareMediaCategory" => $validated['media_type'],
                    "media" => [
                        [
                            "status" => "READY",
                            "description" => [
                                "text" => $validated['caption'] ?? ''
                            ],
                            "media" => $validated['asset'],
                            "title" => [
                                "text" => ""
                            ]
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
            CURLOPT_TIMEOUT => 1800
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseData = json_decode($response, true);
        curl_close($curl);

        // Handling the response
        if ($httpCode >= 200 && $httpCode < 300) {
            $responseArray = [
                'status' => 200,
                'http_code' => $httpCode,
                'message' => 'Post shared successfully',
                'data' => $responseData
            ];
    
            return request()->isMethod('post') ? response()->json($responseArray) : $responseArray;
        } else {
            $errorResponse = [
                'status' => $httpCode,
                'error' => $responseData['message'] ?? 'Unknown error'
            ];
            return request()->isMethod('post') ? response()->json($errorResponse, $httpCode) : $errorResponse;
        }

    }

    /**
     * Share an article on LinkedIn.
     */
    public function shareArticle(array $data) {
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

        if ($httpCode >= 200 && $httpCode < 300) {
            $responseArray = [
                'status' => 200,
                'http_code' => $httpCode,
                'message' => 'Post shared successfully',
                'data' => $responseData
            ];
    
            return request()->isMethod('post') ? response()->json($responseArray) : $responseArray;
        } else {
            $errorResponse = [
                'status' => $httpCode,
                'error' => $responseData['message'] ?? 'Unknown error'
            ];
            return request()->isMethod('post') ? response()->json($errorResponse, $httpCode) : $errorResponse;
        }
    }
}