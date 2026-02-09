<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarAdminController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request);
        $data['slug'] = $this->normalizeSlug($data['slug'] ?? $data['title']);

        $car = Car::create($data);
        $this->syncMedia($car, $request->input('media', []));

        return response()->json(['data' => $car], 201);
    }

    public function update(Request $request, Car $car): JsonResponse
    {
        $data = $this->validatePayload($request, $car->id);
        $data['slug'] = $this->normalizeSlug($data['slug'] ?? $data['title']);

        $car->update($data);
        $this->syncMedia($car, $request->input('media', []));

        return response()->json(['data' => $car]);
    }

    public function destroy(Car $car): JsonResponse
    {
        $car->delete();

        return response()->json(['status' => 'deleted']);
    }

    private function validatePayload(Request $request, ?int $carId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:120'],
            'model' => ['required', 'string', 'max:120'],
            'year' => ['required', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'price' => ['required', 'integer', 'min:0'],
            'mileage' => ['required', 'integer', 'min:0'],
            'fuel_type' => ['required', 'string', 'max:60'],
            'transmission' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:cars,slug,' . $carId],
            'featured_image_path' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'video_path' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function normalizeSlug(string $source): string
    {
        return Str::slug($source);
    }

    private function syncMedia(Car $car, array $media): void
    {
        $car->media()->delete();

        foreach ($media as $index => $item) {
            if (!isset($item['path'])) {
                continue;
            }

            $car->media()->create([
                'type' => $item['type'] ?? 'image',
                'path' => $item['path'],
                'sort_order' => $item['sort_order'] ?? $index,
            ]);
        }
    }
}
