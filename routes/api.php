<?php

use App\Http\Controllers\Api\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


Route::middleware(['api','auth:sanctum','checkbot'])->group(function(){
        Route::post('/start', [TelegramController::class,'start']);
        Route::post('/charge', [TelegramController::class,'charge']);
        Route::post('/ichancy', [TelegramController::class,'ichancy']);
        Route::post('/checkbalance', [TelegramController::class,'checkbalance']);
        Route::post('/newichaccount', [TelegramController::class,'newichaccount']);
        Route::post('/getMyBalance', [TelegramController::class,'getMyBalance']);
        Route::post('/getMyBalanceDir', [TelegramController::class,'getMyBalanceDir']);
        Route::post('/charge_ichancy', [TelegramController::class,'charge_ichancy']);
        Route::post('/withdraw_ichancy', [TelegramController::class,'withdraw_ichancy']);
        Route::post('/withdraw', [TelegramController::class,'withdraw']);
        Route::post('/undo_withdraw', [TelegramController::class,'undo_withdraw']);
        Route::post('/getIchancyBalance', [TelegramController::class,'getIchancyBalance']);
        Route::post('/ex_ich_charge', [TelegramController::class,'ex_ich_charge']);
        Route::post('/ex_withdraw', [TelegramController::class,'ex_withdraw']);
        Route::post('/chargeBemo', [TelegramController::class,'chargeBemo']);
        Route::post('/ex_bemo_charge', [TelegramController::class,'ex_bemo_charge']);
        Route::post('/reject_bemo_charge', [TelegramController::class,'reject_bemo_charge']);
        Route::post('/affiliateQuery', [TelegramController::class,'affiliateQuery']);
        Route::post('/malaki', [TelegramController::class,'malaki']);
        Route::post('/transBalance', [TelegramController::class,'transBalance']);
        Route::post('/execGift', [TelegramController::class,'execGift']);
        Route::post('/newichaccount_v2', [TelegramController::class,'newichaccount_v2']);
        //testing
        Route::post('/charge1', [TelegramController::class,'charge1']);
        Route::post('/chargecash', [TelegramController::class,'chargecash']);
        Route::post('/getSyCashCodes', [TelegramController::class,'getSyCashCodes']);
        Route::post('/getAffPercent', [TelegramController::class,'getAffPercent']);
        Route::post('/chargeOrgignalBeforAsync', [TelegramController::class,'chargeOrgignalBeforAsync']);
        Route::post('/receive_wheel_user_id', [TelegramController::class, 'receive_wheel_user_id']);
        Route::post('/check_wheel_user', [TelegramController::class, 'check_wheel_user']);
        Route::post('/ex_rotate', [TelegramController::class, 'ex_rotate']);
        Route::post('/ex_syr_charge', [TelegramController::class, 'ex_syr_charge']);
        Route::post('/chargecash_manual', [TelegramController::class, 'chargecash_manual']);
        Route::post('/reject_syr_charge', [TelegramController::class, 'reject_syr_charge']);
        Route::post('/charge_history', [TelegramController::class, 'charge_history']);
        Route::post('/withdraw_history', [TelegramController::class, 'withdraw_history']);
        Route::post('/gift_history', [TelegramController::class, 'gift_history']);
        Route::post('/wheel_history', [TelegramController::class, 'wheel_history']);

        Route::post('/testcash', [TelegramController::class, 'testcash']);
    });

Route::middleware('api')->group(function(){
    Route::post('/test', [TelegramController::class,'test']);
    Route::get('/getwebBalance/{chat_id}', [TelegramController::class,'getwebBalance']);
});


// Route::middleware('api')->group(function(){
//     Route::post('/newichaccount', [TelegramController::class,'newichaccount']);
// });
// Route::middleware('api')->group(function(){
//     Route::post('/newichaccount_v2', [TelegramController::class,'newichaccount_v2']);
// });
// // مجرد تضمين التوكين يتم تلقائيا الحصول على بيانات المستخدم المرل للطب
// Route::get('/testSanctum', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/testSanctum', function (Request $request) {
//     if($request->user()->tokenCan('categories:delete')){
//         return ["user" => $request->user(),'can'=>'yes delete'];
//     }
// })->middleware('auth:sanctum');



?>
