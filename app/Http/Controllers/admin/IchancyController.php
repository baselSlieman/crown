<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ichancy;
use App\Models\IchTransaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Telegram\Bot\Laravel\Facades\Telegram;

class IchancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.ichancy.index");
    }

    public function ichancy_transaction(Request $request,$type = null)
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $id = $request->get('ichancyid');
        $ichancyTrans = IchTransaction::query();

        if(isset($id)){
            $ichancyTrans->where("ichancy_id",$id);
        }
        if ($type !== null) {
            $ichancyTrans->where("type", $type);
        }

        $ichancyTransRes = $ichancyTrans->orderByRaw("status = 'requested' DESC, created_at DESC")->with('ichancy')->paginate(5);
        return view("admin.ichancy.ichancy_transactions",compact('ichancyTransRes','type'));
    }

    public function delete_ichancy_charge(IchTransaction $transacion)
    {
        if ($transacion->delete()) {
        return redirect()->route('ichancies.ichancy_transaction', ['type' => $transacion->type])->with('success','success delete transaction'.': '.$transacion->id);
        } else {
                    return redirect()->route('ichancies.ichancy_transaction', ['type' => $transacion->type])->with('danger','failed delete transaction'.': '.$transacion->id);
        }
    }

    public function exec_ichancy_charge(IchTransaction $transacion)
    {



        $subscribers = [842668006,5144738358];

        if($transacion->status != 'requested'){
            return redirect()->route('ichancies.ichancy_transaction')->with('danger','Charge order already processed');
        }

        if($transacion->chat->balance < $transacion->amount){
            foreach ($subscribers as $chatId) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        "parse_mode"=>"HTML",
                        'text' => '❗️ لوحة التحكم:'.PHP_EOL.'لايوجد رصيد كافي في محفظة المستخدم لتنفيذ عملية شحن حساب أيشانسي'.PHP_EOL.'معرف اللاعب:<b><code>'.$transacion->ichancy->identifier.'</code></b>',
                    ]);
            }
            $response = Telegram::sendMessage([
                'chat_id' => $transacion->chat_id,
                "parse_mode"=>"HTML",
                'text' => '❗️'.PHP_EOL.'لايوجد رصيد كافي في محفظتك لتنفيذ عملية شحن حساب أيشانسي، قد يكون جرى عملية سحب من المحفظة خلال فترة معالجة الطلب',
            ]);
            return redirect()->route('ichancies.ichancy_transaction',$transacion->type)->with('danger','User balance not available');
        }
        $client = new Client();
        $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        $playerId = $transacion->ichancy->identifier;
        $pass = false;
        do{
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/depositToPlayer', [
        'headers' => [
            'Content-Type' => 'application/json',
            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
            'Accept-Encoding' => 'gzip,deflate,br',
            'Accept' => '*/*',
            'dnt'=> '1',
            'origin'=>'https://agents.ichancy.com',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: cors',
            'sec-fetch-dest: empty',
            'accept-encoding'=>'gzip, deflate, br',
            'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
            'cookie' => $cookies,
        ],
        'body' => '{"amount":'.$transacion->amount.',"comment":null,"playerId":"'.$playerId.'","currencyCode":"NSP","currency":"NSP","moneyStatus":5}'
        ]);
        $body2 = json_decode($response2->getBody()->getContents());


        if (is_object($body2->result)) {
            $transacion->status = "complete";
            $saved = $transacion->save();
            $pass =true;
            if($saved){
                $transacion->chat->balance=$transacion->chat->balance-$transacion->amount;
                $transacion->chat->save();
                    $response = Telegram::sendMessage([
                        'chat_id' => $transacion->chat_id,
                        'text' => '✅ تم شحن حسابك أيشانسي بنجاح:'.PHP_EOL.'شكراً على انتظارك',
                    ]);
                    foreach ($subscribers as $chatId) {
                            $response = Telegram::sendMessage([
                                'chat_id' => $chatId,
                                "parse_mode"=>"HTML",
                                'text' => '🔔 لوحة التحكم:'.PHP_EOL.''.PHP_EOL.'✅ تم شحن حساب المستخدم بنجاح'.PHP_EOL.'معرف اللاعب: <b><code>'.$playerId.'</code></b>'.PHP_EOL.'المبلغ: '.$transacion->amount.' NSP',
                            ]);
                    }
                return redirect()->route('ichancies.ichancy_transaction',$transacion->type)->with('success','Charge complete successfuly');
            }else{
                return redirect()->route('ichancies.ichancy_transaction',$transacion->type)->with('danger','failed to process order');
            }
        } elseif($body2->result == "ex") {
            $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                'Accept-Encoding' => 'gzip,deflate,br',
                'Accept' => '*/*',
                'dnt'=> '1',
                'origin'=>'https://agents.ichancy.com',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'accept-encoding'=>'gzip, deflate, br',
                'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
            ],
            'body' => '{"username": "Brhoom@agent.nsp","password": "Bas889@@"}'
            ]);
            $incom_cookies = $response->getHeader('Set-Cookie');
            $cookies='';
            foreach($incom_cookies as $cookie) {
                // تقسيم النص بناءً على الفاصلة منقوطة
                $parts = explode(';', $cookie);
                // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                $cookies .= trim($parts[0]) . ';';
            }
            // حذف آخر فاصلة منقوطة
            $cookies = rtrim($cookies, ';');
            }elseif($body2->result == false){
                foreach ($subscribers as $chatId) {
                        $response = Telegram::sendMessage([
                            'chat_id' => $chatId,
                            "parse_mode"=>"HTML",
                            'text' => '🔔 لوحة التحكم:'.PHP_EOL.''.PHP_EOL.'🔅 حدث خطأ أثناء تنفيذ العملية، تحقق من رصيد الكاشيرة ثم من حركة الحساب في لوحة التحكم',
                        ]);
                }
                return redirect()->route('ichancies.ichancy_transaction',$transacion->type)->with('danger','FAILED, Cashera not enought');
            }
        }while(!$pass);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ichancy $ichancy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ichancy $ichancy)
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view("admin.ichancy.edit",compact('ichancy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ichancy $ichancy)
    {
        $ichancy->fill($request->all())->save();
        return redirect()->route('ichancies.index')->with('success','ichancy updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ichancy $ichancy)
    {
        $ichancy->delete();
        return redirect()->route('ichancies.index')->with('success','Ichancy account in site deleted successfully');
    }
}
