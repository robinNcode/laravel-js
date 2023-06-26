<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\Variant;
use App\Services\ProductServices;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * @var ProductServices
     */
    private $productService;

    const PAGINATE_PER_PAGE = 10;

    /**
     * ProductController constructor.
     * @param ProductServices $productService
     */
    public function __construct(ProductServices $productService)
    {
        $this->productService = $productService;
    }


    /**
     * Display a listing of the Products with full information ...
     * @param ProductFilterRequest $request
     * @return Application|Factory|View
     */
    public function index(ProductFilterRequest $request): View
    {
        $variants = Variant::all();
        $products = $this->productService
            ->filterProducts($request->validated())
            ->paginate(self::PAGINATE_PER_PAGE);

        return view('products.index', compact('variants', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductStoreRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        if ($this->productService->storeProduct($request->validated())) {
            return redirect()->route('product.create')->with('success', "Product info added successfully!");
        } else {
            return redirect()->route('product.create')->with('error', "Product info addition failed!");
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return Application|Factory|View
     */
    public function edit(Product $product)
    {
        $variants = Variant::find($product->productVariants->pluck('variant_id'));
        $product->productImages->pluck('file_path'); // to get images

        return view('products.edit', compact('variants', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        if ($this->productService->updateProduct($product->id, $request->validated())) {
            return redirect()->route('product.edit', $product->id)->with('success', "Product updated successfully");
        } else {
            return redirect()->route('product.edit', $product->id)->with('error', "Product update failed");
        }
    }

    public function storeMedia(Request $request): JsonResponse
    {
        $path = public_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }
}
