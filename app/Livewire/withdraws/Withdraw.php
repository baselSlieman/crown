<?php
namespace App\Livewire\withdraws;

use App\Models\Chat;
use App\Models\Withdraw as ModelsWithdraw;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Telegram\Bot\Laravel\Facades\Telegram;

class Withdraw extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    public $rejectedMessage;
    public $chat_id=null;
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $withdraws = ModelsWithdraw::query()
        ->when($this->search, function ($query) {
            return $query->whereHas('chat', function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', $this->search);
            });
        });
        if(isset($this->chat_id)){
            $withdraws->where("chat_id",$this->chat_id);
        }
        $withdraws = $withdraws->orderByRaw("status = 'requested' DESC, created_at DESC")
        ->with('chat')
        ->paginate(10);
        return view('livewire.withdraws.withdraw',compact('withdraws'));
    }

    public function delete($withdrawId)
    {
        ModelsWithdraw::destroy($withdrawId);
        session()->flash('success', trans('Success delete withdraw').': '.$withdrawId);
        return $this->redirect(route('withdraws.index'), navigate: true);
    }

    public function complete($withdrawId)
    {
        $withdraw = ModelsWithdraw::find($withdrawId);
        $chat = Chat::find($withdraw->chat_id);
        if($chat->balance<$withdraw->amount){
            // return redirect()->route('withdraws.index')->with('danger',"The user's balance is insufficient to withdraw the requested amount.");
            session()->flash('danger',trans("The user's balance is insufficient to withdraw the requested amount."));
            return $this->redirect(route('withdraws.index'), navigate: true);
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
                session()->flash('success', trans('The amount has been successfully withdrawn and the user').': '.$withdraw->chat_id.' '.trans('has been notified'));
                return $this->redirect(route('withdraws.index'), navigate: true);
            }else{
                session()->flash('danger',trans("Updating the customer's balance has failed."));
                return $this->redirect(route('withdraws.index'), navigate: true);
            }
        }else{
                session()->flash('danger',trans("Updating the customer's balance has failed."));
                return $this->redirect(route('withdraws.index'), navigate: true);
        }

    }
    public function reject($withdrawId)
    {
        $withdraw = ModelsWithdraw::find($withdrawId);
        $withdraw->status="rejected";
        $saved = $withdraw->save();
        if($saved){
            $response = Telegram::sendMessage([
                'chat_id' => $withdraw->chat_id,
                'text' => '❌ تم رفض عملية السحب'.PHP_EOL.''.PHP_EOL.'التوضيح:'.''.PHP_EOL.''.$this->rejectedMessage,
            ]);

            session()->flash('success',trans('The order has been successfully rejected').': '.$withdrawId);
            return $this->redirect(route('withdraws.index'), navigate: true);
        }else{
            session()->flash('danger',trans("Updating withdraw status has failed").": ".$withdrawId);
            return $this->redirect(route('withdraws.index'), navigate: true);
        }
    }

}
