@extends('layouts.admin')

@section('title', 'Edit Product')

@push('styles')
<style>
    .admin-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    textarea.form-control {
        height: 150px;
    }
    .product-image {
        max-width: 200px;
        margin-bottom: 10px;
    }
    .error-message {
        color: red;
        margin-top: 5px;
        font-size: 0.9em;
    }
</style>
@endpush

@section('content')
    <div class="admin-container">
        <h1>Edit Product</h1>

        @include('components.error-message') {{-- Include error message if any --}}

        <form action="{{ route('admin.update.product', $product->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT') 
            @csrf
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" class="form-control" value="{{ old('price', $product->price) }}" required>
            </div>

            <div class="form-group">
                <label for="image">Current Image</label>
                @if($product->image)
                    <img src="{{ $product->image }}" class="product-image" alt="{{ $product->name }}">
                @endif
                <input type="file" id="image" name="image" class="form-control">
                <small>Leave empty to keep current image</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('admin.products') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    console.log("Products page loaded");
</script>
@endpush
