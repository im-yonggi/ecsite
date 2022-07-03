<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

// middlewareで全て保護する→可能であればカートに追加しようとしたタイミング以降で設定できるとBetter→HeaderのNmae部はログイン/未ログインでif分岐
Route::get('/index', [EcController::class, 'index']);
// 商品一覧ページで、productsテーブルからitmesを渡して、pagenationで表示
Route::get('/detail/{param}', [EcController::class, 'detail']);
// パスパラメーターで、個別の商品詳細ページに飛ばすように。
Route::post('/add', [EcController::class, 'add']);
// Form（hiddennでid表示→DBのcreate時に参照できるように）で個数選択の上、「カートに入れる」でsubmit→redirctでカートページへ
Route::get('/cart', [EcController::class, 'cart']);
// cartテーブルの内、statusがaddedのものを表示させるように＝＞今の設計だと、同じ商品でもカート追加タイミングが違うと、別商品として認識の可能性＝相談
Route::post('/purchase', [EcController::class, 'purchase']);
// cartテーブルのうち、statusがaddedのものを、boughtに変更の上、Thanksページへ遷移し、UUIDを表示。過去のboughtのUUIDが表示されないように、purchaseメソッドでpostされた物だけを表示。