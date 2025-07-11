<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendTelegramMessage;
use App\Livewire\Setting;
use App\Models\Affiliate;
use App\Models\Category;
use App\Models\Charge;
use App\Models\Chat;
use App\Models\Gift;
use App\Models\Ichancy;
use App\Models\IchTransaction;
use App\Models\Setting as ModelsSetting;
use App\Models\Syriatelcash;
use App\Models\Transfer;
use App\Models\Wheel;
use App\Models\Withdraw;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;

// use GuzzleHttp\RequestOptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Container\Attributes\Log as AttributesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Telegram\Bot\Laravel\Facades\Telegram;

use function Laravel\Prompts\error;

class TelegramController extends Controller
{

    public function ex_rotate(Request $request)
    {
        $validatedData = $request->validate([
            'rotId' => 'required',
        ]);
        $rotId = $validatedData['rotId'];
        $wheel = Wheel::find($rotId);
        $wheel->status = true;
        $saved = $wheel->save();
        if ($saved) {
            $chat = Chat::find($wheel->chat_id);
            $chat->balance += $wheel->amount;
            $savedChat = $chat->save();
            if ($savedChat) {
                if ($wheel->rotation != 4) {
                    return response()->json(['message' => 'تم تحديث رصيدك في البوت']);
                } else {
                    return response()->json(['message' => 'Kingdom Bot']);
                }
            } else {
                return response()->json(['message' => 'فشل زيادة رصيدك في البوت']);
            }
        } else {
            return response()->json(['message' => 'فشل منحك الربح']);
        }
    }

    public function check_wheel_user_laravel($chat_id)
    {
        $today = Carbon::today();
        $exists = Charge::where('chat_id', $chat_id)
            ->where('status', 'complete')
            ->whereDate('created_at', $today)
            ->where('amount', '>=', 10000)
            ->exists();
        return $exists;
    }

