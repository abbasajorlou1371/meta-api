<?php

namespace App\Http\Controllers\Api\V1\Feature;

use App\Helpers\AssetHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeatureImageRequest;
use App\Http\Resources\FeatureResource;
use Illuminate\Http\JsonResponse;
use App\Models\Feature;
use App\Models\Image;
use App\Models\User;
use App\Models\Variable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class FeatureController extends Controller
{
    public function __construct()
    {
        $this->middleware(['account.security', 'verified'])->except(['index', 'show']);
    }
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return FeatureResource::collection(auth()->user()->features);
    }

    /**
     * @param User $user
     * @param Feature $feature
     * @return FeatureResource
     */
    public function show(User $user, Feature $feature): FeatureResource
    {
        return new FeatureResource($feature);
    }

    /**
     * @param User $user
     * @param Feature $feature
     * @param FeatureImageRequest $request
     * @return JsonResponse
     */
    public function addFeatureImages(User $user, Feature $feature, FeatureImageRequest $request): \Illuminate\Http\JsonResponse
    {
        foreach ($request->file('images') as $image) {
            $path = env('FTP_ENDPOINT') . $image->store('public/feature-images/' . $feature->id);
            $feature->images()->create([
                'url' => $path
            ]);
        }
        return response()->json([
            'success' => 'تصاویر به ملک اضافه شدند'
        ]);
    }

    /**
     * @param Feature $feature
     * @param Image $image
     * @return JsonResponse
     */
    public function removeّFeatureImage(User $user, Feature $feature, Image $image): JsonResponse
    {
        Storage::disk('ftp')->delete($image->url);
        $image->delete();
        return response()->json([
            'status' => 'تصویر حذف شد'
        ], 200);
    }

    public function updateFeature(User $user, Feature $feature, Request $request)
    {
        $request->validate([
            'minimum_price_percentage' => [
                'required',
                'integer',
                'min:80',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->user()->isUnderEighteen() && $value < 110) {
                        $fail($attribute . ' must be greater than 110');
                    }
                }
            ]
        ]);

        $color = $feature->getColor();
        $totalPrice = $feature->properties->stability * Variable::getRate($color) * $request->minimum_price_percentage / 100;
        $price_psc = $totalPrice * 0.5 / Variable::getRate('psc');
        $price_irr = $totalPrice * 0.5;
        $feature->properties->update([
            'price_psc' => $price_psc,
            'price_irr' => $price_irr,
            'minimum_price_percentage' => $request->minimum_price_percentage
        ]);

        return response()->noContent();
    }
}
