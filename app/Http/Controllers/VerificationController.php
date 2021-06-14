<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['verify']);
    }

/**
     * @OA\Get(
     * path="api/v1/email/verify/{id}",
     * summary="Verify email by Id",
     * description="Email Verification using Id,expires, hash, signature",
     * operationId="authLogin",
     * tags={"Email Verification"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass email verification credentials",
     *    @OA\JsonContent(
     *       required={"expires", "hash", "signature"},
     *       @OA\Property(property="expires", type="string", example="1619949484"),
     *       @OA\Property(property="hash", type="string", example="6a8b54fb68efe1ba5f99dfd2a23f2e310a224356"),
     *       @OA\Property(property="signature", type="string", example="135db5e0a887620e625d500055950a7c649bcc48e2ac7b686672097781223e0d"),
     *    ),
     * ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Wrong URL response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Invalid Email Verification Url.")
     *        ),
     *     ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Email Verified",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Email successfully verified."),
     *       @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *       ),
     *    ),
     * )
     *
     */

    /**
     * Verify email
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function verify($id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Invalid Email Verification Url'
            ], 401);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'message' => 'Email successfully verified',
            'user' => $user
        ], 200);
    }

/**
     * @OA\Get(
     * path="api/v1/email/resend",
     * summary="Request Email Verification link",
     * description="Request for new Email verification link",
     * operationId="authLogin",
     * tags={"Email Verification"},
     *  security={ {"bearer": {} }},
     *
     *
     * @OA\Response(
     *    response=409,
     *    description="Email Already Verified",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Email Already Verified.")
     *        ),
     *     ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Email ",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Email verification link sent to your email"),
     *       ),
     *    ),
     * )
     *
     */


    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email Already Verified'
            ], 409);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => "Email verification link sent to your email"
        ], 200);
    }
}
