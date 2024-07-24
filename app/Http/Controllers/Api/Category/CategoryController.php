<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Api\AppController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\MenuResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Api\MainController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoryController extends AppController
{


    /**
     * Fetch all categories along with their parent and children.
     */
    public function index(Request $request)
    {
        try {
            // Get main categories (those having children)
            $mainCategories = Category::with(['children'])
                ->whereHas('children')
                ->paginate(3);

            // Get sub categories (those having parents)
            $subCategories = Category::with(['parent'])
                ->whereHas('parent')
                ->paginate(3);

            // Get products (assuming your getProducts() method handles this)
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

    /**
     * Fetch a specific category and its children, including parent as a collection.
     */
    public function show(Request $request, $id, $subCategoryId = null)
    {
        try {
            // Fetch the main category with its children and products
            $category = Category::with(['parent', 'children', 'products'])->findOrFail($id);

            if ($subCategoryId) {
                // Fetch the specific subcategory under the main category with its products
                $subCategory = $category->children()->with(['products.images', 'products.sizes', 'products.additions'])
                    ->findOrFail($subCategoryId);

                return $this->successResponse(
                    __('home.home_success'),
                    [
                        'category' => new MenuResource($subCategory),
                    ]
                );
            }

            // Get all products from the main category and its children
            $childCategoryIds = $category->children->pluck('id');
            $allProductIds = $category->products->pluck('id')
                ->merge(
                    Product::whereIn('id', function ($query) use ($childCategoryIds) {
                        $query->select('product_id')
                            ->from('product_category')
                            ->whereIn('category_id', $childCategoryIds);
                    })->pluck('id')
                )->unique();

            $products = Product::whereIn('id', $allProductIds)
                ->with(['images', 'sizes', 'additions']) // Eager load related data
                ->paginate(6);

            return $this->successResponse(
                __('home.home_success'),
                [
                    'category' => new MenuResource($category->setRelation('products', $products)),
                ]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('errors.category_not_found'));
        } catch (Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get products with their images, sizes, and additions.
     */
    private function getProducts()
    {
        return Product::with(['images', 'sizes', 'additions'])->paginate(6);
    }
}
