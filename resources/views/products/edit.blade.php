@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <form action="{{route('product.update', $product->id)}}" method="post" autocomplete="off" spellcheck="false">
        @method('PUT')
        @csrf
        <section>
            <div class="row">
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
            <div class="row">
                <div class="col-md-6">
                    <!--                    Product-->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Product</h6>
                        </div>
                        <div class="card-body border">
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input type="text"
                                       name="title"
                                       id="product_name"
                                       value="{{old('title', $product->title)}}"
                                       required
                                       placeholder="Product Name"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="product_sku">Product SKU</label>
                                <input type="text" name="sku" id="product_sku" value="{{old('sku', $product->sku)}}"
                                       required placeholder="Product Sku" class="form-control"></div>
                            <div class="form-group mb-0">
                                <label for="product_description">Description</label>
                                <textarea name="description" id="product_description" required rows="4"
                                          class="form-control">{{old('description', $product->description)}}</textarea>
                            </div>
                        </div>
                    </div>
                    <!--                    Media-->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6
                                class="m-0 font-weight-bold text-primary">Media</h6></div>
                        <div class="card-body border">
                            <div id="file-upload" class="dropzone dz-clickable">
                                <div class="dz-default dz-message"><span>Drop files here to upload</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--                Variants-->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6
                                class="m-0 font-weight-bold text-primary">Variants</h6>
                        </div>
                        <div class="card-body pb-0" id="variant-sections">
                            @foreach ($variants as $variant)
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Option</label>
                                            <select id="select2-option-{{$loop->index}}"
                                                    data-index="{{$loop->index}}"
                                                    name="product_variant[{{$loop->index}}][option]"
                                                    class="form-control custom-select select2 select2-option">
                                                <option value="{{$variant->id}}">
                                                    {{$variant->title}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="d-flex justify-content-between">
                                                <span>Value</span>
                                                <a href="#" class="remove-btn" data-index="{{$loop->index}}"
                                                   onclick="removeVariant(event, this);">Remove</a>
                                            </label>
                                            <select id="select2-value-{{$loop->index}}"
                                                    data-index="{{$loop->index}}"
                                                    name="product_variant[{{$loop->index}}][variant][]"
                                                    class="select2 select2-value form-control custom-select"
                                                    multiple="multiple" onchange="updateVariantPreview()">

                                                @foreach($product->productVariants as $productVariant)
                                                    @if($productVariant->variant_id == $variant->id)
                                                        <option value="{{$productVariant->variant}}" selected>
                                                            {{$productVariant->variant}}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer bg-white border-top-0" id="add-btn">
                            <div class="row d-flex justify-content-center">
                                <button class="btn btn-primary add-btn" onclick="addVariant(event);">
                                    Add another option
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow">
                        <div class="card-header text-uppercase">Preview</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th width="33%">Variant</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                    </tr>
                                    </thead>
                                    <tbody id="variant-previews">
                                    @foreach($product->productVariantPrices as $productVariantPrice)
                                        <tr>
                                            <th>
                                                <input type="hidden"
                                                       name="product_variant_prices[{{$loop->index}}][title]"
                                                       value="{{$productVariantPrice->title()}}">
                                                <span class="font-weight-bold">
                                                    {{$productVariantPrice->title()}}
                                                </span>
                                            </th>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{old("product_variant_prices[$loop->index][price]", $productVariantPrice->price ?? 0)}}"
                                                       name="product_variant_prices[{{$loop->index}}][price]"
                                                       required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{old("product_variant_prices[$loop->index][stock]", $productVariantPrice->stock ?? 0)}}"
                                                       name="product_variant_prices[{{$loop->index}}][stock]">
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-lg btn-primary">Save</button>
            <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
        </section>
    </form>
@endsection

@push('page_js')
    <script>
        var uploadedDocumentMap = {}
        $("#file-upload").dropzone({
            url: '{{ route('product.storeMedia') }}',
            maxFilesize: 2, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                $('form').append(`<input type="hidden" name="document[]" value="${response.name}">`)
                uploadedDocumentMap[file.name] = response.name
            },
            removedfile: function (file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedDocumentMap[file.name]
                }
                $('form').find(`input[name="document[]"][value="${name}"]`).remove()
            },
            init: function () {
                @if(isset($product) && $product->productImages)

                var files = {!! json_encode($product->productImages) !!};

                for (var i in files) {
                    var file = files[i];
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append(`<input type="hidden" name="document[]" value="${file.file_name}">`)
                }
                @endif
            }
        });
    </script>

    <script type="text/javascript" src="{{ asset('js/product.js') }}"></script>
    <script>
        @if(session('success'))
        alert("{{ session('success') }}");
        @elseif(session('error'))
        alert("{{ session('error') }}");
        @endif
    </script>
@endpush
