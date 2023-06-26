<?php namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\String\s;

class ProductServices{

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * To list products with filter
     * @param array $filters
     * @return Builder
     */
    public function listProducts(array $filters = []): Builder
    {
        $query = $this->product->newQuery();
        $query->select('products.*');

        // Filtering by title
        if (isset($filters['title']) && $filters['title']) {
            $query->where('title', 'like', "%{$filters['title']}%");
        }

        // Filtering by variant
        if (isset($filters['variant']) && $filters['variant']) {
            $query->rightJoin('product_variants', 'products.id', '=', 'product_variants.product_id');
            $query->where('variant', 'like', $filters['variant']);
        }

        // Filtering by price
        if ((isset($filters['price_from']) && $filters['price_from'])
            || (isset($filters['price_to']) && $filters['price_to'])) {

            $query->leftJoin('product_variant_prices', 'products.id', '=', 'product_variant_prices.product_id');

            if (isset($filters['price_from']) && $filters['price_from']) {
                $query->where('price', '>=', (double)$filters['price_from']);
            }

            if (isset($filters['price_to']) && $filters['price_to']) {
                $query->where('price', '<=',  (double)$filters['price_from']);
            }
        }

        // Filtering by date
        if (isset($filters['date']) && $filters['date']) {
            $query->where(DB::raw('DATE(products.created_at)'), '=', $filters['date']);
        }

        return $query;
    }

    /**
     * To store product details with product variant, variant price and product images ...
     * @param array $validated
     * @return Product
     * @throws Exception
     */
    public function storeProduct(array $validated = []): Product
    {
        $product = $this->product->newInstance($validated);
        DB::beginTransaction();
        try {
            $product->save();
            $validated['product_id'] = $product->id;

            $productVariantArray = [];

            foreach ($this->formatProductVariantInfo($validated) as $index => $singleProductVariant) {
                $productVariantArray[$index] = new ProductVariant($singleProductVariant);
                $productVariantArray[$index]->save();
            }

            foreach ($this->formatProductVariantPriceInfo($validated, $productVariantArray) as $singleProductVariantPrice) {
                ProductVariantPrice::create($singleProductVariantPrice);
            }

            if (isset($validated['document'])) {
                foreach ($validated['document'] as $file) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'file_path' => 'tmp/uploads/' . $file,
                    ]);
                }
            }

            $product->refresh();
            DB::commit();

            return $product;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }


    /**
     * Format Product Variant Array Inputs
     *
     * @param array $inputs
     * @return array
     */
    private function formatProductVariantInfo(array $inputs = []): array
    {
        $productVariantArray = [];

        foreach ($inputs['product_variant'] as $input):
            $parentArr = [
                'variant_id' => $input['option'],
                'product_id' => $inputs['product_id'],
            ];

            foreach ($input['variant'] as $variant)
                $productVariantArray[] = array_merge($parentArr, [
                    'variant' => $variant
                ]);

        endforeach;

        return $productVariantArray;
    }

    /**
     * Format Product Variant Array Inputs
     *
     * @param array $inputs
     * @param array $productVariantArray
     * @return array
     */
    private function formatProductVariantPriceInfo(array $inputs, array &$productVariantArray): array
    {
        $productVariantPriceArray = [];

        $variantFreqArr = [];

        foreach ($productVariantArray as $item) {
            $variantFreqArr[$item->variant] = $item->id;
        }

        /**
         * Formatting Product Variant Price Array Inputs
         */
        foreach ($inputs['product_variant_prices'] as $input) {

            $parentArr['price'] = $input['price'] ?? 0;
            $parentArr['stock'] = $input['stock'] ?? 0;
            $parentArr['product_id'] = $inputs['product_id'] ?? 0;

            $titleArray = explode("/", trim($input['title'], "/"));

            if (isset($titleArray[0])) {
                $parentArr['product_variant_one'] = $variantFreqArr[$titleArray[0]] ?? null;
            }

            if (isset($titleArray[1])) {
                $parentArr['product_variant_two'] = $variantFreqArr[$titleArray[1]] ?? null;
            }

            if (isset($titleArray[2])) {
                $parentArr['product_variant_three'] = $variantFreqArr[$titleArray[2]] ?? null;
            }

            $productVariantPriceArray[] = $parentArr;
        }

        Log::info("Data", $productVariantPriceArray);
        return $productVariantPriceArray;
    }
}
