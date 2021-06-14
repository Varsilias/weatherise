<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['register', 'login']);
    }

/**
     * @OA\Post(
     * path="api/v1/auth/register",
     * summary="Sign Up",
     * description="Signup with firstname, lastname, email, password",
     * operationId="authLogin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "password"},
     *       @OA\Property(property="firstname", type="string", description="new user's firstname", example="Anthony"),
     *       @OA\Property(property="lastname", type="string", description="new user's lastname", example="Nwaizugbe"),
     *       @OA\Property(property="email", type="string", format="email", example="anthonynwaizugbe@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Wrong URL response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, Url not found check your spelling and try again.")
     *        ),
     *     ),
     *
     * @OA\Response(
     *    response=201,
     *    description="User created",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User successfully registered."),
     *       @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *       ),
     *    ),
     * )
     *
     */

    /**
     * This method validates user data sent via api request and then stores the data received
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [

        'firstname' => 'required|string|between:2,100',
        'lastname' => 'required|string|between:2,100',
        'email' => 'required|string|email|max:100|unique:users',
        'password' => 'required|string|min:6'

      ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
        ))->sendEmailVerificationNotification();

        return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
        ]);

    }

/**
 * @OA\Post(
 * path="api/v1/auth/login",
 * summary="Sign in",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"Auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="danielokoronkwo@yahoo.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *    ),
 * ),
 *
 * @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *         @OA\Property(property="access_token", type="string", readOnly="true", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYxODY1OTIyNiwiZXhwIjoxNjE4NjYyODI2LCJuYmYiOjE2MTg2NTkyMjYsImp0aSI6ImltaDdQdG9HZzdoeWRBRjYiLCJzdWIiOjMsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.Lq7IJe-YNvuZjxmp-c-EKQj3RxncW8gpHpSt8dn7mfg"),
 *         @OA\Property(property="token_type", type="string", readOnly="true", example="bearer"),
 *         @OA\Property(property="expires_in", type="integer", readOnly="true", example="3600"),
 *         @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *       ),
 *    ),
 *
 * @OA\Response(
 *    response=422,
 *    description="Wrong credentials response",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
 *        )
 *     )
 * )
 */

    /**
     * This method checks to see if the data sent via the api is available in the DB and then logs the user in
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

 /**
     * @OA\Post(
     *  path="api/v1/auth/logout",
     *  summary="Logout",
     *  description="Logout currently authenticated user",
     *  operationId="authLogout",
     *  tags={"Auth"},
     *  security={ {"bearer": {} }},
     *
     * @OA\Response(
     *     response=200,
     *     description="Success",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="'User successfully signed out!!!"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized"),
     *    ),
     *  ),
     *
     * )
     */


    /**
     * THis method logs the user out
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out!']);
    }

/**
     * @OA\Post(
     *  path="api/v1/auth/refresh",
     *  summary="Request new token",
     *  description="Generate new token for the currently authenticated user and invalidate the old token",
     *  operationId="authrefresh",
     *  tags={"Auth"},
     *  security={ {"bearer": {} }},
     *
     * @OA\Response(
     *     response=200,
     *     description="Success",
     * @OA\JsonContent(
     *         @OA\Property(property="access_token", type="string", readOnly="true", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvYXV0aFwvbG9naW4iLCJpYXQiOjE2MTYyMzcxMDEsImV4cCI6MTYxNjI0MDcwMSwibmJmIjoxNjE2MjM3MTAxLCJqdGkiOiJrSngzSlRzRGlhT0h1ckNKIiwic3ViIjoyMSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.pLUchLOEGmIv2Ns120sEZBuJj57YnAJEJuCFsuvXK4A"),
     *         @OA\Property(property="token_type", type="string", readOnly="true", example="bearer"),
     *         @OA\Property(property="expires_in", type="integer", readOnly="true", example="3600"),
     *         @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized"),
     *    ),
     *  ),
     *
     * )
     */

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

 /**
     * @OA\Get(
     *  path="api/v1/auth/profile",
     *  summary="Get Profile Information",
     *  description="Get currently authenticated user information",
     *  operationId="authFindUserById",
     *  tags={"Auth"},
     *  security={ {"bearer": {} }},
     *
     * @OA\Response(
     *     response=200,
     *     description="Success",
     * @OA\JsonContent(
     *       @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized"),
     *    ),
     *  ),
     *
     * )
     */


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }


     protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
