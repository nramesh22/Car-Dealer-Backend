<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function index(): JsonResponse
    {
        $cars = Car::query()
            ->published()
            ->with(['media' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->latest()
            ->get();

        return response()->json([
            'data' => $cars->map(fn (Car $car) => $this->transformCar($car)),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $car = Car::query()
            ->published()
            ->with(['media' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => $this->transformCar($car),
        ]);
    }

    private function transformCar(Car $car): array
    {
        $featuredImageUrl = $car->featured_image_path
            ? Storage::disk('public')->url($car->featured_image_path)
            : null;
        $videoUrl = null;
        if ($car->video_url) {
            $videoUrl = $car->video_url;
        } elseif ($car->video_path) {
            $videoUrl = Storage::disk('public')->url($car->video_path);
        }

        return [
            'id' => $car->id,
            'title' => $car->title,
            'brand' => $car->brand,
            'model' => $car->model,
            'year' => $car->year,
            'price' => $car->price,
            'mileage' => $car->mileage,
            'fuel_type' => $car->fuel_type,
            'transmission' => $car->transmission,
            'description' => $car->description,
            'status' => $car->status,
            'slug' => $car->slug,
            'meta_title' => $car->meta_title,
            'meta_description' => $car->meta_description,
            'featured_image_url' => $featuredImageUrl,
            'media' => $car->media->map(function ($media) {
                return [
                    'type' => $media->type,
                    'url' => Storage::disk('public')->url($media->path),
                ];
            })->values(),
            'video_url' => $videoUrl,
        ];
    }
}
