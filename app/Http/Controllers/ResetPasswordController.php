<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendMailreset;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ResetPasswordController extends Controller
{

/**
     * @OA\Post(
     * path="api/v1/sendPasswordResetLink",
     * summary="Request Password Reset Link",
     * description="Request Password Reset Link passing User's email",
     * operationId="authLogin",
     * tags={"Password Reset"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Password Reset credential",
     *    @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", format="", example="danielokoronkwo@yahoo.com"),
     *    ),
     * ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Email Sent",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Reset password link sent to you email successfully, please check your inbox."),
     *       ),
     *    ),
     * )
     *
     */


    // The sendEmail() method sends the email and handles the error if any

    public function sendEmail(Request $request)
    {
        if(!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }

        $this->send($request->email);
        return $this->successResponse();
    }

    /*
        The send() Method is used by the sendEmail() function to send the email
        by first creating a token and passing it along with the email into the SendMailreset class in the
         App\Mail\SendMailreset directory
    */
    public function send($email)
    {
        $token = $this->createToken($email);
        Mail::to($email)->send(new SendMailreset($token, $email));
    }

    /*
        This createToken() is used by the send() Method to create the token that will be passed along with the
        user's email, it also checks if there is any existing token attached with the email provided by the user

        The createToken() method also creates and stores new token in the password_resets table which would be
        verified when the user wants to reset their password
    */
    public function createToken($email)
    {

        $oldToken = DB::table('password_resets')->where('email', $email)->first();

        if ($oldToken) {
            return $oldToken->token;
        }

        $token = Str::random(40);
        $this->saveToken($token, $email);
        return $token;
    }

    /**
     * This function is utilised by the createToken() method to save any new generated token for each user
    */
    public function saveToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

    }

    /**
     * The validateEmail() method validates that the user's email is present in the User's table and is utilised
     * in the sendEmail() method to send the Password reset link
     */
    public function validateEmail($email)
    {
        return !!User::where('email', $email)->first();
    }

    /**
     * The failedResponse()  method is the custom error response used in the sendEmail() method
     */
    public function failedResponse()
    {
        return response()->json([
            'error' => "Email was not found in the Database"
        ], 404);
    }

    /**
     * The successResponse() method is the custom success response used in the sendEmail() method
     */
    public function successResponse()
    {
        return response()->json([
            'message' => "Reset password link sent to you email successfully, please check your inbox"
        ], 200);
    }
}
