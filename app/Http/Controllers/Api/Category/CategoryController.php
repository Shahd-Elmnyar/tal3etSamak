<?php

namespace App\Http\Controllers\Api\Category;

use Exception;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Api\AppController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryController extends AppController
{

    public function index(): JsonResponse
    {
        try {
            $mainCategories = $this->getMainCategories();
            $subCategories = $this->getSubCategories();
            $products = $this->getProducts();

            return $this->successResponse(
                null,
                [
                    'main_categories' => CategoryResource::collection($mainCategories),
                    'sub_categories' => CategoryResource::collection($subCategories),
                    'products' => ProductResource::collection($products),
                    'products_pagination' => $this->getPaginationData($products),
                ]
            );
        } catch (Exception $e) {
            Log::error('CategoryController error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    public function show(int $id, ?int $subCategoryId = null): JsonResponse
    {
        try {
            $category = $this->getCategoryWithRelations($id);

            if ($subCategoryId) {
                $subCategory = $this->getSubCategoryWithRelations($category, $subCategoryId);

                return $this->successResponse(
                    null,
                    ['category' => new CategoryResource($subCategory)]
                );
            }

            $products = $this->getProductsForCategory($category);

            return $this->successResponse(
                null,
                ['category' => new CategoryResource($category->setRelation('products', $products))]
            );
        } catch (ModelNotFoundException $e) {
            Log::warning('Category not found: ' . $e->getMessage());
            return $this->notFoundResponse(__('home.category_not_found'));
        } catch (Exception $e) {
            Log::error('CategoryController error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    private function getMainCategories()
    {
        return Category::with('children')
            ->whereDoesntHave('parent')
            ->get();
    }

    private function getSubCategories()
    {
        return Category::with('parent')
            ->whereHas('parent')
            ->get();
    }

    private function getCategoryWithRelations(int $id): Category
    {
        return Category::with(['parent', 'children', 'products'])->findOrFail($id);
    }

    private function getSubCategoryWithRelations(Category $category, int $subCategoryId): Category
    {
        return $category->children()->with(['products.images', 'products.sizes', 'products.additions'])
            ->findOrFail($subCategoryId);
    }

    private function getProductsForCategory(Category $category): LengthAwarePaginator
    {
        $childCategoryIds = $category->children->pluck('id');
        $allProductIds = $category->products->pluck('id')
            ->merge(
                Product::whereIn('id', function ($query) use ($childCategoryIds) {
                    $query->select('product_id')
                        ->from('product_category')
                        ->whereIn('category_id', $childCategoryIds);
                })->pluck('id')
            )->unique();

        return Product::whereIn('id', $allProductIds)
            ->with(['images', 'sizes', 'additions'])
            ->paginate(6);
    }

}
