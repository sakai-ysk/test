<?php

namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言。
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        

        $search = $request->input('search'); //商品名の値
        $company_id = $request->input('company_id'); //メーカー名の値
log::info($search);
        $query = Product::query();

        if($search = $request->search){
            $query->where('product_name', 'LIKE', "%{$search}%");
        }

        if (isset($company_id)) {
            $query->where('company_id', $company_id);
        }

        // 最小価格が指定されている場合、その価格以上の商品をクエリに追加
        if($min_price = $request->min_price){
            $query->where('price', '>=', $min_price);
        }
    
        // 最大価格が指定されている場合、その価格以下の商品をクエリに追加
        if($max_price = $request->max_price){
            $query->where('price', '<=', $max_price);
        }
    
        // 最小在庫数が指定されている場合、その在庫数以上の商品をクエリに追加
        if($min_stock = $request->min_stock){
            $query->where('stock', '>=', $min_stock);
        }
    
        // 最大在庫数が指定されている場合、その在庫数以下の商品をクエリに追加
        if($max_stock = $request->max_stock){
            $query->where('stock', '<=', $max_stock);
        }

        // ソートのパラメータが指定されている場合、そのカラムでソートを行う
        if($sort = $request->sort){
            $direction = $request->direction == 'desc' ? 'desc' : 'asc'; // directionがdescでない場合は、デフォルトでascとする
            // もし $request->direction の値が 'desc' であれば、'desc' を返す。
            // そうでなければ'asc' を返す
            $query->orderBy($sort, $direction);// orderBy('カラム名', '並び順')
        }

        $products = $query->paginate(30);


        $company = new Company;
        $companies = $company->getLists();

    


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
    
    
        public function destroy(Request $request) {
            $input = $request->all();
        
//ajaxメソッドから送信されたデータは$requestに格納される
        try {
        
        $product = Product::find($input['product']); 
        // 商品を削除。
        $product->delete();

        return response()->json(['success' => true]);
        
        //
    } catch (\Exception $e) {
        
        
        return response()->json([
            
            
            'success' => false, 'message' => '削除に失敗しました'
            ]);

    }

        
        
    }
}
