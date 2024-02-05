@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品一覧画面</h1>

    <!-- 検索フォームのセクション -->
<div class="search mt-5">
    <form action="{{ route('products.index') }}" method="GET" class="row g-3" id="search-form">
    
        <div class="col-sm-12 col-md-2">
            <input type="text" name="search" id="search" class="form-control" placeholder="検索キーワード" value="{{ request('search') }}">
        </div>

        
        <div class="col-sm-12 col-md-2">
            <select name="company_id" id="company_id" class="form-control" value="{{ $company_id }}">
                <option value="">メーカー名</option>
                @foreach($companies as $id => $company_name)
                <option value="{{ $id }}">{{ $company_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- 最小価格の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_price" id="min_price" class="form-control" placeholder="最小価格" value="{{ request('min_price') }}">
        </div>

        <!-- 最大価格の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_price" id="max_price" class="form-control" placeholder="最大価格" value="{{ request('max_price') }}">
        </div>

        <!-- 最小在庫数の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_stock" id="min_stock" class="form-control" placeholder="最小在庫" value="{{ request('min_stock') }}">
        </div>

        <!-- 最大在庫数の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_stock" id="max_stock" class="form-control" placeholder="最大在庫" value="{{ request('max_stock') }}">
        </div>

        <div class="col-sm-12 col-md-1">
            <button class="btn btn-outline-secondary" id="search-btn" type="submit">検索</button>
        </div>
    </form>
</div>


    <div class="products mt-5" id="product-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'asc']) }}">↑</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'desc']) }}">↓</a>
                    </th>
                    <th>在庫数
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'direction' => 'asc']) }}">↑</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'direction' => 'desc']) }}">↓</a>
                    </th>
                    <th>メーカー名</th>
                    <th><a href="{{ route('products.create') }}" class="btn btn-primary mb-3">新規登録</a></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td><img src="{{ asset($product->img_path) }}" alt="商品画像" width="100"></td>
                    <td>{{ $product->product_name }}</td>
                    <td>¥{{ $product->price }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->company->company_name }}</td>
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細</a>
                        </td>
                        <td>
                        
                        <form action="{{ route('destroy', $product->id) }}" method="POST"
                        @csrf
                        
                        <button data-product_id="{{$product->id}}" type="submit" class="btn btn-danger btn-sm mx-1">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    
</div>
@endsection