<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        return response()->json([
            'success' => true,
            'data' => [
                'profile' => $profile,
            ],
        ]);
    }

    /**
     * Create a profile for the authenticated user.
     */
    public function store(StoreProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile already exists.',
            ], 409);
        }

        $profile = $user->profile()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile created successfully.',
            'data' => [
                'profile' => $profile,
            ],
        ], 201);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found.',
            ], 404);
        }

        $profile->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'profile' => $profile->fresh(),
            ],
        ]);
    }
}

