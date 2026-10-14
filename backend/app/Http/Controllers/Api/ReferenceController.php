<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ResumeTemplate;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class ReferenceController extends Controller
{
    public function templates(): JsonResponse
    {
        $templates = ResumeTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'key',
                'name',
                'description',
                'thumbnail',
                'sort_order',
            ]);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    public function skills(): JsonResponse
    {
        $skills = Skill::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'category',
            ]);

        return response()->json([
            'success' => true,
            'data' => $skills,
        ]);
    }

    public function languages(): JsonResponse
    {
        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'native_name',
                'direction',
            ]);

        return response()->json([
            'success' => true,
            'data' => $languages,
        ]);
    }
}