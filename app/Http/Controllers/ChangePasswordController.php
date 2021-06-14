<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class ChangePasswordController extends Controller
{
   /**
     * @OA\Post(
     * path="api/v1/email/resetPassword",
     * summary="Reset Password",
     * description="Reset Password passing token, email, password and password_confirmation",
     * operationId="authLogin",
     * tags={"Password Reset"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Password reset credentials",
     *    @OA\JsonContent(
     *       required={"token", "email", "password", "password_confirmation"},
     *       @OA\Property(property="token", type="string", example="UclOkLRUrvypm2tmt9t4sbgb5t7bNCxEvbwssABd"),
     *       @OA\Property(property="email", type="string", format="email", example="danielokoronkwo@yahoo.com"),
     *       @OA\Property(property="password", type="string", example="password12345"),
     *       @OA\Property(property="password_confirmation", type="string", example="password12345"),
     *    ),
     * ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Wrong URL response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Invalid Url Provided.")
     *        ),
     *     ),
     *
     *  @OA\Response(
     *    response=403,
     *    description="Wrong email or token",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Either your token or email is wrong.")
     *        ),
     *     ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Email Verified",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Password has been updated successfully."),
     *       ),
     *    ),
     * )
     *
     */

    /**
     *  Uses the UpdatePasswordRequest rules specified in the file to check if the email and the token sent
     * along are valid. If valid it resets the password else it throw an error tokenNotFoundError
     */
    public function passwordResetProcess(UpdatePasswordRequest $request)
    {
        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
    }

    // Verify if token is valid
    private function updatePasswordRow($request)
    {
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->resetToken
        ]);
    }

    // Token not found response
    private function tokenNotFoundError()
    {
        return response()->json([
            'error' => 'Either your email or token is wrong'
        ], 403);
    }

    // Resets the Password
    private function resetPassword($request)
    {
        // find Email
        $userData = User::whereEmail($request->email)->first();

        // Update Password
        $userData->update([
            'password' => bcrypt($request->password)
        ]);

        // Remove Verification data from db
        $this->updatePasswordRow($request)->delete();

        // reset password response
        return response()->json([
            'data' => 'Password has been updated successfully'
        ], 201);
    }
}
