<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
// orders tableのモデル→products tableとcarts tableとリレーション
use Illuminate\Support\Facades\Auth;

class EcController extends Controller
{
    public function index()
    {
        $items = Prodaut::all();
        return view('index', ['items' => $items]);
    }

    public function detail($param)
    {
        $id = $param;
        // nameで渡すかは要検討
        $item = Product::where('id', $id)->first();
        return view ('detail',['item'=>$item]);
    }

    public function add(Request $request)
    {
        $this->validate($request, Order::$rules);
        $item = $request -> all();
        $user = Auth::user();
        $item['user_id'] = $user->id;
        $item['cart_id'] = 1;
        // cart_id = 1 で未購入ステータス
        Order::create($item);
        // 同商品をカートに入れた際は、createではなく、updateでamountのみを更新（加算）する場合の条件分岐はcontrollerに記載すべき？
        return redirect('/cart') ;
    }

    public function cart()
    {
        $user = Auth::user();
        $items = Order::where('cart_id', 1)->andWhere('user_id', $user -> id)->get();
        $products = Product::all();
        $param = [
            'items' => $items,
            'products' => $products
        ];
        // itemsとproductsを連想配列でviewに送って、view側でproduct_idが一致するもの同士で、itemsのamountとproductsのpriceを掛け算する想定
        return view('cart', $param);
        // 'param' => $paramと代入し直した方がいい？
    }

    public function purchase()
    {
        $user = Auth::user();
        $items = Order::where('cart_id', 1)->andWhere('user_id', $user -> id)->get();
        $orders = $items -> uuid;
        $items['cart_id'] = 2;
        // $orderにuuidを代入し終えたところで、$itemsのcart_idは2=購入済ステータスに変更
        Order::update($items);
        return view('thanks', ['orders'=> $orders]);
    }
}
