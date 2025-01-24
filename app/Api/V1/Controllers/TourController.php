<?php

namespace App\Api\V1\Controllers;
use App\Http\Requests\Api\V1\Tour\CreateTourRequest;
use App\Http\Requests\Api\V1\Tour\UpdateTourRequest;
use App\Models\Tour;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * @group Tour Management
 *
 * APIs for managing tours
 */
class TourController extends BaseController
{
    use ApiResponse;

    /**
     * List Tours
     * 
     * Get a paginated list of tours. You can filter tours by various criteria.
     * 
     * @queryParam start_date date Filter tours starting from this date. Example: 2024-02-01
     * @queryParam end_date date Filter tours ending before this date. Example: 2024-03-01
     * @queryParam location string Filter tours by location (partial match). Example: Paris
     * @queryParam min_price numeric Filter tours with price greater than or equal to this value. Example: 100
     * @queryParam max_price numeric Filter tours with price less than or equal to this value. Example: 1000
     * @queryParam search string Search in tour names and descriptions. Example: mountain
     * @queryParam page integer Page number for pagination. Example: 1
     * 
     * @response {
     *  "status": "success",
     *  "message": "Tours retrieved successfully",
     *  "data": {
     *    "current_page": 1,
     *    "data": [
     *      {
     *        "id": 1,
     *        "name": "Paris City Tour",
     *        "description": "Explore the beautiful city of Paris",
     *        "location": "Paris, France",
     *        "start_date": "2024-03-01 10:00:00",
     *        "end_date": "2024-03-03 18:00:00",
     *        "price": "299.99",
     *        "user_id": 1,
     *        "created_at": "2024-01-24T19:26:20.000000Z",
     *        "updated_at": "2024-01-24T19:26:20.000000Z"
     *      }
     *    ],
     *    "total": 60,
     *    "per_page": 10,
     *    "last_page": 6
     *  }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        if (!Gate::allows('view-tours')) {
            return $this->fail('COMMON_AUTHORIZATION_EXCEPTION', []);
        }

        $query = Tour::query();

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Get paginated results
        $tours = $query->latest()->paginate(10);

        return $this->success('TOUR_LIST_SUCCESS', $tours);
    }

    /**
     * Create Tour
     * 
     * Create a new tour. Only authenticated tour operators can create tours.
     * 
     * @authenticated
     * 
     * @bodyParam name string required The name of the tour. Example: Paris City Tour
     * @bodyParam description string required The detailed description of the tour. Example: Explore the beautiful city of Paris with our experienced guides.
     * @bodyParam location string required The location where the tour takes place. Example: Paris, France
     * @bodyParam start_date datetime required The start date and time of the tour. Example: 2024-03-01 10:00:00
     * @bodyParam end_date datetime required The end date and time of the tour. Must be after start_date. Example: 2024-03-03 18:00:00
     * @bodyParam price numeric required The price of the tour. Must be between 0 and 999999.99. Example: 299.99
     * 
     * @response 201 {
     *  "status": "success",
     *  "message": "Tour created successfully",
     *  "data": {
     *    "id": 1,
     *    "name": "Paris City Tour",
     *    "description": "Explore the beautiful city of Paris with our experienced guides.",
     *    "location": "Paris, France",
     *    "start_date": "2024-03-01 10:00:00",
     *    "end_date": "2024-03-03 18:00:00",
     *    "price": "299.99",
     *    "user_id": 1,
     *    "created_at": "2024-01-24T19:26:20.000000Z",
     *    "updated_at": "2024-01-24T19:26:20.000000Z"
     *  }
     * }
     * 
     * @response 422 {
     *  "status": "error",
     *  "message": "Tour name is required",
     *  "data": {},
     *  "code": "422001"
     * }
     * 
     * @response 403 {
     *  "status": "error",
     *  "message": "Forbidden",
     *  "data": {},
     *  "code": "403"
     * }
     */
    public function store(CreateTourRequest $request): JsonResponse
    {
        if (!Gate::allows('create-tour')) {
            return $this->fail('COMMON_AUTHORIZATION_EXCEPTION', [], 403);
        }

        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $tour = Tour::create($validated);

        return $this->success('TOUR_CREATE_SUCCESS', $tour, 201);
    }

