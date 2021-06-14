<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    /**
     * @OA\Get(
     *  path="api/v1/location",
     *  summary="Get all available location associated with the currently authenticated user",
     *  description="The user sending this request must be authenticated",
     *  operationId="location",
     *  tags={"Favourite Locations"},
     *  security={ {"bearer": {} }},
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Successfully returning exact data",
     * @OA\JsonContent(
     *       @OA\Property(property="data", type="object", ref="#/components/schemas/Location"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    ),
     *  ),
     *
     * @OA\Response(
     *    response=404,
     *    description="Returns when no location is found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Location not found"),
     *    ),
     *  ),
     *
     * )
     */


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $location = auth()->user()->locations;

        return response([
            'location' => $location
        ]);
    }

    /**
     * @OA\Post(
     *  path="api/v1/location",
     *  summary="Store a new location",
     *  description="The user sending this request must be authenticated",
     *  operationId="location",
     *  tags={"Favourite Locations"},
     *  security={ {"bearer": {} }},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Location data reference",
     *    @OA\JsonContent(
     *       required={"city_name","city_key"},
     *       @OA\Property(property="city_name", type="string", example="New York"),
     *       @OA\Property(property="city_key", type="integer", example="232"),
     *    ),
     * ),
     *
     * @OA\Response(
     *     response=201,
     *     description="Successfully created",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="New City Added successfully"),
     *       @OA\Property(property="data", type="object", ref="#/components/schemas/Location"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    ),
     *  ),
     *
     * @OA\Response(
     *    response=403,
     *    description="Returns when the user has reached their maximum limit of 5 locations",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have reached your maximum limit"),
     *    ),
     *  ),
     *
     * )
     */



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->locations->count() >= 5) {
            return response([
                'message' => 'You have reached your maximum limit'
            ], 403);
        }

        $request->validate([
            'city_name' => 'required|string',
            'city_key' => 'required|numeric'
        ]);

        $newLocation = auth()->user()->locations()->create([
            'city_name' => $request->input('city_name'),
            'city_key' => $request->input('city_key')
        ]);

        return response([
            'message' => 'New City Added Successfully',
            'location' => $newLocation
        ], 201);
    }


    /**
     * @OA\Get(
     *  path="api/v1/location{id}",
     *  summary="Get a single location associated with the currently authenticated user",
     *  description="The user sending this request must be authenticated",
     *  operationId="location",
     *  tags={"Favourite Locations"},
     *  security={ {"bearer": {} }},
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Successfully returning exact data",
     * @OA\JsonContent(
     *       @OA\Property(property="data", type="object", ref="#/components/schemas/Location"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    ),
     *  ),
     *
     * @OA\Response(
     *    response=404,
     *    description="Returns when no location is found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Location not found"),
     *    ),
     *  ),
     *
     * )
     */


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(location $location)
    {
        $favLocation = auth()->user()->locations->find($location);

        if (!$favLocation || $favLocation == null) {

            return response([
                'message' => 'Location not found'
            ], 404);

        }
            return response([
                'location' => $favLocation
            ]);

    }

    /**
     * @OA\Put(
     *  path="api/v1/location/{id}",
     *  summary="Update a single location associated with the currently authenticated user",
     *  description="The user sending this request must be authenticated",
     *  operationId="location",
     *  tags={"Favourite Locations"},
     *  security={ {"bearer": {} }},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass updated Location data reference",
     *    @OA\JsonContent(
     *       required={"city_name","city_key"},
     *       @OA\Property(property="city_name", type="string", example="Manchester"),
     *       @OA\Property(property="city_key", type="integer", example="010"),
     *    ),
     * ),
     *
     * @OA\Response(
     *     response=200,
     *     description="Successfully returning exact data",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Location updated successfully"),
     *       @OA\Property(property="id", type="integer", example="31"),
     *       @OA\Property(property="user_id", type="integer", example="6"),
     *       @OA\Property(property="city_name", type="string", example="Manchester"),
     *       @OA\Property(property="city_key", type="string", example="010"),
     *       @OA\Property(property="created_at", type="string", readOnly="true", example="2019-02-25 12:59:20"),
     *       @OA\Property(property="updated_at", type="string", readOnly="true", example="2019-02-25 12:59:20"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    ),
     *  ),
     *
     *@OA\Response(
     *    response=403,
     *    description="Returns when the user tries updating a resource with another userId other than theirs",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Action forbidden"),
     *    ),
     *  ),
     *
     * )
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, location $location)
    {

        if (auth()->user()->id !== $location->user_id) {
            return response([
                'message' => 'Action Forbidden'
            ], 403);
        }

        $request->validate([
            'city_name' => 'required|string',
            'city_key' => 'required|numeric'
        ]);

        $location->city_name = $request->input('city_name');
        $location->city_key = $request->input('city_key');
        $location->save();

        return response([
            'message' => 'Location updated successfully',
            'location' => $location
        ]);
    }

    /**
     * @OA\Delete(
     *  path="api/v1/location/{id}",
     *  summary="Delete a single location associated with the currently authenticated user",
     *  description="The user sending this request must be authenticated",
     *  operationId="location",
     *  tags={"Favourite Locations"},
     *  security={ {"bearer": {} }},
     *
     *
     * @OA\Response(
     *     response=200,
     *     description="Successfully deleted exact data",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Location deleted successfully"),
     *       @OA\Property(property="location", type="null"),
     *       ),
     *    ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    ),
     *  ),
     *
     *@OA\Response(
     *    response=403,
     *    description="Returns when the user tries to delete a resource with another userId other than theirs",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Action forbidden"),
     *    ),
     *  ),
     *
     * )
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(location $location)
    {
        if (auth()->user()->id !== $location->user_id) {
            return response([
                'message' => 'Action Forbidden'
            ], 403);
        }

        $location->delete();
        return response([
            'message' => 'Location deleted successfully',
            'location' => null
        ], 200);
    }
}
