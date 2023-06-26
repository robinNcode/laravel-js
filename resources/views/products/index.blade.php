@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>

    <div class="card">
        <form action="{{ route('product.index') }}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control"
                           value="{{ request('title') }}">
                </div>
                <div class="col-md-2">
                    <select name="variant" class="form-control">
                        <option value="">--Select a Variant--</option>
                        @foreach($variants as $variant)
                            <optgroup label="{{$variant->title}}">
                                @foreach(array_unique($variant->productVariants->pluck('variant')->toArray()) as $productVariant)
                                    <option value="{{$productVariant}}"
                                            @if($productVariant == request('variant')) selected @endif>
                                        {{ $productVariant }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From"
                               class="form-control" value="{{request('price_from')}}">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control"
                               value="{{request('price_to')}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control" value="{{request('date')}}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <div class="card-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th style="width: 150px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{$loop->index + $products->firstItem()}}</td>
                            <td style="width: 10%;">{{$product->title}} <br> Created at : {{ $product->created_at}}</td>
                            <td style="width: 40%;">{{$product->description}}</td>
                            <td>
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
                                    @forelse($product->productVariantPrices as $productVariantPrice)
                                        <dt class="col-sm-3 pb-0">
                                            {{$productVariantPrice->title()}}
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price
                                                    : {{ number_format($productVariantPrice->price,2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock
                                                    : {{ number_format($productVariantPrice->stock,2) }}</dd>
                                            </dl>
                                        </dd>
                                    @empty
                                        <p>No Data Available</p>
                                    @endforelse
                                </dl>
                                <button
                                    onclick="$('#variant').toggleClass('h-auto')"
                                    class="btn btn-sm btn-link">
                                    Show more
                                </button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>

@endsection
