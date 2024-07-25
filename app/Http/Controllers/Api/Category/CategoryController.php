<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Api\AppController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\MenuResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoryController extends AppController
{

    public function index(Request $request)
    {
        try {
            $mainCategories = $this->getMainCategories();
            $subCategories = $this->getSubCategories();
            $products = $this->getProducts();

            return $this->successResponse(
                __('home.home_success'),
                [
                    'main_categories' => CategoryResource::collection($mainCategories),
                    'main_pagination' => $this->getPaginationData($mainCategories),
                    'sub_categories' => CategoryResource::collection($subCategories),
                    'sub_pagination' => $this->getPaginationData($subCategories),
                    'products' => ProductResource::collection($products),
                    'products_pagination' => $this->getPaginationData($products),
                ]
            );
        } catch (Exception $e) {
            return $this->genericErrorResponse(__('errors.server_error'), ['error' => $e->getMessage()]);
        }
    }


    private function getMainCategories()
    {
        return Category::with(['children'])
            ->whereDoesntHave('parent')
            ->all();
    }

    private function getSubCategories()
    {
        return Category::with(['parent'])
            ->whereHas('parent')
            ->all();
    }
    public function show(Request $request, $id, $subCategoryId = null)
    {
        try {
            $category = $this->getCategoryWithRelations($id);

            if ($subCategoryId) {
                $subCategory = $this->getSubCategoryWithRelations($category, $subCategoryId);
                return $this->successResponse(
                    __('home.home_success'),
                    ['category' => new MenuResource($subCategory)]
                );
            }

            $products = $this->getProductsForCategory($category);

            return $this->successResponse(
                __('home.home_success'),
                ['category' => new MenuResource($category->setRelation('products', $products))]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('errors.category_not_found'));
        } catch (Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }

    private function getCategoryWithRelations($id)
    {
        return Category::with(['parent', 'children', 'products'])->findOrFail($id);
    }


    private function getSubCategoryWithRelations($category, $subCategoryId)
    {
        return $category->children()->with(['products.images', 'products.sizes', 'products.additions'])
            ->findOrFail($subCategoryId);
    }

    private function getProductsForCategory($category)
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
