<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use App\Models\Variant;
use App\Services\ProductServices;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * @var ProductServices
     */
    private $productService;

    const PAGINATE_PER_PAGE = 5;

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
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
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
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::find($product->productVariants->pluck('variant_id'));

        return view('products.edit', compact('variants', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        if ($this->productService->updateProduct($product->id, $request->validated())) {
            return redirect()->route('product.edit', $product->id)->with('success', "Product updated successfully");
        } else {
            return redirect()->route('product.edit', $product->id)->with('error', "Product update failed");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
