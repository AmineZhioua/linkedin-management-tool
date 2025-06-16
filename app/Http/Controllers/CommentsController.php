<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class CommentsController extends Controller
{
    // METHOD TO GET COMMENTS ON A POST
    public function getCommentsOnThePost(Request $request) {
        try {
            $validated = $request->validate([
                'linkedin_id' => 'required|integer|exists:linkedin_users,id',
                'linkedin_token' =>  'required|string',
                'post_urn' => 'required|string',
            ]);

            $commentsUrl = "https://api.linkedin.com/rest/socialActions/". $validated['post_urn'] ."/comments";

            $headers = [
                "X-HTTP-Method-Override: GET",
                "Authorization: Bearer " . $validated['linkedin_token'],
                "Content-Type: application/x-www-form-urlencoded",
                "X-Restli-Protocol-Version: 2.0.0",
                "Linkedin-version: 202501",
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $commentsUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 1800
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $responseData = json_decode($response, true);
            curl_close($curl);

            // Handling the response
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 200,
                    'httpCode' => $httpCode,
                    'data' => $responseData,
                ], 200);

            } else {
                return response()->json([
                    'status' => $httpCode,
                    'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                ], $httpCode);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                'error_message' => $e->getMessage(),
            ], 500);
        }
    }


    // METHOD TO GET REPLIES ON THE COMMENTS
    public function getCommentsOnComments(Request $request) {
        try {
            $validated = $request->validate([
                "linkedin_id" => "required|integer|exists:linkedin_users,id",
                "linkedin_token" => "required|string",
                "activity_urn" => "required|string",
                "comment_urn" => "required|string"
            ]);
            
            $activityUrn = $validated["activity_urn"];
            $commentUrn = $validated["comment_urn"];

            $nestedCommentsUrl = "https://api.linkedin.com/rest/socialActions/urn:li:comment:({$activityUrn},{$commentUrn})/comments";

            $headers = [
                "X-HTTP-Method-Override: GET",
                "Authorization: Bearer " . $validated['linkedin_token'],
                "Content-Type: application/x-www-form-urlencoded",
                "X-Restli-Protocol-Version: 2.0.0",
                "Linkedin-version: 202501",
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $nestedCommentsUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 1800
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $responseData = json_decode($response, true);
            curl_close($curl);

            // Handling the response
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 200,
                    'httpCode' => $httpCode,
                    'data' => $responseData,
                ], 200);

            } else {
                return response()->json([
                    'status' => $httpCode,
                    'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                ], $httpCode);
            }

        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                'error_message' => $e->getMessage(),
            ], 500);
        }
    }


    // METHOD TO A CREATE A COMMENT ON A POST
    public function createComment(Request $request) {
        try {
            $validated = $request->validate([
                "linkedin_id" => "required|string",
                "linkedin_token" => "required|string",
                "content" => "required|string|max:3000",
                "post_urn" => "required|string",
                "parent_comment_urn" => "nullable|string"
            ]);

            $postUrn = $validated["post_urn"];
            $createCommentUrl = "https://api.linkedin.com/rest/socialActions/{$postUrn}/comments";
            $personUrn = 'urn:li:person:' . $validated['linkedin_id'];

            $commentData = [
                "actor" => $personUrn,
                "object" => "urn:li:activity:" . $validated["post_urn"],
                "message" => [
                    "text" => $validated["content"]
                ]
            ];

            if (!empty($validated["parent_comment_urn"])) {
                $commentData["parentComment"] = $validated["parent_comment_urn"];
            }
            
            $headers = [
                "Authorization: Bearer " . $validated['linkedin_token'],
                "Content-Type: application/json",
                "X-Restli-Protocol-Version: 2.0.0",
                "Linkedin-version: 202501",
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $createCommentUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($commentData),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FOLLOWLOCATION => false
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                $curlError = curl_error($curl);
                curl_close($curl);
                
                return response()->json([
                    'status' => 500,
                    'message' => 'Erreur de connexion à LinkedIn API',
                    'error' => $curlError
                ], 500);
            }
            
            curl_close($curl);
            $responseData = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 'success',
                    'httpCode' => $httpCode,
                    'data' => $responseData,
                    'comment_type' => !empty($validated["parent_comment_urn"]) ? 'reply' : 'comment'
                ], 201);

            } else {
                $errorMessage = "Une erreur s'est produite lors de la création du commentaire";
                
                if (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                } elseif (isset($responseData['error_description'])) {
                    $errorMessage = $responseData['error_description'];
                }

                return response()->json([
                    'status' => 'error',
                    'httpCode' => $httpCode,
                    'message' => $errorMessage,
                    'linkedin_response' => $responseData
                ], $httpCode);
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Données de validation invalides',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('LinkedIn Comment Creation Error: ' . $e->getMessage(), [
                'request_data' => $request->except(['linkedin_token']),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur interne du serveur',
                'error_code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }


    // METHOD TO DELETE A COMMENT ON A POST
    public function deleteComment(Request $request) {
        try {
            $validated = $request->validate([
                "linkedin_token" => "required|string",
                "comment_urn" => "required|string",
                "post_urn" => "required|string",
                "person_urn" => "required|string"
            ]);

            $postUrn = $validated['post_urn'];
            $commentUrn = $validated['comment_urn'];
            $personUrn = $validated['person_urn'];

            $deleteUrl = "https://api.linkedin.com/rest/socialActions/{$postUrn}/comments/{$commentUrn}?actor={$personUrn}";

            $headers = [
                "Authorization: Bearer " . $validated['linkedin_token'],
                "X-Restli-Protocol-Version: 2.0.0",
                "Linkedin-version: 202501",
            ];

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $deleteUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 600
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $responseData = json_decode($response, true);
            curl_close($curl);

            // Handling the response
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 200,
                    'httpCode' => $httpCode,
                    'data' => $responseData,
                ], 200);

            } else {
                return response()->json([
                    'status' => $httpCode,
                    'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                ], $httpCode);
            }

        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                'error_message' => $e->getMessage(),
            ], 500);
        }
    }


    // METHOD USED TO EDIT A COMMENT ON A POST
    public function editComment(Request $request) {
        try {
            $validated = $request->validate([
                "linkedin_id" => "required|string",
                "linkedin_token" => "required|string",
                "content" => "required|string|max:3000",
                "comment_urn" => "required|string",
                "person_urn" => "required|string",
                "post_urn" => "required|string",
            ]);

            $postUrn = $validated['post_urn'];
            $commentUrn = $validated['comment_urn'];
            $personUrn = $validated['person_urn'];

            $updateCommentUrl = "https://api.linkedin.com/rest/socialActions/{$postUrn}/comments/{$commentUrn}?actor={$personUrn}";

            $commentUpdateData = [
                "patch" =>  [
                    "message" => [
                        "\$set" => [
                            "text" => $validated['content']
                        ]
                    ]
                ]
            ];

            $headers = [
                "Authorization: Bearer " . $validated['linkedin_token'],
                "Content-Type: application/json",
                "X-Restli-Protocol-Version: 2.0.0",
                "Linkedin-version: 202501",
                "X-RestLi-Method: PARTIAL_UPDATE",
            ];

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $updateCommentUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($commentUpdateData),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FOLLOWLOCATION => false
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $responseData = json_decode($response, true);

            curl_close($curl);

            // Handling the response
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 201,
                    'httpCode' => $httpCode,
                    'data' => $responseData,
                ], 201);

            } else {
                return response()->json([
                    'status' => $httpCode,
                    'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                ], $httpCode);
            }

        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Une erreur s\'est produite lors de la récupération des commentaires !",
                'error_message' => $e->getMessage(),
            ], 500);
        }
    }
}
