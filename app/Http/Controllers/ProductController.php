<?php

namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言。
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        try {

        $search = $request->input('search'); //商品名の値
        $company_id = $request->input('company_id'); //メーカー名の値

        $query = Product::query();

        if($search = $request->search){
            $query->where('product_name', 'LIKE', "%{$search}%");
        }

        if (isset($company_id)) {
            $query->where('company_id', $company_id);
        }

        $products = $query->paginate(30);


        $company = new Company;
        $companies = $company->getLists();

    } catch (\Exception $e) {
        return back();
    }


        return view('products.index', [
            'products' => $products,
            'companies' => $companies,
            'search' => $search,
            'company_id' => $company_id
        ]);



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::all();
        //
        return view('products.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required', //requiredは必須という意味
            'company_id' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable', //'nullable'はそのフィールドが未入力でもOKという意味
            'img_path' => 'nullable|image|max:2048',
        ]);
        //
        try {
            // トランザクションの開始
            DB::beginTransaction();

        // 新しく商品を作ります。そのための情報はリクエストから取得します。
        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);

        // リクエストに画像が含まれている場合、その画像を保存します。
        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        // 作成したデータベースに新しいレコードとして保存。
        $product->save();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        return back();
    }

        // 全ての処理が終わったら、商品一覧画面に戻る。
        return redirect('products');

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function show(Product $product)
    {
        return view('products.show', ['product' => $product]);
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function edit(Product $product)
    {
        $companies = Company::all();
        //
        return view('products.edit', compact('product', 'companies'));
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);
        //バリデーションによりフォームに未入力項目があればエラーメッセー発生させる（未入力ですなど）

        try {
            // トランザクションの開始
            DB::beginTransaction();

        // 商品の情報を更新。
        $product->product_name = $request->product_name;
        //productモデルのproduct_nameをフォームから送られたproduct_nameの値に書き換える
        $product->price = $request->price;
        $product->stock = $request->stock;

        // 更新した商品を保存。
        $product->save();
        // モデルインスタンスである$productに対して行われた変更をデータベースに保存するためのメソッド（機能）。

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        return back();
    }

        // 全ての処理が終わったら、商品一覧画面に戻る。
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
        // ビュー画面にメッセージを代入した変数(success)を送る
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function destroy(Product $product)
    {
        try {
        // 商品を削除。
        $product->delete();
        //
    } catch (\Exception $e) {
        return back();
    }

        return redirect('/products');
    }
}