    public function receive_wheel_user_id(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|string',
        ]);
        $chatId = $validatedData['user_id'];
        $today = Carbon::today();

        $exists = Wheel::where('chat_id', $chatId)
            // ->where('status', true)
            ->whereDate('created_at', $today)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'exists']);
        }

        $balance = Chat::find($chatId)->balance;

        // مجموع مبالغ الشحنات في اليوم الحالي وحالتها 'complete'
        $chargesSum = DB::table('charges')
            ->where('chat_id', $chatId)
            ->where('status', 'complete')
            ->whereDate('created_at', $today)
            ->sum('amount');

        // مجموع مبالغ السحب في اليوم الحالي وحالتها 'complete'
        $withdrawsSum = DB::table('withdraws')
            ->where('chat_id', $chatId)
            ->where('status', 'complete')
            ->whereDate('created_at', $today)
            ->sum('amount');

        // الفرق بين الشحنات والسحوبات
        $difference = $chargesSum - $withdrawsSum - $balance;
        $crwheel = ["chat_id" => $chatId, "difference" => $difference];
        if (!$this->check_wheel_user_laravel($chatId)) {
            return response()->json(['message' => 'notcharge']);
        } elseif ($difference < 50000) {
            $crwheel["rotation"] = 4;
            $crwheel["amount"] = 0;
            $created = Wheel::create($crwheel);
            if ($created) {
                return response()->json(['message' => 'success', 'result' => 4, 'rotId' => $created->id]); //hard luk
            }
        } elseif ($difference >= 50000 && $difference < 100000) {
            $crwheel["rotation"] = 7;
            $crwheel["amount"] = 1000;
            $created = Wheel::create($crwheel);
            if ($created) {
                return response()->json(['message' => 'success', 'result' => 7, 'rotId' => $created->id]); //1000
            }
        } elseif ($difference >= 100000 && $difference < 200000) {
            $crwheel["rotation"] = 5;
            $crwheel["amount"] = 5000;
            $created = Wheel::create($crwheel);
            if ($created) {
                return response()->json(['message' => 'success', 'result' => 5, 'rotId' => $created->id]); //5000
            }
        } elseif ($difference >= 200000 && $difference < 500000) {
            $crwheel["rotation"] = 3;
            $crwheel["amount"] = 10000;
            $created = Wheel::create($crwheel);
            if ($created) {
                return response()->json(['message' => 'success', 'result' => 3, 'rotId' => $created->id]); //10000
            }
        } elseif ($difference >= 500000 && $difference < 1400000) {
            $crwheel["rotation"] = 1;
            $crwheel["amount"] = 50000;
            $created = Wheel::create($crwheel);
            if ($created) {
                return response()->json(['message' => 'success', 'result' => 1, 'rotId' => $created->id]); //50000
            }
        } elseif ($difference >= 1400000 && $difference < 3000000) {
            $crwheel["rotation"] = 0;
            $crwheel["amount"] = 100000;
            $created = Wheel::create($crwheel);
            if ($created) {
                return response()->json(['message' => 'success', 'result' => 0, 'rotId' => $created->id]); //100000
            }
        } elseif ($difference >= 3000000) {
            $crwheel["rotation"] = 9;
            $crwheel["amount"] = 500000;
            $created = Wheel::create($crwheel);
            if ($created) {
                return response()->json(['message' => 'success', 'result' => 9, 'rotId' => $created->id]); //500000
            }
        } else {
            return response()->json(['message' => 'failed']); // error
        }
    }



    public function check_wheel_user(Request $request)
    {
        $today = Carbon::today();
        $form = $request->all();
        $chat_id = $form["chat_id"];
        $totalAmount = Charge::where('chat_id', $chat_id)
            ->where('status', 'complete')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $countAffiliate = DB::table('chats')
            ->where('affiliate_code', $chat_id)
            ->whereDate('created_at', $today)
            ->count();

        $exists = $totalAmount >= 10000;
        $hasAffiliats = $countAffiliate >= 5;

        return response()->json(['exists' => $exists || $hasAffiliats]);
    }

    public function getwebBalance($chat_id)
    {
        $balance = Chat::where('id', $chat_id)->first()->balance;
        return response()->json(['balance' => $balance]);
    }

    public function start(Request $request)
    {
        $form = Validator::make($request->all(), [
            "id" => "required",
            "username" => "nullable",
            "first_name" => "nullable",
            "last_name" => "nullable",
            "affiliate_code" => "nullable",
        ], [
            'required' => 'الحقل :attribute مطلوب.'
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "validator", "errorMessages" => $errorMessages]);
        }
        $form = $form->validated();
        // $chat = Chat::find($form['id']);
        // if($chat===null){
        //     $chat = Chat::create($form);
        // }
        $chat = Chat::firstOrCreate(['id' => $form['id']], $form);
        if ($chat) {
            return response()->json(["status" => "success"]);
        } else {
            return response()->json(["status" => "failed"]);
        }
    }


    public function chargeBemo(Request $request)
    {
        $form = Validator::make($request->all(), [
            "amount" => "required|numeric",
            "processid" => "required|numeric",
            "chat_id" => "required",
            "method" => "required"
        ], [
            "numeric" => "الرجاء إدخال قيم صحيحة"
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "validator", "message" => $errorMessages]);
        }
        $form = $form->validate();
        $checkCharge = Charge::where("processid", $form['processid'])->first();
        if ($checkCharge) {
            if ($checkCharge['status'] == 'complete') {
                return response()->json(["status" => "failed", "message" => "عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            } else {
                return response()->json(["status" => "failed", "message" => "عملية التحويل موجودة مسبقاً، الرجاء انتظار التنفيذ أو التواصل مع الدعم في حال التأخير"]);
            }
        }
        $charge = Charge::create($form);
        if ($charge) {
            $inlineKeyboard = [
                [
                    ['text' => '✅ تنفيذ', 'callback_data' => 'ex_bemo_charge:' . $charge->id],
                    ['text' => '▶️ متابعة', 'callback_data' => 'pending_bemo_charge:' . $charge->chat->id],
                    ['text' => '❌ رفض', 'callback_data' => 'reject_bemo_charge:' . $charge->chat->id . ':' . $charge->id],
                ]
            ];
            $keyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
            $subscribers = [842668006];
            foreach ($subscribers as $chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    "parse_mode" => "HTML",
                    'text' => '🚨 هنالك عملية شحن بيمو:' . PHP_EOL . '' . PHP_EOL . 'معرف المستخدم: <b><code>' . $charge->chat_id . '</code></b>' . PHP_EOL . 'رقم العملية: <b><code>' . $charge->processid . '</code></b> ' . PHP_EOL . 'المبلغ: <b><code>' . $charge->amount . '</code></b> ل.س' . PHP_EOL . ' المحفظة: ' . $charge->chat->balance . ' NSP' . PHP_EOL . ' الوقت: ' . $charge->created_at . PHP_EOL . ' رقم العملية: ' . $charge->id,
                    'reply_markup' => $keyboard,
                ]);
            }
            return response()->json(["status" => "success", "message" => "🏷 جاري التحقق من عملية الدفع" . PHP_EOL . "" . PHP_EOL . "🏷 ستستغرق العملية بضع دقائق" . PHP_EOL . "" . PHP_EOL . "🏷 شكراً لانتظارك"]);
        } else {
            return response()->json(["status" => "failed", "message" => "حصل خطأ أثناء تنفيذ العملية"]);
        }
    }


    public function chargecash_manual(Request $request)
    {
        $form = Validator::make($request->all(), [
            "amount" => ["required", "numeric", "min:0.1", "max:99999999.9"],
            "processid" => "required|numeric",
            "chat_id" => "required",
            "method" => "required"
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "success", "message" => "💡 الرجاء التحقق من معلومات الدفع وإدخالها وفق الترتيب الصحيح"]);
        }
        $form = $form->validate();
        $checkCharge = Charge::where("processid", $form['processid'])->first();
        if ($checkCharge) {
            if ($checkCharge['status'] == 'complete') {
                return response()->json(["status" => "failed", "message" => "عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            } else {
                return response()->json(["status" => "failed", "message" => "عملية التحويل موجودة مسبقاً، الرجاء انتظار التنفيذ أو التواصل مع الدعم في حال التأخير"]);
            }
        }
        $charge = Charge::create($form);
        if ($charge) {
            $inlineKeyboard = [
                [
                    ['text' => '✅ تنفيذ', 'callback_data' => 'ex_syr_charge:' . $charge->id],
                    ['text' => '▶️ متابعة', 'callback_data' => 'pending_syr_charge:' . $charge->chat->id],
                    ['text' => '❌ رفض', 'callback_data' => 'reject_syr_charge:' . $charge->chat->id . ':' . $charge->id],
                ]
            ];
            $keyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
            $subscribers = [842668006, 7631183476];
            $adminMsg = '🚨 هنالك عملية شحن سيريتل كاش:' . PHP_EOL . '' . PHP_EOL . 'معرف المستخدم: <b><code>' . $charge->chat_id . '</code></b>' . PHP_EOL . 'رقم العملية: <b><code>' . $charge->processid . '</code></b> ' . PHP_EOL . 'المبلغ: <b><code>' . $charge->amount . '</code></b> ل.س' . PHP_EOL . ' المحفظة: ' . $charge->chat->balance . ' NSP' . PHP_EOL . ' الوقت: ' . $charge->created_at . PHP_EOL . ' رقم العملية: ' . $charge->id;
            foreach ($subscribers as $chatId) {
                SendTelegramMessage::dispatch($chatId, $adminMsg, "HTML", $keyboard);
            }
            return response()->json(["status" => "success", "message" => "🏷 جاري التحقق من عملية الدفع" . PHP_EOL . "" . PHP_EOL . "🏷 ستستغرق العملية بضع دقائق" . PHP_EOL . "" . PHP_EOL . "🏷 شكراً لانتظارك"]);
        } else {
            return response()->json(["status" => "failed", "message" => "حصل خطأ أثناء تنفيذ العملية"]);
        }
    }



    public function charge_withoutAsync(Request $request)
    {
        $form = Validator::make($request->all(), [
            "amount" => "required|numeric",
            "processid" => "required|numeric",
            "chat_id" => "required",
            "method" => "required"
        ], [
            "numeric" => "الرجاء إدخال قيم صحيحة"
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "validator", "message" => $errorMessages]);
        }
        $form = $form->validate();
        $tody =  Carbon::now()->format('Y-m-d');
        $checkCharge = Charge::where("processid", $form['processid'])->first();
        if ($checkCharge) {
            if ($checkCharge['status'] == 'complete') {
                return response()->json(["status" => "failed", "message" => "عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            } else {
                return response()->json(["status" => "failed", "message" => "عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////
        $bodys = collect([
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956579&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=5bcd650721e2ab948f595c55b490074ed002d9e9305602e5a04f5f9f04433851&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956601&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=e7f0d6d36bfef4cff116f1076b813b10100bf21166977e33357dcdbaa0dd175e&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=4377068&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=c2b54fdcf4e7b1ac144bad60c6859f113c0da8b131917912ab6b707780f5c4a3&status=2'
        ]);
        // $client = new Client(['proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@43.153.237.55:2334']);
        $client = new Client();
        $pass = false;
        $iter = 0;
        do {
            try {
                $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                        'Host' => 'cash-api.syriatel.sy',
                        'Connection' => 'Keep-Alive',
                        'Accept-Encoding' => 'gzip'
                    ],
                    'body' => $bodys->get($iter),
                    'timeout' => 120
                ]);

                $body = json_decode($response->getBody()->getContents());

                if ($body->code == 1) {
                    $data =  $body->data->data;
                    $desiredAmount = $form['amount'];
                    $desiredTransactionNo = $form['processid'];

                    // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
                    // $found = false;
                    // $matchedAmount = null;
                    // foreach ($data as $item) {
                    //     if ($item->transactionNo == $desiredTransactionNo && $item->amount == $desiredAmount && Carbon::parse($item->date)->toDateString() == $tody && $item->status==1) {
                    //         $found = true;
                    //         $matchedAmount = $item->amount;
                    //         break;
                    //     }
                    // }
                    $found = collect($data)->first(function ($item) use ($desiredTransactionNo, $desiredAmount, $tody) {
                        return $item->transactionNo == $desiredTransactionNo
                            && $item->amount == $desiredAmount
                            && Carbon::parse($item->date)->toDateString() == $tody
                            && $item->status == 1;
                    });
                    if ($found) {
                        $form['status'] = 'complete';
                        $charge = Charge::create($form);
                        if ($charge) {
                            if ($charge->amount >= 5000) {
                                $chat = Chat::find($form['chat_id']);
                                // $chat->balance = $chat->balance +$matchedAmount;
                                $chat->balance = $chat->balance + $found->amount;
                                $chat->save();
                                if (isset($chat->affiliate_code)) {
                                    Affiliate::create([
                                        'client' => $chat->id,
                                        'amount' => $desiredAmount,
                                        'affiliate_amount' => $desiredAmount * 0.03,
                                        'chat_id' => $chat->affiliate_code,
                                        'month_at' => date('Y-m')
                                    ]);
                                }
                                return response()->json(["status" => "success", "message" => "✅ تم شحن رصيدك في البوت بنجاح" . PHP_EOL . "" . PHP_EOL . "💵 مبلغ الشحن:" . PHP_EOL . "" . $desiredAmount . " NSP"]);
                            } else {
                                return response()->json(["status" => "success", "message" => "أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                            }
                        } else {
                            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                        }
                        $pass = true;
                    } elseif ($iter < 2) {
                        $iter = $iter + 1;
                    } else {
                        return response()->json(["status" => "failed", "message" => "❗️ عملية الدفع غير صحيحة"]);
                    }
                } else {
                    return response()->json(["status" => "failed", "message" => "فشل التحقق من عملية الدفع، الرجاء التواصل مع الدعم"]);
                }
            } catch (GuzzleException $e) {
                // return response()->json(["status"=>"failedsy","message"=>"فشلت التحقق الآلي من عملية الدفع، الرجاء إعادة المحاولة"]);
            }
        } while (!$pass);
    }























    public function undo_withdraw(Request $request)
    {
        $withdrawId = $request->withdrawId;
        $withdraw = Withdraw::find($withdrawId);
        if (!$withdraw) {
            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء عملية التراجع"]);
        }
        if ($withdraw->status != "requested") {
            return response()->json(["status" => "failed", "message" => "لايمكن التراجع عن طلبات منفّذة أو ملغية"]);
        }
        $withdraw->status = "canceled";
        $saved = $withdraw->save();
        if ($saved) {
            $subscribers = [842668006, 7631183476];
            foreach ($subscribers as $chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    "parse_mode" => "HTML",
                    'text' => '💡إعلام💡:' . PHP_EOL . 'قام المشترك <b><code>' . $withdraw->chat_id . '</code></b> بالتراجع عن الطلب.' . PHP_EOL . '' . PHP_EOL . 'الوقت: ' . $withdraw->updated_at . '' . PHP_EOL . 'رقم الطلب: ' . $withdraw->id,
                ]);
            }
            return response()->json(["status" => "success", "message" => "تمت عملية التراجع"]);
        } else {
            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء عملية التراجع"]);
        }
    }



    public function ex_ich_charge(Request $request)
    {
        $orderId = $request->orderId;
        $admin_chat_id = $request->chat_id;
        $transacion = IchTransaction::find($orderId);


        $subscribers = [842668006, 7631183476];

        if ($transacion->status != 'requested') {
            return response()->json(["status" => "requested", "message" => "🔔 تم معالجة الطلب في وقت سابق"]);
        }

        if ($transacion->chat->balance < $transacion->amount) {
            foreach ($subscribers as $chatId) {
                if ($chatId != $admin_chat_id) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        "parse_mode" => "HTML",
                        'text' => '❗️النتيجة❗️:' . PHP_EOL . 'لايوجد رصيد كافي في محفظة المستخدم لتنفيذ عملية شحن شحاب أيشانسي' . PHP_EOL . 'معرف اللاعب:<b><code>' . $transacion->ichancy->identifier . '</code></b>',
                    ]);
                }
            }
            $response = Telegram::sendMessage([
                'chat_id' => $transacion->chat_id,
                "parse_mode" => "HTML",
                'text' => '❗️النتيجة❗️:' . PHP_EOL . 'لايوجد رصيد كافي في محفظتك لتنفيذ عملية شحن حساب أيشانسي،قد يكون جرى عملية سحب من المحفظة خلال فترة معالجة الطلب',
            ]);
            return response()->json(["status" => "balance", "message" => "لايوجد رصيد كافي في محفظة المستخدم لشحن المبلغ المطلوب"]);
        }
        $client = new Client();
        $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        $playerId = $transacion->ichancy->identifier;
        $pass = false;
        do {
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/depositToPlayer', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                    'Accept-Encoding' => 'gzip,deflate,br',
                    'Accept' => '*/*',
                    'dnt' => '1',
                    'origin' => 'https://agents.ichancy.com',
                    'sec-fetch-site: same-origin',
                    'sec-fetch-mode: cors',
                    'sec-fetch-dest: empty',
                    'accept-encoding' => 'gzip, deflate, br',
                    'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                    'cookie' => $cookies,
                ],
                'body' => '{"amount":' . $transacion->amount . ',"comment":null,"playerId":"' . $playerId . '","currencyCode":"NSP","currency":"NSP","moneyStatus":5}'
            ]);
            $body2 = json_decode($response2->getBody()->getContents());


            if (is_object($body2->result)) {
                $transacion->status = "complete";
                $saved = $transacion->save();
                $pass = true;
                if ($saved) {
                    $transacion->chat->balance = $transacion->chat->balance - $transacion->amount;
                    $transacion->chat->save();
                    $response = Telegram::sendMessage([
                        'chat_id' => $transacion->chat_id,
                        'text' => '✅ تم شحن حسابك أيشانسي بنجاح:' . PHP_EOL . 'شكراً على انتظارك',
                    ]);
                    foreach ($subscribers as $chatId) {
                        if ($chatId != $admin_chat_id) {
                            $response = Telegram::sendMessage([
                                'chat_id' => $chatId,
                                "parse_mode" => "HTML",
                                'text' => '🔔 الأدمن الآخر:' . PHP_EOL . '' . PHP_EOL . '✅ تم شحن حساب المستخدم بنجاح' . PHP_EOL . 'معرف اللاعب: <b><code>' . $playerId . '</code></b>' . PHP_EOL . ' المبلغ:' . $transacion->amount . ' NSP',
                            ]);
                        }
                    }
                    return response()->json(["status" => "success", "message" => '✅ تم شحن حساب المستخدم بنجاح' . PHP_EOL . 'معرف اللاعب: <code>' . $playerId . '</code>' . PHP_EOL . ' المبلغ:' . $transacion->amount . ' NSP']);
                } else {
                    return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
                }
            } elseif ($body2->result == "ex") {
                $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt' => '1',
                        'origin' => 'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding' => 'gzip, deflate, br',
                        'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                    ],
                    'body' => '{"username": "' . env('AGENT_NAME') . '","password": "' . env("AGENT_PWD") . '"}'
                ]);
                $incom_cookies = $response->getHeader('Set-Cookie');
                $cookies = '';
                foreach ($incom_cookies as $cookie) {
                    // تقسيم النص بناءً على الفاصلة منقوطة
                    $parts = explode(';', $cookie);
                    // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                    $cookies .= trim($parts[0]) . ';';
                }
                // حذف آخر فاصلة منقوطة
                $cookies = rtrim($cookies, ';');
            } elseif ($body2->result == false) {
                foreach ($subscribers as $chatId) {
                    if ($chatId != $admin_chat_id) {
                        $response = Telegram::sendMessage([
                            'chat_id' => $chatId,
                            "parse_mode" => "HTML",
                            'text' => '🔔 الأدمن الآخر:' . PHP_EOL . '' . PHP_EOL . '🔅 حدث خطأ أثناء تنفيذ العملية، تحقق من رصيد الكاشيرة ثم من حركة الحساب في لوحة التحكم',
                        ]);
                    }
                }
                return response()->json(["status" => "failed", "message" => "🔅 حدث خطأ أثناء تنفيذ العملية، تحقق من رصيد الكاشيرة ثم من حركة الحساب في لوحة التحكم"]);
            }
        } while (!$pass);
    }


    public function reject_syr_charge(Request $request)
    {
        $orderId = $request->orderId;
        $charge = Charge::find($orderId);
        if ($charge->status != 'pending') {
            return response()->json(["status" => "failed", "message" => "🔔 تمت معالجة الطلب في وقت سابق"]);
        }
        $charge->status = "reject";
        $saved = $charge->save();
        if ($saved) {
            SendTelegramMessage::dispatch($charge->chat_id, "🚫 عملية الدفع عبر سيريتل كاش غير صحيحة" . PHP_EOL . "" . PHP_EOL . "رقم الطلب: " . $charge->id . "" . PHP_EOL . "المبلغ: " . $charge->amount . "" . PHP_EOL . "رقم العملية: " . $charge->processid . "" . PHP_EOL . "الوقت: " . (Carbon::now())->toDateTimeString(), "HTML");
            $subscribers = [842668006, 7631183476];
            $admin_chat_id = $request->chat_id;
            $otherChatId = collect($subscribers)->first(fn($chatId) => $chatId != $admin_chat_id);
            SendTelegramMessage::dispatch($otherChatId, "الأدمن الآخر:" . PHP_EOL . "" . PHP_EOL . "🔔 تم رفض الطلب بنجاح" . PHP_EOL . "" . PHP_EOL . "رقم الطلب: " . $charge->id . "" . PHP_EOL . "المبلغ: " . $charge->amount . "" . PHP_EOL . "رقم العملية: " . $charge->processid . "" . PHP_EOL . "الوقت: " . (Carbon::now())->toDateTimeString(), "HTML");
            return response()->json(["status" => "success", "message" => "🔔 تم رفض الطلب بنجاح" . PHP_EOL . "" . PHP_EOL . "رقم الطلب: " . $charge->id . "" . PHP_EOL . "المبلغ: " . $charge->amount . "" . PHP_EOL . "رقم العملية: " . $charge->processid . "" . PHP_EOL . "الوقت: " . (Carbon::now())->toDateTimeString()]);
        } else {
            return response()->json(["status" => "failed", "message" => "⛔️ حدث خطأ أثناء رفض الطلب"]);
        }
    }

    public function reject_bemo_charge(Request $request)
    {
        $orderId = $request->orderId;
        $charge = Charge::find($orderId);
        if ($charge->status != 'pending') {
            return response()->json(["status" => "failed", "message" => "🔔 تمت معالجة الطلب في وقت سابق"]);
        }
        $charge->status = "reject";
        $saved = $charge->save();
        if ($saved) {
            $response = Telegram::sendMessage([
                'chat_id' => $charge->chat_id,
                "parse_mode" => "HTML",
                'text' => "🚫 عملية الدفع عبر بيمو غير صحيحة",
            ]);
            return response()->json(["status" => "success", "message" => "🔔 تم رفض الطلب بنجاح"]);
        } else {
            return response()->json(["status" => "failed", "message" => "⛔️ حدث خطأ أثناء رفض الطلب"]);
        }
    }

    public function ex_bemo_charge(Request $request)
    {
        $orderId = $request->orderId;
        $charge = Charge::find($orderId);
        if ($charge->status != 'pending') {
            return response()->json(["status" => "failed", "message" => "🔔 تمت معالجة الطلب في وقت سابق"]);
        }
        $charge->chat->balance = $charge->chat->balance + $charge->amount;
        $saved = $charge->chat->save();
        if ($saved) {
            $charge->status = "complete";
            $charged = $charge->save();
            if ($charged) {
                $response = Telegram::sendMessage([
                    'chat_id' => $charge->chat_id,
                    'text' => '✅ نجاح:' . PHP_EOL . '' . PHP_EOL . '✅ تم تنفيذ عملية الشحن بنجاح عبر بنك بيمو' . PHP_EOL . '' . PHP_EOL . '💵 رصيد حسابك في البوت: ' . $charge->chat->balance . ' NSP',
                ]);
                if (isset($charge->chat->affiliate_code)) {
                    Affiliate::create([
                        'client' => $charge->chat_id,
                        'amount' => $charge->amount,
                        'affiliate_amount' => $charge->amount * 0.03,
                        'chat_id' => $charge->chat->affiliate_code,
                        'month_at' => date('Y-m')
                    ]);
                }
                return response()->json(["status" => "success", "message" => "✅ تم تنفيذ عملية شحن بنجاح عبر بيمو"]);
            } else {
                return response()->json(["status" => "failed", "message" => "🔔 تمت عملية شحن محفظة المستخدم، ولكن فشلت عملية تعديل حالة الطلب"]);
            }
        } else {
            return response()->json(["status" => "failed", "message" => "🔔 فشلت عملية شحن محفظة المستخدم"]);
        }
    }


    public function ex_syr_charge(Request $request)
    {
        $orderId = $request->orderId;
        $charge = Charge::find($orderId);
        if ($charge->status != 'pending') {
            return response()->json(["status" => "failed", "message" => "🔔 تمت معالجة الطلب في وقت سابق"]);
        }
        $setting = ModelsSetting::first();
        $amountWithBonus = $charge->amount;
        if ($setting->bonusStatus) {
            $amountWithBonus += ($charge->amount * ($setting->bonus / 100));
            SendTelegramMessage::dispatch($charge->chat_id, "💰 تمت إضافة بونص: " . $setting->bonus . "%");
        }
        $charge->chat->balance = $charge->chat->balance + $amountWithBonus;
        $saved = $charge->chat->save();
        if ($saved) {
            $charge->status = "complete";
            $charged = $charge->save();
            if ($charged) {
                $userMsgtxt = '✅ نجاح:' . PHP_EOL . '' . PHP_EOL . '✅ تم تنفيذ عملية الشحن بنجاح عبر سيريتل كاش' . PHP_EOL . '' . PHP_EOL . '💵 رصيد حسابك في البوت: ' . $charge->chat->balance . ' NSP';
                SendTelegramMessage::dispatch($charge->chat_id, $userMsgtxt);
                if (isset($charge->chat->affiliate_code)  && $setting->affilliateStatus) {
                    $f_affiliate_amount = $charge->amount * ($setting->affilliate / 100);
                    Affiliate::create([
                        'client' => $charge->chat_id,
                        'amount' => $charge->amount,
                        'affiliate_amount' => $f_affiliate_amount,
                        'chat_id' => $charge->chat->affiliate_code,
                        'month_at' => date('Y-m')
                    ]);
                    $affChat = Chat::find($charge->chat->affiliate_code);
                    $affChat->balance += $f_affiliate_amount;
                    $affChat->save();
                    SendTelegramMessage::dispatch($charge->chat->affiliate_code, "💰 تمت إضافة مبلغ: " . $f_affiliate_amount . "NSP" . " من عملية شحن، وفق نظام الإحالة");
                }
                $adminMsg = "✅ تم تنفيذ عملية شحن بنجاح عبر سيريتل كاش" . PHP_EOL . "" . PHP_EOL . "معرف المستخدم: <code>" . $charge->chat_id . "</code>" . PHP_EOL . "المبلغ: " . $charge->amount . " NSP" . PHP_EOL . "المبلغ النهائي: " . $amountWithBonus . " NSP" . PHP_EOL . "العملية: " . $charge->processid . "" . PHP_EOL . "الوقت: " . (Carbon::now())->toDateTimeString();
                $subscribers = [842668006, 7631183476];
                $admin_chat_id = $request->chat_id;
                $otherChatId = collect($subscribers)->first(fn($chatId) => $chatId != $admin_chat_id);
                SendTelegramMessage::dispatch($otherChatId, "الأدمن الآخر:" . PHP_EOL . "" . PHP_EOL . $adminMsg, "HTML");
                return response()->json(["status" => "success", "message" => $adminMsg]);
            } else {
                return response()->json(["status" => "failed", "message" => "🔔 تمت عملية شحن محفظة المستخدم، ولكن فشلت عملية تعديل حالة الطلب"]);
            }
        } else {
            return response()->json(["status" => "failed", "message" => "🔔 فشلت عملية شحن محفظة المستخدم"]);
        }
    }
    // public function execGift(Request $request)
    // {
    //     $code =  $request->code;
    //     $gift = Gift::where('code', $code)->first();
    //     if(! $gift){
    //         return response()->json(["status"=>"failed","message"=>"🔔 كود الهدية غير صحيح"]);
    //     }
    //     $isStatusPending = $gift->status === 'pending';
    //     if(! $isStatusPending){
    //         return response()->json(["status"=>"failed","message"=>"🔔 كود الهدية منفذ مسبقاً"]);
    //     }
    //     $updated = $gift->update(['status' => 'complete']);
    //     if($updated){
    //         $gift->chat->balance += $gift->amount;
    //         $savedbalanace = $gift->chat->save();
    //         if($savedbalanace){
    //             return response()->json(["success"=>"failed","message"=>"✅ تم تنفيذ الهدية بنجاح وتعديل رصيدك في البوت"]);
    //         }else{
    //             return response()->json(["status"=>"failed","message"=>"🔴 حصل خطأ أثناء تحديث رصيدك بالبوت"]);
    //         }
    //     }else{
    //         return response()->json(["status"=>"failed","message"=>"🔴 حصل خطأ أثناء تحديث قسيمة الهدية"]);
    //     }
    // }

    public function charge_history(Request $request){
        $chat_id =  $request->chat_id;
        $query = Charge::where('chat_id', $chat_id)->where('status', 'complete');
        $count = $query->count();
        $data = $query->orderBy('created_at', 'DESC')->take(25)->get();
        $result="💡 لديك ".$count." عملية شحن".PHP_EOL."".PHP_EOL."";
        if($count>0){
            $counter = 1;
            foreach ($data as $charge) {
                $result.="----------------".$counter.PHP_EOL."مبلغ الشحن: ".$charge->amount."".PHP_EOL."الشحن عبر: ".$charge->method.PHP_EOL."التاريخ: ".$charge->created_at."".PHP_EOL."";
                $counter++;
            }
            return response()->json(["status"=>"success","message"=>$result]);
        }else{
            return response()->json(["status"=>"success","message"=>"❗️ ليس لديك عمليات شحن بعد"]);
        }
    }

    public function withdraw_history(Request $request){
        $chat_id =  $request->chat_id;
        $query = Withdraw::where('chat_id', $chat_id)->where('status', 'complete');
        $count = $query->count();
        $data = $query->orderBy('created_at', 'DESC')->take(25)->get();
        $result="💡 لديك ".$count." عملية سحب".PHP_EOL."".PHP_EOL."";
        if($count>0){
            $counter = 1;
            foreach ($data as $withdraw) {
                $result.="----------------".$counter.PHP_EOL."صافي مبلغ السحب: ".$withdraw->finalAmount."".PHP_EOL."السحب عبر: ".$withdraw->method.PHP_EOL."التاريخ: ".$withdraw->created_at."".PHP_EOL."";
                $counter++;
            }
            return response()->json(["status"=>"success","message"=>$result]);
        }else{
            return response()->json(["status"=>"success","message"=>"❗️ ليس لديك عمليات سحب بعد"]);
        }
    }

    public function gift_history(Request $request){
        $chat_id =  $request->chat_id;
        $query = Gift::where('chat_id', $chat_id)->where('status', 'complete');
        $count = $query->count();
        $data = $query->orderBy('updated_at', 'DESC')->take(25)->get();
        $result="💡 لديك ".$count." هدية".PHP_EOL."".PHP_EOL."";
        if($count>0){
            $counter = 1;
            foreach ($data as $gift) {
                $result.="----------------".$counter.PHP_EOL."مبلغ الهدية: ".$gift->amount."".PHP_EOL."التاريخ: ".$gift->updated_at."".PHP_EOL."";
                $counter++;
            }
            return response()->json(["status"=>"success","message"=>$result]);
        }else{
            return response()->json(["status"=>"success","message"=>"❗️ ليس لديك هدايا منفّذة بعد"]);
        }
    }

    public function wheel_history(Request $request){
        $chat_id =  $request->chat_id;
        $query = Wheel::where('chat_id', $chat_id)->where('status', true);
        $count = $query->count();
        $data = $query->orderBy('updated_at', 'DESC')->take(25)->get();
        $result="💡 لديك ".$count." ضربة".PHP_EOL."".PHP_EOL."";
        if($count>0){
            $counter = 1;
            foreach ($data as $gift) {
                $result.="----------------".$counter.PHP_EOL."مبلغ الربح من الضربة: ".$gift->amount."".PHP_EOL."التاريخ: ".$gift->updated_at."".PHP_EOL."";
                $counter++;
            }
            return response()->json(["status"=>"success","message"=>$result]);
        }else{
            return response()->json(["status"=>"success","message"=>"❗️ ليس لديك محاولات منفّذة بعد"]);
        }
    }

    public function execGift(Request $request)
    {
        $code =  $request->code;
        $chat_id =  $request->chat_id;

        $gift = Gift::where('code', $code)->first();
        if (! $gift) {
            return response()->json(["status" => "failed", "message" => "🔔 كود الهدية غير صحيح"]);
        }
        $isStatusPending = $gift->status === 'pending';
        if (! $isStatusPending) {
            return response()->json(["status" => "failed", "message" => "🔔 كود الهدية منفذ مسبقاً"]);
        }

        if (isset($gift->chat_id)) {
            if ($gift->chat_id == $chat_id) {
                $updated = $gift->update(['status' => 'complete']);
                if ($updated) {
                    $gift->chat->balance += $gift->amount;
                    $savedbalanace = $gift->chat->save();
                    if ($savedbalanace) {
                        return response()->json(["success" => "failed", "message" => "✅ تم تنفيذ الهدية بنجاح وتعديل رصيدك في البوت"]);
                    } else {
                        return response()->json(["status" => "failed", "message" => "🔴 حصل خطأ أثناء تحديث رصيدك بالبوت"]);
                    }
                } else {
                    return response()->json(["status" => "failed", "message" => "🔴 حصل خطأ أثناء تحديث قسيمة الهدية"]);
                }
            } else {
                return response()->json(["status" => "failed", "message" => "🔔 عذراً، البطاقة مخصصة لمستخدم آخر"]);
            }
        } else {
            $updated = $gift->update(['status' => 'complete', 'chat_id' => $chat_id]);
            if ($updated) {
                $gift->chat->balance += $gift->amount;
                $savedbalanace = $gift->chat->save();
                if ($savedbalanace) {
                    return response()->json(["success" => "failed", "message" => "✅ تم تنفيذ الهدية بنجاح وتعديل رصيدك في البوت"]);
                } else {
                    return response()->json(["status" => "failed", "message" => "🔴 حصل خطأ أثناء تحديث رصيدك بالبوت"]);
                }
            } else {
                return response()->json(["status" => "failed", "message" => "🔴 حصل خطأ أثناء تحديث قسيمة الهدية"]);
            }
        }
    }


    public function affiliateQuery(Request $request)
    {
        $form = Validator::make($request->all(), [
            "chat_id" => "required",
        ], [
            'required' => 'الحقل :attribute مطلوب.'
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "validator", "message" => "فشل الحصول على المعرف الخص بك، الرجاء إعادة المحاولة"]);
        }
        $form = $form->validated();
        $totalAffiliateAmount = Affiliate::where('chat_id', $form['chat_id'])
            ->where('month_at', date('Y-m'))
            ->sum('affiliate_amount');

        $totalAffiliateCount = Affiliate::where('chat_id', $form['chat_id'])
            ->where('month_at', date('Y-m'))
            ->count('affiliate_amount');

        $afcount = Chat::where('affiliate_code', $form['chat_id'])->count();



        $today = Carbon::today();
        $year = $today->year;
        $month = $today->month;
        $day = $today->day;

        // if ($day <= 10) {
        //     $startDate = Carbon::create($year, $month, 1)->startOfDay();
        //     $endDate = Carbon::create($year, $month, 10)->endOfDay();
        // } elseif ($day <= 20) {
        //     $startDate = Carbon::create($year, $month, 11)->startOfDay();
        //     $endDate = Carbon::create($year, $month, 20)->endOfDay();
        // } else {
        //     $startDate = Carbon::create($year, $month, 21)->startOfDay();
        //     $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        // }

        $startDate1 = Carbon::create($year, $month, 1)->startOfDay();
        $endDate1 = Carbon::create($year, $month, 10)->endOfDay();
        $totAfAmount1 = Affiliate::where('chat_id', $form['chat_id'])
            ->whereBetween('created_at', [$startDate1, $endDate1])
            ->sum('affiliate_amount');
        $totAfCount1 = Affiliate::where('chat_id', $form['chat_id'])
            ->whereBetween('created_at', [$startDate1, $endDate1])
            ->count('affiliate_amount');
        $afcount1 = Chat::where('affiliate_code', $form['chat_id'])
            ->whereBetween('created_at', [$startDate1, $endDate1])
            ->count();


        $startDate2 = Carbon::create($year, $month, 11)->startOfDay();
        $endDate2 = Carbon::create($year, $month, 20)->endOfDay();
        $totAfAmount2 = Affiliate::where('chat_id', $form['chat_id'])
            ->whereBetween('created_at', [$startDate2, $endDate2])
            ->sum('affiliate_amount');
        $totAfCount2 = Affiliate::where('chat_id', $form['chat_id'])
            ->whereBetween('created_at', [$startDate2, $endDate2])
            ->count('affiliate_amount');
        $afcount2 = Chat::where('affiliate_code', $form['chat_id'])
            ->whereBetween('created_at', [$startDate2, $endDate2])
            ->count();

        $startDate3 = Carbon::create($year, $month, 21)->startOfDay();
        $endDate3 = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        $totAfAmount3 = Affiliate::where('chat_id', $form['chat_id'])
            ->whereBetween('created_at', [$startDate3, $endDate3])
            ->sum('affiliate_amount');
        $totAfCount3 = Affiliate::where('chat_id', $form['chat_id'])
            ->whereBetween('created_at', [$startDate3, $endDate3])
            ->count('affiliate_amount');
        $afcount3 = Chat::where('affiliate_code', $form['chat_id'])
            ->whereBetween('created_at', [$startDate3, $endDate3])
            ->count();

        return response()->json([
            "status" => "success",
            "message" => "
            ⚪️ عدد إحالاتك الكُلية: " . $afcount . "" . PHP_EOL . "" . PHP_EOL . "⚪️ عمليات الشحن الشهرية التي تمت عن طريق إحالاتك: " . $totalAffiliateCount . "" . PHP_EOL . "" . PHP_EOL . "⚪️ العمولة الشهرية: " . $totalAffiliateAmount . "" . PHP_EOL . "" . PHP_EOL . "" .
                "🗓 السنة: " . $year . " الشهر: " . $month . "" . PHP_EOL . "" . PHP_EOL . "" .
                "1️⃣ أول 10 أيام: " . PHP_EOL . "" .
                "- عدد الإحالات: " . $afcount1 . PHP_EOL . "" .
                "- عدد عمليات الشحن: " . $totAfCount1 . PHP_EOL . "" .
                "- مبالغ الإحالات: " . $totAfAmount1 . " NSP" . PHP_EOL . "" . PHP_EOL . "" .

                "2️⃣ ثاني 10 أيام: " . PHP_EOL . "" .
                "- عدد الإحالات: " . $afcount2 . PHP_EOL . "" .
                "- عدد عمليات الشحن: " . $totAfCount2 . PHP_EOL . "" .
                "- مبالغ الإحالات: " . $totAfAmount2 . " NSP" . PHP_EOL . "" . PHP_EOL . "" .

                "3️⃣ ثالث 10 أيام: " . PHP_EOL . "" .
                "- عدد الإحالات: " . $afcount3 . PHP_EOL . "" .
                "- عدد عمليات الشحن: " . $totAfCount3 . PHP_EOL . "" .
                "- مبالغ الإحالات: " . $totAfAmount3 . " NSP" . PHP_EOL . "" .

                "" . PHP_EOL . "" . PHP_EOL . "يجب أن يكون لديك 3 إحلات نشطة على الأقل ليتم صرف العمولة لك."
        ]);
    }


    // public function affiliateQuery(Request $request)
    // {
    //     $form = Validator::make($request->all(), [
    //         "chat_id"=>"required",
    //     ],[
    //         'required'=>'الحقل :attribute مطلوب.'
    //     ]);
    //     if ($form->fails()) {
    //         $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
    //         return response()->json(["status"=>"validator","message"=>"فشل الحصول على المعرف الخص بك، الرجاء إعادة المحاولة"]);
    //     }
    //     $form = $form->validated();
    //     $totalAffiliateAmount = Affiliate::where('chat_id', $form['chat_id'])
    //     ->where('month_at', date('Y-m'))
    //     ->sum('affiliate_amount');

    //     $totalAffiliateCount = Affiliate::where('chat_id', $form['chat_id'])
    //     ->where('month_at', date('Y-m'))
    //     ->count('affiliate_amount');

    //     $afcount = Chat::where('affiliate_code', $form['chat_id'])->count();
    //     return response()->json(["status"=>"success","message"=>"⚪️ عدد إحالاتك الحالية: ".$afcount."".PHP_EOL."".PHP_EOL."⚪️ عمليات الشحن الحالية التي تمت عن طريق إحالاتك: ".$totalAffiliateCount."".PHP_EOL."".PHP_EOL."⚪️ العمولة الشهرية: ".$totalAffiliateAmount."".PHP_EOL."".PHP_EOL."يجب أن يكون لديك 3 إحلات نشطة على الأقل ليتم صرف العمولة لك."]);
    // }

    public function charge_ichancy(Request $request)
    {
        $form = $request->all();
        $form['type'] = 'charge';
        $chat = Chat::find($form['chat_id']);
        $balance = $chat->balance;
        if ($balance < $form["amount"]) {
            return response()->json(["status" => "balance", "message" => "لايوجد رصيد كافي في حسابك لشحن المبلغ المطلوب" . PHP_EOL . "أدخل مبلغ شحن بكافئ رصيدك الحالي في البوت أو دون:"]);
        }
        $count = IchTransaction::where('chat_id', '=', $form["chat_id"])->where('type', '=', 'charge')->where('status', '=', "requested")->count();
        if ($count != 0) {
            return response()->json(["status" => "requested", "message" => "لديك طلب شحن سابق غير معالج، الرجاء الانتظار"]);
        }
        $ichancy = Ichancy::where('chat_id', '=', $form["chat_id"])->first();
        $form["ichancy_id"] = $ichancy->id;
        $client = new Client();
        // $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        // if (!session()->has('cookies')) {
        //     session(['cookies' => $cookies]);
        // }
        $jsonFile = file_get_contents(public_path('data.json'));
        $data = json_decode($jsonFile, true);
        $playerId = $ichancy->identifier;
        $pass = false;
        do {
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/depositToPlayer', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                    'Accept-Encoding' => 'gzip,deflate,br',
                    'Accept' => '*/*',
                    'dnt' => '1',
                    'origin' => 'https://agents.ichancy.com',
                    'sec-fetch-site: same-origin',
                    'sec-fetch-mode: cors',
                    'sec-fetch-dest: empty',
                    'accept-encoding' => 'gzip, deflate, br',
                    'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                    'cookie' => $data['cookies'],
                ],
                'body' => '{"amount":' . $form['amount'] . ',"comment":null,"playerId":"' . $playerId . '","currencyCode":"NSP","currency":"NSP","moneyStatus":5}'
            ]);
            $body2 = json_decode($response2->getBody()->getContents());


            if (is_object($body2->result)) {
                $form["status"] = "complete";
                $transacion = IchTransaction::create($form);
                $pass = true;
                if ($transacion) {
                    $chat->balance = $chat->balance - $form['amount'];
                    $chat->save();
                    return response()->json(["status" => "success", "message" => "✅ تم شحن حسابك بنجاح"]);
                } else {
                    return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
                }
            } elseif ($body2->result == "ex") {
                $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt' => '1',
                        'origin' => 'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding' => 'gzip, deflate, br',
                        'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                    ],
                    'body' => '{"username": "' . env('AGENT_NAME') . '","password": "' . env("AGENT_PWD") . '"}'
                ]);
                $incom_cookies = $response->getHeader('Set-Cookie');
                $cookies = '';
                foreach ($incom_cookies as $cookie) {
                    // تقسيم النص بناءً على الفاصلة منقوطة
                    $parts = explode(';', $cookie);
                    // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                    $cookies .= trim($parts[0]) . ';';
                }
                // حذف آخر فاصلة منقوطة
                $cookies = rtrim($cookies, ';');
                // session(['cookies' => $cookies]);
                $data['cookies'] = $cookies;
                $updatedJson = json_encode($data);
                file_put_contents(public_path('data.json'), $updatedJson);
            } elseif ($body2->result == false) {
                $transacion = IchTransaction::create($form);
                $inlineKeyboard = [
                    [
                        ['text' => '✅ تنفيذ العملية', 'callback_data' => 'ex_ich_charge:' . $transacion->id],
                        ['text' => '▶️ متابعة الطلب', 'callback_data' => 'pending_ich_charge:' . $chat->id],
                    ]
                ];
                $keyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
                $subscribers = [842668006, 7631183476];
                foreach ($subscribers as $chatId) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        "parse_mode" => "HTML",
                        'text' => '🚨 هنالك عملية تعبئة فوق قدرة الكاشيرة الرجاء اعادة التعبئة لاتمام عملية الشحن:' . PHP_EOL . '' . PHP_EOL . 'معرف المستخدم: <b><code>' . $transacion->chat_id . '</code></b>' . PHP_EOL . 'حساب اللاعب: <b><code>' . $ichancy->username . '</code></b> ' . PHP_EOL . 'معرّف اللاعب: <b><code>' . $ichancy->identifier . '</code></b>' . PHP_EOL . ' مبلغ الشحن: ' . $transacion->amount . ' NSP' . PHP_EOL . ' المحفظة: ' . $chat->balance . ' NSP' . PHP_EOL . ' الوقت: ' . $transacion->created_at . PHP_EOL . ' رقم العملية: ' . $transacion->id,
                        'reply_markup' => $keyboard,
                    ]);
                }
                return response()->json(["status" => "failed", "message" => "🔅 سيستغرق شحن حساب أيشانسي قليلاً من الوقت, سيتم إعلامك بإتمام العملية بعد قليل"]);
            }
        } while (!$pass);
    }

    public function withdraw(Request $request)
    {
        $form = $request->all();
        $balance = Chat::select('balance')->where('id', '=', $form["chat_id"])->value('balance');
        if ($balance < $form["amount"]) {
            return response()->json(["status" => "balance", "message" => "لايوجد رصيد كافي في حسابك لسحب المبلغ المطلوب"]);
        }
        $count = Withdraw::where('chat_id', '=', $form["chat_id"])->where('status', '=', "requested")->count();
        if ($count != 0) {
            return response()->json(["status" => "requested", "message" => "لديك طلب سحب سابق غير معالج، الرجاء انتظار المهلة المحددة ومن ثم الاتصال بالدعم للمعالجة"]);
        }
        $amount = $form['amount'];
        if ($amount < 25000) {
            return response()->json(["status" => "minvalue", "message" => "أقل قيمة يمكن سحبها هي 25,000 "]);
        }

        $withdrawPercent = ModelsSetting::first()->extra_col;
        //حساب قيمة الخصم

        $discount = $amount * ($withdrawPercent / 100);
        // الحصول على المبلغ بعد الخصم
        $finalAmount = $amount - $discount;
        $stringValue = strval($finalAmount);
        // القيمة المخصومة
        $discountAmount = $amount - $finalAmount;
        $form['finalAmount'] = $finalAmount;
        $form['discountAmount'] = $discountAmount;
        $withdraw = Withdraw::create($form);
        if ($withdraw) {
            $inlineKeyboard = [
                [
                    ['text' => '✅ تنفيذ العملية', 'callback_data' => 'ex_withdraw:' . $withdraw->id]
                ]
            ];
            $keyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
            $subscribers = [842668006, 7631183476];
            $messagetext = '🚨عاجل🚨:' . PHP_EOL . 'تم إضافة طلب سحب للمشترك <b><code>' . $form["chat_id"] . '</code></b> وبانتظار المعالجة.' . PHP_EOL . '' . PHP_EOL . 'الوقت: ' . $withdraw->created_at . '' . PHP_EOL . 'رقم الطلب: ' . $withdraw->id . '' . PHP_EOL . 'القيمة النهائية: ' . $withdraw->finalAmount . '' . PHP_EOL . 'عبر: ' . $withdraw->method . '' . PHP_EOL . 'كود التحويل: <b><code>' . $withdraw->code . '</code></b>' . PHP_EOL . 'الرصيد الحالي: ' . $withdraw->chat->balance . '';
            if ($withdraw->subscriber != null) {
                $messagetext = $messagetext . '' . PHP_EOL . 'المستفيد: ' . $withdraw->subscriber;
            }
            foreach ($subscribers as $chatId) {
                // $response = Telegram::sendMessage([
                //     'chat_id' => $chatId,
                //     "parse_mode"=>"HTML",
                //     // 'text' => '🚨عاجل🚨:'.PHP_EOL.'تم إضافة طلب سحب للمشترك <b><code>'.$form["chat_id"].'</code></b> وبانتظار المعالجة.'.PHP_EOL.''.PHP_EOL.'الوقت: '.$withdraw->created_at.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'عبر: '.$withdraw->method.''.PHP_EOL.'كود التحويل: <b><code>'.$withdraw->code.'</code></b>'.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.''.PHP_EOL.''.$withdraw->subscriber ,
                //     'text' => $messagetext,
                //     'reply_markup' => $keyboard,
                // ]);
                SendTelegramMessage::dispatch($chatId, $messagetext, "HTML", $keyboard);
            }
            return response()->json(["status" => "success", "message" => "✅ تم طلب السحب بنجاح\nسيتم إعلامك بتنفيذ الطلب خلال ساعة\nمعلومات الطلب:\n\nرقم الطلب: " . $withdraw->id . "\nالطلب: " . $withdraw->code . "\nالقيمة: " . $withdraw->amount . "\nنسبة الاقتطاع: " . $withdrawPercent . "%\nالمبلغ المقتطع: " . $withdraw->discountAmount . "\nالقيمة المستحقة بعد الاقتطاع: " . $withdraw->finalAmount . "\nمعرف المستخدم: " . $withdraw->chat_id . "\nطريقة السحب: " . $withdraw->method, "withdrawId" => $withdraw->id]);
        } else {
            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
        }
    }

    public function ex_withdraw(Request $request)
    {
        $subscribers = [842668006, 7631183476];
        $admin_chat_id = $request->chat_id;
        $withdraw = Withdraw::find($request->orderId);
        if ($withdraw->status != "requested") {
            return response()->json(["status" => "failed", "message" => '❗️ تمت معالجة عملية السحب في وقت لاحق']);
        }
        if ($withdraw->chat->balance < $withdraw->amount) {
            foreach ($subscribers as $chatId) {
                if ($chatId != $admin_chat_id) {
                    // $response = Telegram::sendMessage([
                    //     'chat_id' => $chatId,
                    //     "parse_mode"=>"HTML",
                    //     'text' => '💡 الأدمن الآخر:'.PHP_EOL.'رصيد المستخدم أصبح غير كافي لإتمام عملية السحب'.PHP_EOL.''.PHP_EOL.'معرف المستخدم: <b><code>'.$withdraw->chat_id.'</code></b>'.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP'.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'المبلغ المطلوب سحبه: '.$withdraw->finalAmount,
                    // ]);
                    SendTelegramMessage::dispatch($chatId, '💡 الأدمن الآخر:' . PHP_EOL . 'رصيد المستخدم أصبح غير كافي لإتمام عملية السحب' . PHP_EOL . '' . PHP_EOL . 'معرف المستخدم: <b><code>' . $withdraw->chat_id . '</code></b>' . PHP_EOL . 'الرصيد الحالي: ' . $withdraw->chat->balance . ' NSP' . PHP_EOL . 'رقم الطلب: ' . $withdraw->id . '' . PHP_EOL . 'المبلغ المطلوب سحبه: ' . $withdraw->finalAmount, "HTML");
                }
            }
            // $response = Telegram::sendMessage([
            //     'chat_id' => $withdraw->chat_id,
            //     "parse_mode"=>"HTML",
            //     'text' => "❗️ رصيدك في البوت أصبح غير كافي لإتمام عملية السحب",
            // ]);
            SendTelegramMessage::dispatch($withdraw->chat_id, "❗️ رصيدك في البوت أصبح غير كافي لإتمام عملية السحب", "HTML");
            return response()->json(["status" => "failed", "message" => '❗️ رصيد المستخدم أصبح غير كافي لإتمام عملية السحب' . PHP_EOL . '' . PHP_EOL . 'معرف المستخدم: <b><code>' . $withdraw->chat_id . '</code></b>' . PHP_EOL . 'الرصيد الحالي: ' . $withdraw->chat->balance . ' NSP' . PHP_EOL . 'رقم الطلب: ' . $withdraw->id . '' . PHP_EOL . 'المبلغ المطلوب سحبه: ' . $withdraw->finalAmount]);
        }
        $withdrawPercent = ModelsSetting::first()->extra_col;
        $withdraw->status = "complete";
        $saved = $withdraw->save();
        if ($saved) {
            $withdraw->chat->balance = $withdraw->chat->balance - $withdraw->amount;
            $updated = $withdraw->chat->save();
            if ($updated) {
                foreach ($subscribers as $chatId) {
                    if ($chatId != $admin_chat_id) {
                        // $response = Telegram::sendMessage([
                        //     'chat_id' => $chatId,
                        //     "parse_mode"=>"HTML",
                        //     'text' => '💡 الأدمن الآخر:'.PHP_EOL.''.PHP_EOL.'✅ تم تنفيذ عملية السحب بنجاح'.PHP_EOL.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'معرف المستخدم: '.$withdraw->chat_id.''.PHP_EOL.'القيمة: '.$withdraw->amount.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'نسبة الحسم: '.$withdrawPercent.'%'.PHP_EOL.'القيمة المحسومة: '.$withdraw->discountAmount.''.PHP_EOL.''.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP',
                        // ]);
                        SendTelegramMessage::dispatch($chatId, '💡 الأدمن الآخر:' . PHP_EOL . '' . PHP_EOL . '✅ تم تنفيذ عملية السحب بنجاح' . PHP_EOL . '' . PHP_EOL . 'رقم الطلب: ' . $withdraw->id . '' . PHP_EOL . 'معرف المستخدم: ' . $withdraw->chat_id . '' . PHP_EOL . 'القيمة: ' . $withdraw->amount . '' . PHP_EOL . 'القيمة النهائية: ' . $withdraw->finalAmount . '' . PHP_EOL . 'نسبة الحسم: ' . $withdrawPercent . '%' . PHP_EOL . 'القيمة المحسومة: ' . $withdraw->discountAmount . '' . PHP_EOL . '' . PHP_EOL . 'الرصيد الحالي: ' . $withdraw->chat->balance . ' NSP', "HTML");
                    }
                }
                // $response = Telegram::sendMessage([
                //     'chat_id' => $withdraw->chat_id,
                //     'text' => '✅ تم تنفيذ عملية السحب بنجاح'.PHP_EOL.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'معرف المستخدم: '.$withdraw->chat_id.''.PHP_EOL.'القيمة: '.$withdraw->amount.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'نسبة الحسم: '.$withdrawPercent.'%'.PHP_EOL.'القيمة المحسومة: '.$withdraw->discountAmount.''.PHP_EOL.''.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP',
                // ]);
                SendTelegramMessage::dispatch($withdraw->chat_id, '✅ تم تنفيذ عملية السحب بنجاح' . PHP_EOL . '' . PHP_EOL . 'رقم الطلب: ' . $withdraw->id . '' . PHP_EOL . 'معرف المستخدم: ' . $withdraw->chat_id . '' . PHP_EOL . 'القيمة: ' . $withdraw->amount . '' . PHP_EOL . 'القيمة النهائية: ' . $withdraw->finalAmount . '' . PHP_EOL . 'نسبة الحسم: ' . $withdrawPercent . '%' . PHP_EOL . 'القيمة المحسومة: ' . $withdraw->discountAmount . '' . PHP_EOL . '' . PHP_EOL . 'الرصيد الحالي: ' . $withdraw->chat->balance . ' NSP', "HTML");
                return response()->json(["status" => "success", "message" => '✅ تم تنفيذ عملية السحب بنجاح' . PHP_EOL . '' . PHP_EOL . 'رقم الطلب: ' . $withdraw->id . '' . PHP_EOL . 'معرف المستخدم: ' . $withdraw->chat_id . '' . PHP_EOL . 'القيمة: ' . $withdraw->amount . '' . PHP_EOL . 'القيمة النهائية: ' . $withdraw->finalAmount . '' . PHP_EOL . 'نسبة الحسم: ' . $withdrawPercent . '%' . PHP_EOL . 'القيمة المحسومة: ' . $withdraw->discountAmount . '' . PHP_EOL . '' . PHP_EOL . 'الرصيد الحالي: ' . $withdraw->chat->balance . ' NSP']);
            } else {
                return response()->json(["status" => "failed", "message" => '❗️ فشلت عملية تحديث رصيد المستخدم بعد إتمام السحب']);
            }
        } else {
            return response()->json(["status" => "failed", "message" => '❗️ فشلت عملية السجب']);
        }
    }

    public function getIchancyBalance(Request $request)
    {
        $form = $request->all();
        $ichancy = Ichancy::where('chat_id', $form["chat_id"])->first();
        $playerId = $ichancy->identifier;
        $client = new Client();
        $jsonFile = file_get_contents(public_path('data.json'));
        $data = json_decode($jsonFile, true);
        $pass = false;
        do {
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayerBalanceById', [
                'headers' => [
                    // 'Content-Type' => 'application/json',
                    // 'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                    // 'Accept-Encoding' => 'gzip,deflate,br',
                    // 'Accept' => '*/*',
                    // 'dnt'=> '1',
                    // 'origin'=>'https://agents.ichancy.com',
                    // 'sec-fetch-site: same-origin',
                    // 'sec-fetch-mode: cors',
                    // 'sec-fetch-dest: empty',
                    // 'accept-encoding'=>'gzip, deflate, br',
                    // 'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                    'cookie' => $data['cookies'],
                ],
                'body' => '{"playerId":"' . $playerId . '"}'
            ]);
            $body2 = json_decode($response2->getBody()->getContents());

            if (is_array($body2->result)) {
                $ichancyBalance = data_get(($body2->result)[0], "balance", null);
                return response()->json(["status" => "success", "message" => "💡 معلومات الحساب: ".PHP_EOL."".PHP_EOL."معرف المستخدم: <code>".$ichancy->chat_id."</code>".PHP_EOL."الحساب: <code>".$ichancy->username."</code>".PHP_EOL."كلمة المرور: <code>".$ichancy->password."</code>".PHP_EOL."الرصيد: " . $ichancyBalance . " NSP".PHP_EOL."تاريخ الإنشاء: ".$ichancy->created_at.""]);
            } elseif ($body2->result == "ex") {
                $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt' => '1',
                        'origin' => 'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding' => 'gzip, deflate, br',
                        'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                    ],
                    'body' => '{"username": "' . env('AGENT_NAME') . '","password": "' . env("AGENT_PWD") . '"}'
                ]);
                $incom_cookies = $response->getHeader('Set-Cookie');
                $cookies = '';
                foreach ($incom_cookies as $cookie) {
                    // تقسيم النص بناءً على الفاصلة منقوطة
                    $parts = explode(';', $cookie);
                    // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                    $cookies .= trim($parts[0]) . ';';
                }
                // حذف آخر فاصلة منقوطة
                $cookies = rtrim($cookies, ';');
                $data['cookies'] = $cookies;
                $updatedJson = json_encode($data);
                file_put_contents(public_path('data.json'), $updatedJson);
            }
        } while (!$pass);
    }

    public function withdraw_ichancy(Request $request)
    {
        $form = $request->all();
        $count = IchTransaction::where('chat_id', '=', $form["chat_id"])->where('type', '=', 'withdraw')->where('status', '=', "requested")->count();
        if ($count != 0) {
            return response()->json(["status" => "requested", "message" => "لديك طلب سحب سابق غير معالج، الرجاء الانتظار"]);
        }
        $ichancy = Ichancy::where('chat_id', '=', $form["chat_id"])->first();
        $client = new Client();
        // $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        // if (!session()->has('cookies')) {
        //     session(['cookies' => $cookies]);
        // }
        $jsonFile = file_get_contents(public_path('data.json'));
        $data = json_decode($jsonFile, true);
        $playerId = $ichancy->identifier;
        $pass = false;
        do {
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayerBalanceById', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                    'Accept-Encoding' => 'gzip,deflate,br',
                    'Accept' => '*/*',
                    'dnt' => '1',
                    'origin' => 'https://agents.ichancy.com',
                    'sec-fetch-site: same-origin',
                    'sec-fetch-mode: cors',
                    'sec-fetch-dest: empty',
                    'accept-encoding' => 'gzip, deflate, br',
                    'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                    'cookie' => $data['cookies'],
                ],
                'body' => '{"playerId":"' . $playerId . '"}'
            ]);
            $body2 = json_decode($response2->getBody()->getContents());


            if (is_array($body2->result)) {
                $ichancyBalance = data_get(($body2->result)[0], "balance", null);
                if ($ichancyBalance < $form['amount']) {
                    return response()->json(["status" => "success", "message" => "⛔️ لايوجد رصيد كافي في حسابك أيشانسي لسحب المبلغ"]);
                } else {
                    $response3 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/withdrawFromPlayer', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                            'Accept-Encoding' => 'gzip,deflate,br',
                            'Accept' => '*/*',
                            'dnt' => '1',
                            'origin' => 'https://agents.ichancy.com',
                            'sec-fetch-site: same-origin',
                            'sec-fetch-mode: cors',
                            'sec-fetch-dest: empty',
                            'accept-encoding' => 'gzip, deflate, br',
                            'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                            'cookie' => $data['cookies'],
                        ],
                        'body' => '{"amount":-' . $form['amount'] . ',"comment":null,"playerId":"' . $playerId . '","currencyCode":"NSP","currency":"NSP","moneyStatus":5}'
                    ]);
                    $body3 = json_decode($response3->getBody()->getContents());
                    if ($body3->result == false) {
                        return response()->json(["status" => "success", "message" => "⛔️ حدث خطأ أثناء تنفيذ السحب، الرجاء المحاولة من جديد"]);
                    } elseif ($body2->result !== "ex") {
                        $form["status"] = "complete";
                        $form["ichancy_id"] = $ichancy->id;
                        $transacion = IchTransaction::create($form);
                        $chat = Chat::find($ichancy->chat_id);
                        $chat->balance = $chat->balance + $transacion->amount;
                        $saved = $chat->save();
                        if ($saved) {
                            return response()->json(["status" => "success", "message" => "✅ تم سحب مبلغ: " . $transacion->amount . "NSP من حسابك بنجاح."]);
                        } else {
                            return response()->json(["status" => "success", "message" => "تم السحب لكن فشلت عملية تسجيل العملية لدينا"]);
                        }
                    }
                }
            } elseif ($body2->result == "ex") {
                $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt' => '1',
                        'origin' => 'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding' => 'gzip, deflate, br',
                        'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                    ],
                    'body' => '{"username": "' . env('AGENT_NAME') . '","password": "' . env("AGENT_PWD") . '"}'
                ]);
                $incom_cookies = $response->getHeader('Set-Cookie');
                $cookies = '';
                foreach ($incom_cookies as $cookie) {
                    // تقسيم النص بناءً على الفاصلة منقوطة
                    $parts = explode(';', $cookie);
                    // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                    $cookies .= trim($parts[0]) . ';';
                }
                // حذف آخر فاصلة منقوطة
                $cookies = rtrim($cookies, ';');
                // session(['cookies' => $cookies]);
                $data['cookies'] = $cookies;
                $updatedJson = json_encode($data);
                file_put_contents(public_path('data.json'), $updatedJson);
            }
        } while (!$pass);
    }


    public function getMyBalance(Request $request)
    {
        try {
            $form = $request->chat_id;
            $chat = Chat::find($form);
            return response()->json(["status" => "success", "balance" => $chat["balance"]]);
        } catch (Exception $e) {
            return response()->json(["status" => "success", "message" => $e->getMessage()]);
        }
    }

    public function getMyBalanceDir(Request $request)
    {
        $balance = Chat::where('id', $request->chat_id)->value('balance');
        Telegram::sendMessage([
            'chat_id' => $request->chat_id,
            'parse_mode' => 'HTML',
            'text' => "💵 رصيدك الحالي في البوت:" . PHP_EOL . "<b  style='color: green;'>" . $balance . "</b> NSP",
        ]);
        return response()->json(["status" => "success"]);
    }

    public function newichaccount(Request $request)
    {
        $client = new Client();

        // $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
        //     'headers' => [
        //         'Content-Type' => 'application/json',
        //         'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
        //         'Accept-Encoding' => 'gzip,deflate,br',
        //         'Accept' => '*/*',
        //         'dnt'=> '1',
        //         'origin'=>'https://agents.ichancy.com',
        //         'sec-fetch-site: same-origin',
        //         'sec-fetch-mode: cors',
        //         'sec-fetch-dest: empty',
        //         'accept-encoding'=>'gzip, deflate, br',
        //         'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
        //     ],
        //     'body' => '{"username": "xxxxx","password": "xxxx"}'
        // ]);

        // $body = json_decode($response->getBody()->getContents());

        // if($body->result !==false){
        if (true) {
            //$incom_cookies = $response->getHeader('Set-Cookie');
            //$incom_cookies = ['PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=b387e774c1076ba60fcc6841c50115e6; Path=/; Domain=agents.ichancy.com; Expires=Wed, 16 Apr 2025 00:23:17 GMT','languageCode=ar_IQ; Path=/','language=English%20%28UK%29; Path=/'];
            // $incom_cookies =[
            //     "PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142; Path=/; Domain=agents.ichancy.com; Expires=Wed, 16 Apr 2025 02:10:14 GMT",
            //     "languageCode=en_GB; Path=/",
            //     "language=English%20%28UK%29; Path=/"
            // ];
            // $cookies = [];
            // foreach ($incom_cookies as $cookie) {
            //     list($key, $value) = explode('=', $cookie, 2);
            //     $cookies[$key] = $value;
            // }
            $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
            $form = $request->all();
            $username = $form['e_username'];
            $password = $form['e_password'];
            $emailExt = "@player.nsp";
            $email = $username . $emailExt;
            $pass = false;
            do {
                $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/registerPlayer', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt' => '1',
                        'origin' => 'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding' => 'gzip, deflate, br',
                        'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                        'cookie' => $cookies,

                    ],
                    'body' => '{"player":{"email":"' . $email . '","password":"' . $password . '","parentId":"2322884","login":"' . $username . '"}}'
                ]);
                $body2 = json_decode($response2->getBody()->getContents());

                if ($body2->result == 1) {
                    $pass = true;
                } elseif ($body2->result == "ex") {
                    return response()->json(["status" => "failede", "reason" => "ex"]);
                } else {
                    $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789'); // يحدد الحروف الممكنة
                    $randomString = substr($chars, 0, 4);
                    $username = $username . $randomString;
                    $email = $username . $emailExt;
                    $password = $password . $randomString;
                    $pass = false;
                }
            } while (!$pass);

            if ($pass) {
                $form['username'] = $username;
                $form['password'] = $password;
                $form['status'] = "complete";
                $ichancy = Ichancy::create($form);
                if ($ichancy) {
                    return response()->json(["status" => "success", "message" => "تم إنشاء الحساب بنجاح" . PHP_EOL . "اسم المستخدم: " . $username . "" . PHP_EOL . "كلمة المرور: " . $password . ""]);
                } else {
                    return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                }
            } else {
                return response()->json(["status" => "failed"]);
            }
        } else {
            return response()->json(["status" => "failed"]);
        }
    }

    public function newichaccount1(Request $request)
    {
        $form = $request->all();
        $ichancy = Ichancy::create($form);
        if ($ichancy) {
            return response()->json(["status" => "success", "message" => "تم طلب إنشاء الحساب بنجاح، جاري المعالجة وسيتم إرسال اسم المستخدم وكلمة المرور إليك في أسرع وقت "]);
        } else {
            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
        }
    }

    public function checkbalance(Request $request)
    {
        $form = $request->chat_id;
        $chat = Chat::find($form);
        if ($chat["balance"] >= 10000) {
            return response()->json(["status" => "success"]);
        } else {
            return response()->json(["status" => "failed"]);
        }
    }

    public function ichancy(Request $request)
    {
        $chat_id = $request->input('chat_id');
        $ichancy = Ichancy::where("chat_Id", $chat_id)->first();
        if ($ichancy) {
            if ($ichancy->identifier == null) {
                $client = new Client();
                $jsonFile = file_get_contents(public_path('data.json'));
                $data = json_decode($jsonFile, true);
                $username = $ichancy->username;
                $pass = false;
                $playerId = '';
                do {
                    $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayersForCurrentAgent', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                            'Accept-Encoding' => 'gzip,deflate,br',
                            'Accept' => '*/*',
                            'dnt' => '1',
                            'origin' => 'https://agents.ichancy.com',
                            'sec-fetch-site: same-origin',
                            'sec-fetch-mode: cors',
                            'sec-fetch-dest: empty',
                            'accept-encoding' => 'gzip, deflate, br',
                            'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                            'cookie' => $data['cookies']
                        ],
                        'body' => '{"start":0,"limit":20,"filter":{},"isNextPage":false,"searchBy":{"getPlayersFromChildrenLists":"' . $username . '"}}'
                    ]);
                    $body2 = json_decode($response2->getBody()->getContents());


                    if (is_object($body2->result)) {
                        if (!empty($body2->result->records)) {
                            $users = collect($body2->result->records);
                            $playerId =  data_get($users->firstWhere('username', $username), 'playerId', null);
                            $ichancy->identifier = $playerId;
                            $saved = $ichancy->save();
                            if ($saved) {
                                $pass = true;
                            } else {
                                return response()->json(["status" => "success", "message" => "error_playerId"]);
                            }
                        } else {
                            return response()->json(["status" => "success", "message" => "error_playerId"]);
                        }
                    } elseif ($body2->result == "ex") {
                        $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                                'Accept-Encoding' => 'gzip,deflate,br',
                                'Accept' => '*/*',
                                'dnt' => '1',
                                'origin' => 'https://agents.ichancy.com',
                                'sec-fetch-site: same-origin',
                                'sec-fetch-mode: cors',
                                'sec-fetch-dest: empty',
                                'accept-encoding' => 'gzip, deflate, br',
                                'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                            ],
                            'body' => '{"username": "' . env('AGENT_NAME') . '","password": "' . env("AGENT_PWD") . '"}'
                        ]);
                        $incom_cookies = $response->getHeader('Set-Cookie');
                        $cookies = '';
                        foreach ($incom_cookies as $cookie) {
                            // تقسيم النص بناءً على الفاصلة منقوطة
                            $parts = explode(';', $cookie);
                            // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                            $cookies .= trim($parts[0]) . ';';
                        }
                        // حذف آخر فاصلة منقوطة
                        $cookies = rtrim($cookies, ';');
                        // session(['cookies' => $cookies]);
                        $data['cookies'] = $cookies;
                        $updatedJson = json_encode($data);
                        file_put_contents(public_path('data.json'), $updatedJson);
                    }
                } while (!$pass);
            }
            if ($ichancy['status'] == "requested") {
                return response()->json(["status" => "success", "message" => "requested"]);
            }
            return response()->json(["status" => "success", "message" => "exist", "username" => $ichancy["username"]]);
        } else {
            return response()->json(["status" => "success", "message" => "notexist"]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Category::all());
    }


    public function test(Request $request)
    {
        $this->validate($request, [
            "email" => "required|email|exists:users,email",
            "password" => "required"
        ]);
        if (!auth()->attempt($request->only("email", "password"))) {
            throw new AuthenticationException();
        }
        $token = auth()->user()->createToken("web", ["categories:delete"])->plainTextToken;
        return ["token" => $token];
    }

    /**
     * Store a newly created resource in storage.
     */
    //or Login action
    public function store(Request $request)
    {
        $this->validate($request, [
            "email" => "required|email|exists:users,email",
            "password" => "required"
        ]);
        if (!auth()->attempt($request->only("email", "password"))) {
            throw new AuthenticationException();
        }
        return ["token" => auth()->user()->createToken("web")->plainTextToken];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }












    public function transBalance(Request $request)
    {
        $to = Chat::find($request->user_id);
        if (!$to) {
            return response()->json(["message" => "🔴 لا يوجد مستخدم في البوت بالمعرف المدخل"]);
        }
        $from = Chat::find($request->chat_id);
        if ($from->balance < $request->amount) {
            return response()->json(["message" => "🔴 ليس لديك رصيد كافي لإهداء المبلغ المطلوب"]);
        }
        $from->balance = $from->balance - $request->amount;
        $saved = $from->save();
        if ($saved) {
            $to->balance = $to->balance + $request->amount;
            $savedto = $to->save();
            if ($savedto) {
                Transfer::create(["sender_number" => $request->chat_id, "receiver_number" => $request->user_id, "amount" => $request->amount]);
                return response()->json(["message" => "✅ تم إهداء الرصيد: " . $request->amount . " NSP للمستخدم: " . $request->user_id . " بنجاح"]);
            } else {
                return response()->json(["message" => "🔴 حدث خطأ غير متوفع أثناء تنفيذ عملية الإهداء، الرجاء المحاولة لاحقاً"]);
            }
        } else {
            return response()->json(["message" => "🔴 حدث خطأ غير متوفع أثناء تنفيذ عملية الإهداء، الرجاء المحاولة لاحقاً"]);
        }
    }









    public function malakiMonth(Request $request)
    {
        $currentMonth = date('Y-m');
        $topChats = Chat::select('chats.id', 'chats.username', 'chats.first_name', 'chats.last_name', DB::raw('SUM(charges.amount) as month_total'))
            ->join('charges', 'chats.id', '=', 'charges.chat_id')
            ->where(DB::raw('MONTH(charges.created_at)'), '=', date('m'))
            ->groupBy('chats.id', 'chats.username', 'chats.first_name', 'chats.last_name')
            ->orderBy(DB::raw('SUM(charges.amount)'), 'desc')
            ->limit(5)
            ->get(['chats.id', 'chats.username', 'chats.first_name', 'chats.last_name', DB::raw('SUM(charges.amount) as total_charge_amount')]);
        return response()->json(["topChats" => $topChats]);
    }


    public function malaki(Request $request)
    {
        $today = date('Y-m-d');
        $topChats = Chat::select(
            'chats.id',
            'chats.username',
            'chats.first_name',
            'chats.last_name',
            DB::raw('SUM(charges.amount) as day_total')
        )
            ->join('charges', 'chats.id', '=', 'charges.chat_id')
            ->whereDate('charges.created_at', '=', $today)
            ->groupBy('chats.id', 'chats.username', 'chats.first_name', 'chats.last_name')
            ->orderBy(DB::raw('SUM(charges.amount)'), 'desc')
            ->limit(5)
            ->get([
                'chats.id',
                'chats.username',
                'chats.first_name',
                'chats.last_name',
                DB::raw('SUM(charges.amount) as total_charge_amount')
            ]);

        return response()->json(["topChats" => $topChats]);
    }

    //////////////////////testing




    public function charge1(Request $request)
    {

        $currentMonth = date('Y-m');

        $topChats = Chat::select('chats.id', 'chats.username', 'chats.first_name', 'chats.last_name', DB::raw('SUM(charges.amount) as month_total'))
            ->join('charges', 'chats.id', '=', 'charges.chat_id')
            ->where(DB::raw('MONTH(charges.created_at)'), '=', date('m'))
            ->groupBy('chats.id', 'chats.username', 'chats.first_name', 'chats.last_name')
            ->orderBy(DB::raw('SUM(charges.amount)'), 'desc')
            ->limit(5)
            ->get(['chats.id', 'chats.username', 'chats.first_name', 'chats.last_name', DB::raw('SUM(charges.amount) as total_charge_amount')]);
        return $topChats;
        // $form = Validator::make($request->all(),[
        //         "amount"=>"required|numeric",
        //         "processid"=>"required|numeric",
        //         "chat_id"=>"required"
        // ],[
        //     "numeric"=>"الرجاء إدخال قيم صحيحة"
        // ]);
        // if($form->fails()){
        //     $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
        //     return response()->json(["status"=>"validator","message"=>$errorMessages]);
        // }
        //  $form =$form->validate();
        ///////
        $client = new Client();

        $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                'Host' => 'cash-api.syriatel.sy',
                'Connection' => 'Keep-Alive',
                'Accept-Encoding' => 'gzip'
            ],
            'body' => 'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&sortType=1&mobileManufaturer=samsung&mobileModel=SM-A505F&channelName=4&lang=0&hash=cd939479d1e2c5e0dfb93b428825a77e467c1c890131508fe85199c6e6f6ed07&status=1'
        ]);

        $body = json_decode($response->getBody()->getContents());
        $data =  $body->data->data;
        $desiredAmount = "1100";
        $desiredTransactionNo = '600195060895';
        $found = (bool) array_filter($data, function ($item) use ($desiredAmount, $desiredTransactionNo) {
            return $item->amount == $desiredAmount && $item->transactionNo == $desiredTransactionNo;
        });
        return $found;
        // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
        // $found = false;
        // foreach ($data as $item) {
        //     if ($item->amount == $desiredAmount && $item->transactionNo == $desiredTransactionNo) {
        //         $found = true;
        //         break;
        //     }
        // }
        // if($found){
        // $form['status']='complete';
        // $charge = Charge::create($form);
        //     if($charge){
        //         return response()->json(["status"=>"success","message"=>"شكراً لك، سيتم شحن رصيد في البوت فور التحقق من عملية الدفع وإعلامك على الفور."]);
        //     }else{
        //             return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
        //     }
        // }else{
        //     return response()->json(["status"=>"failed","message"=>"عملية الدفع غير صحيحة" ]);
        // }
    }



    // Testing auto withdrow
    public function withdraw_auto(Request $request)
    {
        $form = $request->all();
        $balance = Chat::select('balance')->where('id', '=', $form["chat_id"])->value('balance');
        if ($balance < $form["amount"]) {
            return response()->json(["status" => "balance", "message" => "لايوجد رصيد كافي في حسابك لسحب المبلغ المطلوب"]);
        }
        $count = Withdraw::where('chat_id', '=', $form["chat_id"])->where('status', '=', "requested")->count();
        if ($count != 0) {
            return response()->json(["status" => "requested", "message" => "لديك طلب سحب سابق غير معالج، الرجاء الاتصال بالدعم للمعالجة"]);
        }
        $client = new Client();
        $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/features/ePayment/refresh_balance', [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                'Host' => 'cash-api.syriatel.sy',
                'Connection' => 'Keep-Alive',
                'Accept-Encoding' => 'gzip'
            ],
            'body' => '{"appVersion":"5.5.2","mobileManufaturer":"samsung","mobileModel":"SM-A505F","lang":"0","systemVersion":"Android+v11","deviceId":"ffffffff-fa8d-e3ca-ffff-ffffef05ac4a","userId":"1657180","hash":"5611b0377dfe37a88541f4aa8eaa3b4f795e08fdfe1af02fdf907cda47326205"}'
        ]);

        $body = json_decode($response->getBody()->getContents());
        if ($body->code == 1) {
            $mySyrialeCashBalance =  $body->data->data[0]->CUSTOMER_BALANCE;
            $amount = $form['amount'];

            // return response()->json(["discount"=>$discount,"finalAmount"=>$finalAmount,"discountAmount"=>$discountAmount,"amount"=>$amount]);
            if ($amount > $mySyrialeCashBalance) {
                return response()->json(["status" => "failed", "message" => "لا يتوفر لدينا حالياً المبلغ المطلوب للسحب، الرجاء التواصل مع الدعم أو المحاولة في وقت لاحق"]);
            } else {
                //حساب قيمة الخصم (10٪)
                $discount = $amount * 0.1;

                // الحصول على المبلغ بعد الخصم
                $finalAmount = $amount - $discount;
                $stringValue = strval($finalAmount);
                // القيمة المخصومة
                $discountAmount = $amount - $finalAmount;

                $reqcheckbody = 'appVersion=5.5.2&mobileManufaturer=samsung&mobileModel=SM-A505F&lang=0&customerCodeOrGSM=' . $form['code'] . '&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&transactAmount=' . $finalAmount . '&hash=bf2032ac5155c820b05c8288e3b4f6bf7b59b6527111d655540b50f0e484a4fd&';
                $response_check = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/checkCustomer', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                        'Host' => 'cash-api.syriatel.sy',
                        'Connection' => 'Keep-Alive',
                        'Accept-Encoding' => 'gzip'
                    ],
                    'body' => $reqcheckbody
                ]);
                $body_check_trans = json_decode($response_check->getBody()->getContents());
                $feeAmount = null;
                $billcode = null;
                if ($body_check_trans->code == 1) {
                    $feeAmount =  $body->data->data[0]->feeAmount;
                    $billcode =  $body->data->data[0]->billcode;
                }

                if ($feeAmount && $billcode) {
                    $reqTransbody = 'appVersion=5.5.2&amount=1200&fee=' . $feeAmount . '&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&toGSM=' . $form['code'] . '&mobileManufaturer=samsung&mobileModel=SM-A505F&pinCode=1234&billcode=' . $billcode . '&lang=0&secretCodeOrGSM=' . $form['code'] . '&hash=af21aa4561da93f07045d1b567c71ca2145954ab284affa636ed38b2ff6d3e97&';
                    return $reqTransbody;
                    $response_trans = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/transfer', [
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                            'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                            'Host' => 'cash-api.syriatel.sy',
                            'Connection' => 'Keep-Alive',
                            'Accept-Encoding' => 'gzip'
                        ],
                        'body' => $reqTransbody
                    ]);
                    $body_trans = json_decode($response_trans->getBody()->getContents());
                    if ($body_trans->code == 1 && $body_trans->message == "تمت العملية بنجاح") {
                        $form['finalAmount'] = $finalAmount;
                        $form['discountAmount'] = $discountAmount;
                        $form['status'] = 'complete';
                        $withdraw = Withdraw::create($form);
                        if ($withdraw) {
                            $chat = Chat::find($form['chat_id']);
                            $chat->balance = $chat->balance - $amount;
                            $chat->save();
                            return response()->json(["status" => "success", "message" => "تم تحويل المبلغ: " . $finalAmount . "NSP إلى الرقم: " . $form['code'] . " وذلك بعد خصم نسبة 10% من المبلغ الكلي المطلوب، بما يعادل: " . $discountAmount . " NSP"]);
                        } else {
                            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
                        }
                    } else {
                        return response()->json(["status" => "failed", "message" => "نواجه مشكلة أثناء تنفيذ عملية السحب الآلي، الرجاء التواصل مع الدعم", "msg" => $body_trans]);
                    }
                }
            }
        }
    }



    public function newichaccount_v2(Request $request)
    {
        $client = new Client();
        $jsonFile = file_get_contents(public_path('data.json'));
        $data = json_decode($jsonFile, true);
        $form = $request->all();
        $username = $form['e_username'];
        $password = $form['e_password'];
        $emailExt = "@player.nsp";
        $email = $username . $emailExt;
        $pass = false;
        $iter = 0;
        do {
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/registerPlayer', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                    'Accept-Encoding' => 'gzip,deflate,br',
                    'Accept' => '*/*',
                    'dnt' => '1',
                    'origin' => 'https://agents.ichancy.com',
                    'sec-fetch-site: same-origin',
                    'sec-fetch-mode: cors',
                    'sec-fetch-dest: empty',
                    'accept-encoding' => 'gzip, deflate, br',
                    'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                    'cookie' => $data['cookies'],
                ],
                'body' => '{"player":{"email":"' . $email . '","password":"' . $password . '","parentId":"2322884","login":"' . $username . '"}}'
            ]);
            $body2 = json_decode($response2->getBody()->getContents());
            Log::error('regplayer -> '.$body2);
            if ($body2->result == 1) {
                $pass = true;
            } elseif ($body2->result == "ex") {
                $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt' => '1',
                        'origin' => 'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding' => 'gzip, deflate, br',
                        'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                    ],
                    'body' => '{"username": "' . env('AGENT_NAME') . '","password": "' . env("AGENT_PWD") . '"}'
                ]);
                $incom_cookies = $response->getHeader('Set-Cookie');
                $cookies = '';
                foreach ($incom_cookies as $cookie) {
                    // تقسيم النص بناءً على الفاصلة منقوطة
                    $parts = explode(';', $cookie);
                    // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                    $cookies .= trim($parts[0]) . ';';
                }
                // حذف آخر فاصلة منقوطة
                $cookies = rtrim($cookies, ';');
                // session(['cookies' => $cookies]);
                $data['cookies'] = $cookies;
                $updatedJson = json_encode($data);
                $iter = $iter+1;
                // file_put_contents(public_path('data.json'), $updatedJson);
                try {
                    file_put_contents(public_path('data.json'), $updatedJson);
                } catch (Exception $e) {
                    Log::error('خطأ في كتابة ملف data.json: ' . $e->getMessage());
                    // يمكنك أيضاً إظهار رسالة للمستخدم أو التعامل مع الخطأ كما تريد
                }
            } else {
                $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789'); // يحدد الحروف الممكنة
                $randomString = substr($chars, 0, 1);
                $username = $username . $randomString;
                if (strlen($username) > 16) {
                    $username = substr($username, 1);
                }
                $iter = $iter+1;
                Log::error('exist user -> '.$username);
                $email = $username . $emailExt;
                // $password=$password.$randomString;
                $pass = false;
            }
        } while (!$pass || $iter < 10);

        if ($pass) {
            $todayy = Carbon::now()->format('Y/m/d');
            Log::error('todayy -> '.$todayy);
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Statistics/getPlayersStatisticsPro', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                    'Accept-Encoding' => 'gzip,deflate,br',
                    'Accept' => '*/*',
                    'dnt' => '1',
                    'origin' => 'https://agents.ichancy.com',
                    'sec-fetch-site: same-origin',
                    'sec-fetch-mode: cors',
                    'sec-fetch-dest: empty',
                    'accept-encoding' => 'gzip, deflate, br',
                    'accept-language' => 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                    'cookie' => $data['cookies']
                ],
                'body' => '{"start":0,"limit":10,"filter":{"registrationDate":{"action":"between","from":"' . $todayy . '","to":"' . $todayy . '","valueLabel":"' . $todayy . ' - ' . $todayy . '","staticDataKey":"registrationDate","label":"registrationDate"},"affiliateId":{"action":"=","value":2322884,"valueLabel":2322884}}}'
            ]);
            $body2 = json_decode($response2->getBody()->getContents());
            Log::error('get player id -> '.$body2);

            if (is_object($body2->result)) {
                if (!empty($body2->result->records)) {
                    $users = collect($body2->result->records);
                    $playerId =  data_get($users->firstWhere('username', $username), 'playerId', null);
                    $form['username'] = $username;
                    $form['password'] = $password;
                    $form['identifier'] = $playerId;
                    $form['status'] = "complete";
                    $ichancy = Ichancy::create($form);
                    if ($ichancy) {
                        return response()->json(["status" => "success", "message" => "✅ تم إنشاء الحساب بنجاح" . PHP_EOL . "" . PHP_EOL . "👤 اسم المستخدم: <code>" . $username . "</code>" . PHP_EOL . "🔐 كلمة المرور: <code>" . $password . "</code>"]);
                    } else {
                        return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                    }
                } else {
                    return response()->json(["status" => "success", "message" => "error_playerId"]);
                }
            }
        } else {
            return response()->json(["status" => "failed"]);
        }
    }





    public function chargeAsync(Request $request)
    {
        $form = Validator::make($request->all(), [
            "amount" => "required|numeric",
            "processid" => "required|numeric",
            "chat_id" => "required",
            "method" => "required"
        ], [
            "numeric" => "الرجاء إدخال قيم صحيحة"
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "validator", "message" => $errorMessages]);
        }
        $form = $form->validate();
        $tody =  Carbon::now()->format('Y-m-d');
        $checkCharge = Charge::where("processid", $form['processid'])->first();
        if ($checkCharge) {
            if ($checkCharge['status'] == 'complete') {
                return response()->json(["status" => "failed", "message" => "عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            } else {
                return response()->json(["status" => "failed", "message" => "عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////
        $requests = [];
        $maxAttempts = 3;
        $bodys = [
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956579&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=5bcd650721e2ab948f595c55b490074ed002d9e9305602e5a04f5f9f04433851&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956601&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=e7f0d6d36bfef4cff116f1076b813b10100bf21166977e33357dcdbaa0dd175e&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=4377068&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=c2b54fdcf4e7b1ac144bad60c6859f113c0da8b131917912ab6b707780f5c4a3&status=2'
        ];
        $client = new Client();
        $desiredAmount = $form['amount'];
        $desiredTransactionNo = $form['processid'];
        for ($i = 0; $i < $maxAttempts; $i++) {
            $requests[] = $client->postAsync('https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                    'Host' => 'cash-api.syriatel.sy',
                    'Connection' => 'Keep-Alive',
                    'Accept-Encoding' => 'gzip'
                ],
                'body' => $bodys[$i],
                'timeout' => 120
            ]);
        }

        // انتظار كل الوعود (Promises) لتكتمل
        $results = Utils::settle($requests)->wait();
        $foundItem = null;
        foreach ($results as $result) {
            if ($result['state'] === 'fulfilled') {
                $response = $result['value'];
                $body = json_decode($response->getBody()->getContents());
                if ($body->code == 1) {
                    $data = collect($body->data->data);

                    $item = $data->first(function ($item) use ($desiredTransactionNo, $desiredAmount, $tody) {
                        return $item->transactionNo == $desiredTransactionNo
                            && $item->amount == $desiredAmount
                            && Carbon::parse($item->date)->toDateString() == $tody
                            && $item->status == 1;
                    });

                    if ($item) {
                        $form['status'] = 'complete';
                        $charge = Charge::create($form);
                        if ($charge) {
                            if ($charge->amount >= 5000) {
                                $chat = Chat::find($form['chat_id']);
                                // $chat->balance = $chat->balance +$matchedAmount;
                                $chat->balance = $chat->balance + $item->amount;
                                $chat->save();
                                if (isset($chat->affiliate_code)) {
                                    Affiliate::create([
                                        'client' => $chat->id,
                                        'amount' => $desiredAmount,
                                        'affiliate_amount' => $desiredAmount * 0.03,
                                        'chat_id' => $chat->affiliate_code,
                                        'month_at' => date('Y-m')
                                    ]);
                                }
                                return response()->json(["status" => "success", "message" => "✅ تم شحن رصيدك في البوت بنجاح" . PHP_EOL . "" . PHP_EOL . "💵 مبلغ الشحن:" . PHP_EOL . "" . $desiredAmount . " NSP"]);
                            } else {
                                return response()->json(["status" => "success", "message" => "أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                            }
                        } else {
                            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                        }
                    }
                }
            }
        }
        if (!$foundItem) {
            return response()->json(["status" => "failed", "message" => "❗️ عملية الدفع غير صحيحة"]);
        }
    }












    public function charge_true(Request $request)
    {
        $form = Validator::make($request->all(), [
            "amount" => "required|numeric",
            "processid" => "required|numeric",
            "chat_id" => "required",
            "method" => "required"
        ], [
            "numeric" => "الرجاء إدخال قيم صحيحة"
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "validator", "message" => $errorMessages]);
        }
        $form = $form->validate();
        $tody =  Carbon::now()->format('Y-m-d');
        $checkCharge = Charge::where("processid", $form['processid'])->first();
        if ($checkCharge) {
            if ($checkCharge['status'] == 'complete') {
                return response()->json(["status" => "failed", "message" => "عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            } else {
                return response()->json(["status" => "failed", "message" => "عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////
        $requests = [];
        $maxAttempts = 3;
        $bodys = [
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956579&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=5bcd650721e2ab948f595c55b490074ed002d9e9305602e5a04f5f9f04433851&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956601&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=e7f0d6d36bfef4cff116f1076b813b10100bf21166977e33357dcdbaa0dd175e&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=4377068&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=c2b54fdcf4e7b1ac144bad60c6859f113c0da8b131917912ab6b707780f5c4a3&status=2'
        ];
        $client = new Client();
        $desiredAmount = $form['amount'];
        $desiredTransactionNo = $form['processid'];
        for ($i = 0; $i < $maxAttempts; $i++) {
            $requests[] = $client->postAsync('https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                    'Host' => 'cash-api.syriatel.sy',
                    'Connection' => 'Keep-Alive',
                    'Accept-Encoding' => 'gzip'
                ],
                'body' => $bodys[$i],
                'timeout' => 120
            ]);
        }

        // انتظار كل الوعود (Promises) لتكتمل
        $results = Utils::settle($requests)->wait();
        $foundItem = null;
        foreach ($results as $result) {
            if ($result['state'] === 'fulfilled') {
                $response = $result['value'];
                $body = json_decode($response->getBody()->getContents());

                if ($body->code == 1) {
                    $data = collect($body->data->data);

                    $foundItem = $data->first(function ($item) use ($desiredTransactionNo, $desiredAmount, $tody) {
                        return $item->transactionNo == $desiredTransactionNo
                            && $item->amount == $desiredAmount
                            && Carbon::parse($item->date)->toDateString() == $tody
                            && $item->status == 1;
                    });

                    if ($foundItem) {
                        $form['status'] = 'complete';
                        $charge = Charge::create($form);
                        if ($charge) {
                            if ($charge->amount >= 5000) {
                                $chat = Chat::find($form['chat_id']);
                                // $chat->balance = $chat->balance +$foundItem->amount;

                                $amountWithBonus = $foundItem->amount * 1.05;
                                $chat->balance = $chat->balance + $amountWithBonus;

                                $chat->save();
                                if (isset($chat->affiliate_code)) {
                                    Affiliate::create([
                                        'client' => $chat->id,
                                        'amount' => $desiredAmount,
                                        'affiliate_amount' => $desiredAmount * 0.03,
                                        'chat_id' => $chat->affiliate_code,
                                        'month_at' => date('Y-m')
                                    ]);
                                }
                                return response()->json(["status" => "success", "message" => "✅ تم شحن رصيدك في البوت بنجاح" . PHP_EOL . "" . PHP_EOL . "💵 مبلغ الشحن:" . PHP_EOL . "" . $desiredAmount . " NSP"]);
                            } else {
                                return response()->json(["status" => "success", "message" => "أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                            }
                        } else {
                            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                        }
                    }
                }
            }
        }
        if (!$foundItem) {
            return response()->json(["status" => "failed", "message" => "❗️ عملية الدفع غير صحيحة"]);
        }
    }










    public function charge(Request $request)
    {

        $form = Validator::make($request->all(), [
            "amount" => "required|numeric",
            "processid" => "required|numeric",
            "chat_id" => "required",
            "method" => "required"
        ], [
            "numeric" => "الرجاء إدخال قيم صحيحة"
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "validator", "message" => $errorMessages]);
        }
        $form = $form->validate();
        $tody =  Carbon::now()->format('Y-m-d');
        $checkCharge = Charge::where("processid", $form['processid'])->first();
        if ($checkCharge) {
            if ($checkCharge['status'] == 'complete') {
                return response()->json(["status" => "failed", "message" => "عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            } else {
                return response()->json(["status" => "failed", "message" => "عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////
        $bodys = collect([
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956579&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=5bcd650721e2ab948f595c55b490074ed002d9e9305602e5a04f5f9f04433851&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956601&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=e7f0d6d36bfef4cff116f1076b813b10100bf21166977e33357dcdbaa0dd175e&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=4377068&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=c2b54fdcf4e7b1ac144bad60c6859f113c0da8b131917912ab6b707780f5c4a3&status=2'
        ]);
        // $client = new Client(['proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@43.153.237.55:2334']);
        $client = new Client();
        $pass = false;
        $iter = 0;
        do {
            try {
                $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                        'Host' => 'cash-api.syriatel.sy',
                        'Connection' => 'Keep-Alive',
                        'Accept-Encoding' => 'gzip'
                    ],
                    'body' => $bodys->get($iter),
                    'timeout' => 120
                ]);

                $body = json_decode($response->getBody()->getContents());

                if ($body->code == 1) {
                    $data =  $body->data->data;
                    $desiredAmount = $form['amount'];
                    $desiredTransactionNo = $form['processid'];

                    // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
                    $found = false;
                    $matchedAmount = null;
                    foreach ($data as $item) {
                        if ($item->transactionNo == $desiredTransactionNo && $item->amount == $desiredAmount && Carbon::parse($item->date)->toDateString() == $tody  && $item->status == 1) {
                            $found = true;
                            $matchedAmount = $item->amount;
                            break;
                        }
                    }
                    if ($found) {
                        $form['status'] = 'complete';
                        $charge = Charge::create($form);
                        if ($charge) {
                            if ($charge->amount >= 5000) {
                                $chat = Chat::find($form['chat_id']);
                                $amountWithBonus = $matchedAmount * 1.05;
                                $chat->balance = $chat->balance + $amountWithBonus;
                                $chat->save();
                                if (isset($chat->affiliate_code)) {
                                    $f_affiliate_amount = $desiredAmount * 0.07;
                                    Affiliate::create([
                                        'client' => $chat->id,
                                        'amount' => $desiredAmount,
                                        'affiliate_amount' => $f_affiliate_amount,
                                        'chat_id' => $chat->affiliate_code,
                                        'month_at' => date('Y-m')
                                    ]);
                                    $affChat = Chat::find($chat->affiliate_code);
                                    $affChat->balance += $f_affiliate_amount;
                                    $affChat->save();
                                }
                                $subscribers = [842668006, 7631183476];
                                foreach ($subscribers as $chatId) {
                                    SendTelegramMessage::dispatch($chatId, "🗳 عملية شحن جديدة:" . PHP_EOL . "" . PHP_EOL . "المستخدم: " . $chat->id . "" . PHP_EOL . "" . PHP_EOL . "المبلغ: " . $matchedAmount . " NSP" . PHP_EOL . "" . PHP_EOL . "رقم العملية: " . $desiredTransactionNo . "" . PHP_EOL . "" . PHP_EOL . "الوقت: " . (Carbon::now())->toDateTimeString() . "", 'HTML');
                                }
                                return response()->json(["status" => "success", "message" => "✅ شكراً لك، تم شحن رصيدك في البوت بنجاح."]);
                            } else {
                                return response()->json(["status" => "success", "message" => "أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                            }
                        } else {
                            return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                        }
                        $pass = true;
                    } elseif ($iter < 2) {
                        $iter = $iter + 1;
                    } else {
                        return response()->json(["status" => "failed", "message" => "عملية الدفع غير صحيحة"]);
                    }
                } else {
                    return response()->json(["status" => "failed", "message" => "فشل التحقق من عملية الدفع، الرجاء التواصل مع الدعم"]);
                }
            } catch (GuzzleException $e) {
                // return response()->json(["status"=>"failedsy","message"=>"فشلت التحقق الآلي من عملية الدفع، الرجاء إعادة المحاولة"]);
            }
        } while (!$pass);
    }


    public function getSyCashCodes(Request $request)
    {
        $result = "";
        $codes = Syriatelcash::where('status', true)->orderBy('codeOrder')->pluck('code');
        foreach ($codes as $code) {
            $result .= PHP_EOL . "<code>" . $code . "</code>";
        }
        return response()->json(["status" => "success", "message" => $result]);
    }



    public function getAffPercent(Request $request)
    {
        $affPercent = ModelsSetting::first()->affilliate;
        return response()->json(["status" => "success", "message" => $affPercent]);
    }


    public function chargecash_orginal(Request $request)
    {

        $form = Validator::make($request->all(),[
                "amount"=>["required", "numeric", "min:0.1", "max:99999999.9"],
                "processid"=>"required|numeric",
                "chat_id"=>"required",
                "method"=>"required"
        ]);
        if($form->fails()){
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status"=>"success","message"=>"💡 الرجاء التحقق من معلومات الدفع وإدخالها وفق الترتيب الصحيح"]);
        }
        $form =$form->validate();
        $tody =  Carbon::now()->format('Y-m-d');
        $checkCharge = Charge::where("processid",$form['processid'])->first();
        if($checkCharge){
            if($checkCharge['status']=='complete'){
                return response()->json(["status"=>"failed","message"=>"عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            }else{
                return response()->json(["status"=>"failed","message"=>"عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////

        // $bodys =collect([
        //     'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956579&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=5bcd650721e2ab948f595c55b490074ed002d9e9305602e5a04f5f9f04433851&status=2',
        //     'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956601&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=e7f0d6d36bfef4cff116f1076b813b10100bf21166977e33357dcdbaa0dd175e&status=2',
        //     'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=4377068&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=c2b54fdcf4e7b1ac144bad60c6859f113c0da8b131917912ab6b707780f5c4a3&status=2'
        // ]);
        $bodys = Syriatelcash::where('status', true)->orderBy('codeOrder')->pluck('userHistory');

        // $client = new Client(['proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@43.153.237.55:2334']);
        $client = new Client();
        $pass = false;
        $iter = 0;
        do{
            try{
                $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                        'Host' => 'cash-api.syriatel.sy',
                        'Connection' => 'Keep-Alive',
                        'Accept-Encoding' => 'gzip'
                    ],
                    'body' => $bodys->get($iter),
                    'timeout' => 120,
                ]);

                $body = json_decode($response->getBody()->getContents());

                if($body->code==1){
                        $data =  $body->data->data;
                        $desiredAmount = $form['amount'];
                        $desiredTransactionNo = $form['processid'];

                        // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
                        $found = false;
                        $matchedAmount = null;
                        foreach ($data as $item) {
                            if ($item->transactionNo == $desiredTransactionNo && $item->amount == $desiredAmount && Carbon::parse($item->date)->toDateString() == $tody  && $item->status==1) {
                                $found = true;
                                $matchedAmount = $item->amount;
                                break;
                            }
                        }
                        if($found){
                            $form['status']='complete';
                            $charge = Charge::create($form);
                                if($charge){
                                    if($charge->amount >= 5000){
                                        $chat = Chat::find($form['chat_id']);
                                        $setting = ModelsSetting::first();
                                        $amountWithBonus = $matchedAmount;
                                        if ($setting->bonusStatus) {
                                            $amountWithBonus += ($matchedAmount * ($setting->bonus / 100));
                                            SendTelegramMessage::dispatch($chat->id, "✅ نجاح".PHP_EOL."".PHP_EOL."💰 تمت إضافة بونص: ".$setting->bonus."%");
                                        }
                                        $chat->balance += $amountWithBonus;
                                        $chat->save();
                                        if(isset($chat->affiliate_code) && $setting->affilliateStatus){
                                            $f_affiliate_amount = $desiredAmount * ($setting->affilliate/100);
                                            SendTelegramMessage::dispatch($chat->affiliate_code, "💰 تمت إضافة مبلغ: ".$f_affiliate_amount."NSP"." من عملية شحن، وفق نظام الإحالة");
                                            Affiliate::create([
                                                'client'=>$chat->id,
                                                'amount'=>$desiredAmount,
                                                'affiliate_amount'=>$f_affiliate_amount,
                                                'chat_id'=>$chat->affiliate_code,
                                                'month_at' => date('Y-m')
                                            ]);
                                            // $affChat = Chat::find($chat->affiliate_code);
                                            // $affChat->balance += $f_affiliate_amount;
                                            // $affChat->save();
                                        }
                                        $subscribers = [842668006,7631183476];
                                        foreach ($subscribers as $chatId) {
                                            SendTelegramMessage::dispatch($chatId, "🗳 عملية شحن جديدة:".PHP_EOL."".PHP_EOL."المستخدم: ".$chat->id."".PHP_EOL."".PHP_EOL."المبلغ: ".$matchedAmount." NSP".PHP_EOL."".PHP_EOL."رقم العملية: ".$desiredTransactionNo."".PHP_EOL."".PHP_EOL."الوقت: ".(Carbon::now())->toDateTimeString()."", 'HTML');
                                        }
                                        return response()->json(["status"=>"success","message"=>"✅ شكراً لك، تم شحن رصيدك في البوت بنجاح."]);
                                    }else{
                                        return response()->json(["status"=>"success","message"=>"أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                                    }
                                }else{
                                        return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                                }
                            $pass=true;
                        }elseif($iter< $bodys->count()-1){
                                $iter = $iter+1;
                        }else{
                                return response()->json(["status"=>"failed","message"=>"⛔️ عملية الدفع غير صحيحة" ]);
                        }

                }
                else{
                        return response()->json(["status"=>"failed","message"=>"فشل التحقق من عملية الدفع، الرجاء التواصل مع الدعم" ]);

                }
            }catch(GuzzleException $e){
                Log::error("guzzel chargecash".$e->getMessage());
            }
        }while(!$pass);
    }











    public function testcash(Request $request)
    {
        $username = 'uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256';
        $password = 'uc28a3ecf573f05d0';
        $PROXY_PORT = 2334;
        $PROXY_DNS = '118.193.58.115';

        $url = 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory';
        $bodys = Syriatelcash::where('status', true)->orderBy('codeOrder')->pluck('userHistory');
        $body = $bodys->get(0);  // Assuming $bodys->get($iter) returns the form-urlencoded string

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'User-Agent: Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
            'Host: cash-api.syriatel.sy',
            'Connection: Keep-Alive',
            'Accept-Encoding: gzip'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        curl_setopt($ch, CURLOPT_PROXY, $PROXY_DNS);
        curl_setopt($ch, CURLOPT_PROXYPORT, $PROXY_PORT);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username . ':' . $password);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            echo $response; // أو عالج الاستجابة كما تريد
        }

        curl_close($ch);
    }

    public function chargecash(Request $request)
    {

        $form = Validator::make($request->all(), [
            "amount" => ["required", "numeric", "min:0.1", "max:99999999.9"],
            "processid" => "required|numeric",
            "chat_id" => "required",
            "method" => "required"
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status" => "success", "message" => "💡 الرجاء التحقق من معلومات الدفع وإدخالها وفق الترتيب الصحيح"]);
        }
        $form = $form->validate();
        $tody =  Carbon::now()->format('Y-m-d');
        $checkCharge = Charge::where("processid", $form['processid'])->first();
        if ($checkCharge) {
            if ($checkCharge['status'] == 'complete') {
                return response()->json(["status" => "failed", "message" => "عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            } else {
                return response()->json(["status" => "failed", "message" => "عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////

        // $bodys =collect([
        //     'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956579&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=5bcd650721e2ab948f595c55b490074ed002d9e9305602e5a04f5f9f04433851&status=2',
        //     'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=5956601&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=e7f0d6d36bfef4cff116f1076b813b10100bf21166977e33357dcdbaa0dd175e&status=2',
        //     'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv13&deviceId=00000000-0145-86e6-ffff-ffffef05ac4a&userId=4377068&sortType=1&mobileManufaturer=Xiaomi&mobileModel=23106RN0DA&channelName=4&lang=0&hash=c2b54fdcf4e7b1ac144bad60c6859f113c0da8b131917912ab6b707780f5c4a3&status=2'
        // ]);
        $bodys = Syriatelcash::where('status', true)->orderBy('codeOrder')->pluck('userHistory');

        // $client = new Client(['proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@43.153.237.55:2334']);
        $client = new Client();
        $pass = false;

            $breakme = true;
            do {
                try {
                    $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
                        'proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@118.193.58.115:2333',
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                            'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                            'Host' => 'cash-api.syriatel.sy',
                            'Connection' => 'Keep-Alive',
                            'Accept-Encoding' => 'gzip'
                        ],
                        'body' => $bodys->get(0),
                        'timeout' => 120
                    ]);
                     Log::error("chargecash ---- ");
                    $body = json_decode($response->getBody()->getContents());
                    if ($body->code == 1) {
                        $data =  $body->data->data;
                        $desiredAmount = $form['amount'];
                        $desiredTransactionNo = $form['processid'];

                        // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
                        $found = false;
                        $matchedAmount = null;
                        foreach ($data as $item) {
                            if ($item->transactionNo == $desiredTransactionNo && $item->amount == $desiredAmount && Carbon::parse($item->date)->toDateString() == $tody  && $item->status == 1) {
                                $found = true;
                                $matchedAmount = $item->amount;
                                break;
                            }
                        }
                        if ($found) {
                            $form['status'] = 'complete';
                            $charge = Charge::create($form);
                            if ($charge) {
                                if ($charge->amount >= 5000) {
                                    $chat = Chat::find($form['chat_id']);
                                    $setting = ModelsSetting::first();
                                    $amountWithBonus = $matchedAmount;
                                    if ($setting->bonusStatus) {
                                        $amountWithBonus += ($matchedAmount * ($setting->bonus / 100));
                                        SendTelegramMessage::dispatch($chat->id, "💰 تمت إضافة بونص: " . $setting->bonus . "%");
                                    }
                                    $chat->balance += $amountWithBonus;
                                    $chat->save();
                                    if (isset($chat->affiliate_code) && $setting->affilliateStatus) {
                                        $f_affiliate_amount = $desiredAmount * ($setting->affilliate / 100);
                                        SendTelegramMessage::dispatch($chat->affiliate_code, "💰 تمت إضافة مبلغ: " . $setting->f_affiliate_amount . "NSP" . " من عملية شحن، وفق نظام الإحالة");
                                        Affiliate::create([
                                            'client' => $chat->id,
                                            'amount' => $desiredAmount,
                                            'affiliate_amount' => $f_affiliate_amount,
                                            'chat_id' => $chat->affiliate_code,
                                            'month_at' => date('Y-m')
                                        ]);
                                        $affChat = Chat::find($chat->affiliate_code);
                                        $affChat->balance += $f_affiliate_amount;
                                        $affChat->save();
                                    }
                                    $subscribers = [842668006, 7631183476];
                                    foreach ($subscribers as $chatId) {
                                        SendTelegramMessage::dispatch($chatId, "🗳 عملية شحن جديدة:" . PHP_EOL . "" . PHP_EOL . "المستخدم: " . $chat->id . "" . PHP_EOL . "" . PHP_EOL . "المبلغ: " . $matchedAmount . " NSP" . PHP_EOL . "" . PHP_EOL . "رقم العملية: " . $desiredTransactionNo . "" . PHP_EOL . "" . PHP_EOL . "الوقت: " . (Carbon::now())->toDateTimeString() . "", 'HTML');
                                    }
                                    $userMsgtxt = '✅ نجاح:' . PHP_EOL . '' . PHP_EOL . '✅ تم تنفيذ عملية الشحن بنجاح عبر سيريتل كاش' . PHP_EOL . '' . PHP_EOL . '💵 رصيد حسابك في البوت: ' . $chat->balance . ' NSP';
                                    return response()->json(["status" => "success", "message" => $userMsgtxt]);
                                } else {
                                    return response()->json(["status" => "success", "message" => "أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                                }
                            } else {
                                return response()->json(["status" => "failed", "message" => "حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                            }
                            $pass = true;
                        } else {
                            return response()->json(["status" => "failed", "message" => "⛔️ عملية الدفع غير صحيحة"]);
                        }
                    } else {
                        return response()->json(["status" => "failed", "message" => "فشل التحقق من عملية الدفع، الرجاء التواصل مع الدعم"]);
                    }
                } catch (GuzzleException $e) {
                    Log::error("chargecash ---- GuzzleException" . $e->getMessage());
                } catch (Exception $e) {
                    Log::error("chargecash ---- Exception" . $e->getMessage());
                    return response()->json([
                        "status" => "failed",
                        "message" => "حدث خطأ غير متوقع، الرجاء المحاولة لاحقاً"
                    ]);
                }
            } while ($breakme);

    }
}
