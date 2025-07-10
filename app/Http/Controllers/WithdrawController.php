<?php
namespace App\Http\Controllers;
use App\Models\Chat;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chat_id = $request->query('chat_id');
        return view("admin.withdraw.index",compact("chat_id"));
    }
    public function rejectOrder(Request $request,Withdraw $withdraw){
        $withdraw->status="rejected";
        $saved = $withdraw->save();
        if($saved){
            $response = Telegram::sendMessage([
                'chat_id' => $withdraw->chat_id,
                'text' => '❌ تم رفض عملية السحب'.PHP_EOL.''.PHP_EOL.'التوضيح:'.''.PHP_EOL.''.$request->message,
            ]);
            return redirect()->route('withdraws.index')->with('success',"The order has been successfully rejected.");
        }else{
            return redirect()->route('withdraws.index')->with('danger',"Updating withdraw status has failed.");
        }
    }
    public function completeOrder(Withdraw $withdraw){
        $chat = Chat::find($withdraw->chat_id);
        if($chat->balance<$withdraw->amount){
            return redirect()->route('withdraws.index')->with('danger',"The user's balance is insufficient to withdraw the requested amount.");
        }
        $withdraw->status="complete";
        $saved = $withdraw->save();
        if($saved){
            $chat->balance=$chat->balance-$withdraw->amount;
            $updated = $chat->save();
            if($updated){
                $response = Telegram::sendMessage([
                    'chat_id' => $withdraw->chat_id,
                    'text' => '✅ تم تنفيذ عملية السحب بنجاح'.PHP_EOL.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'معرف المستخدم: '.$withdraw->chat_id.''.PHP_EOL.'القيمة: '.$withdraw->amount.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'نسبة الحسم: 10%'.PHP_EOL.'القيمة المحسومة: '.$withdraw->discountAmount.''.PHP_EOL.''.PHP_EOL.'الرصيد الحالي: '.$chat->balance.' NSP',
                ]);
                return redirect()->route('withdraws.index')->with('success',"The amount has been successfully withdrawn and the user has been notified.");
            }else{
                return redirect()->route('withdraws.index')->with('danger',"Updating the customer's balance has failed.");
            }
        }else{
            return redirect()->route('withdraws.index')->with('danger',"Updating withdraw status has failed.");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $response = Telegram::getMe();
        dd($response);
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
    public function show(Withdraw $withdraw)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Withdraw $withdraw)
    {
        return view("admin.withdraw.edit",compact('withdraw'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Withdraw $withdraw)
    {

        if($request->amount==$withdraw->amount){
            $withdraw->fill($request->all())->save();
        }else{
            $amount =$request->amount;
            $discount = $amount * 0.1;
            // الحصول على المبلغ بعد الخصم
            $finalAmount = $amount - $discount;
            // القيمة المخصومة
            $discountAmount = $amount - $finalAmount;
            $withdraw->finalAmount =$finalAmount;
            $withdraw->discountAmount =$discountAmount;
            $withdraw->fill($request->all())->save();
        }
        return redirect()->route('withdraws.index')->with('success','Withdraw updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Withdraw $withdraw)
    {
        $withdraw->delete();
        return redirect()->route('withdraws.index')->with('success','Withdraw deleted successfully');
    }
}