    /**
     * Show Tour
     * 
     * Get detailed information about a specific tour.
     * 
     * @urlParam id integer required The ID of the tour. Example: 1
     * 
     * @response {
     *  "status": "success",
     *  "message": "Tour details retrieved successfully",
     *  "data": {
     *    "id": 1,
     *    "name": "Paris City Tour",
     *    "description": "Explore the beautiful city of Paris",
     *    "location": "Paris, France",
     *    "start_date": "2024-03-01 10:00:00",
     *    "end_date": "2024-03-03 18:00:00",
     *    "price": "299.99",
     *    "user_id": 1,
     *    "created_at": "2024-01-24T19:26:20.000000Z",
     *    "updated_at": "2024-01-24T19:26:20.000000Z"
     *  }
     * }
     * 
     * @response 404 {
     *  "status": "error",
     *  "message": "Tour not found",
     *  "data": {},
     *  "code": "404001"
     * }
     */
    public function show(int $id): JsonResponse
    {
        $tour = Tour::find($id);
        
        if (!$tour) {
            return $this->fail('TOUR_NOT_FOUND', [], 404);
        }

        if (!Gate::allows('view-tour', $tour)) {
            return $this->fail('COMMON_AUTHORIZATION_EXCEPTION', [], 403);
        }

        return $this->success('TOUR_SHOW_SUCCESS', $tour);
    }

    /**
     * Update Tour
     * 
     * Update an existing tour. Only the tour operator who created the tour or an admin can update it.
     * 
     * @authenticated
     * 
     * @urlParam id integer required The ID of the tour. Example: 1
     * @bodyParam name string The name of the tour. Example: Updated Paris City Tour
     * @bodyParam description string The detailed description of the tour. Example: Updated tour description
     * @bodyParam location string The location where the tour takes place. Example: Paris, France
     * @bodyParam start_date datetime The start date and time of the tour. Example: 2024-03-01 10:00:00
     * @bodyParam end_date datetime The end date and time of the tour. Must be after start_date. Example: 2024-03-03 18:00:00
     * @bodyParam price numeric The price of the tour. Must be between 0 and 999999.99. Example: 349.99
     * 
     * @response {
     *  "status": "success",
     *  "message": "Tour updated successfully",
     *  "data": {
     *    "id": 1,
     *    "name": "Updated Paris City Tour",
     *    "description": "Updated tour description",
     *    "location": "Paris, France",
     *    "start_date": "2024-03-01 10:00:00",
     *    "end_date": "2024-03-03 18:00:00",
     *    "price": "349.99",
     *    "user_id": 1,
     *    "created_at": "2024-01-24T19:26:20.000000Z",
     *    "updated_at": "2024-01-24T19:30:20.000000Z"
     *  }
     * }
     * 
     * @response 403 {
     *  "status": "error",
     *  "message": "Forbidden",
     *  "data": {},
     *  "code": "403"
     * }
     * 
     * @response 404 {
     *  "status": "error",
     *  "message": "Tour not found",
     *  "data": {},
     *  "code": "404001"
     * }
     * 
     * @response 422 {
     *  "status": "error",
     *  "message": "Tour price cannot exceed 999999.99",
     *  "data": {},
     *  "code": "422015"
     * }
     */
    public function update(UpdateTourRequest $request, int $id): JsonResponse
    {
        $tour = Tour::find($id);
        
        if (!$tour) {
            return $this->fail('TOUR_NOT_FOUND', [], 404);
        }

        if (!Gate::allows('update-tour', $tour)) {
            return $this->fail('COMMON_AUTHORIZATION_EXCEPTION', [], 403);
        }

        $validated = $request->validated();
        
        $tour->update($validated);

        return $this->success('TOUR_UPDATE_SUCCESS', $tour);
    }

    /**
     * Delete Tour
     * 
     * Delete an existing tour. Only the tour operator who created the tour or an admin can delete it.
     * 
     * @authenticated
     * 
     * @urlParam id integer required The ID of the tour. Example: 1
     * 
     * @response {
     *  "status": "success",
     *  "message": "Tour deleted successfully",
     *  "data": {}
     * }
     * 
     * @response 403 {
     *  "status": "error",
     *  "message": "Forbidden",
     *  "data": {},
     *  "code": "403"
     * }
     * 
     * @response 404 {
     *  "status": "error",
     *  "message": "Tour not found",
     *  "data": {},
     *  "code": "404001"
     * }
     */
    public function destroy(int $id): JsonResponse
    {
        $tour = Tour::find($id);
        
        if (!$tour) {
            return $this->fail('TOUR_NOT_FOUND', [], 404);
        }

        if (!Gate::allows('delete-tour', $tour)) {
            return $this->fail('COMMON_AUTHORIZATION_EXCEPTION', []);
        }

        $tour->delete();

        return $this->success('TOUR_DELETE_SUCCESS');
    }
} 