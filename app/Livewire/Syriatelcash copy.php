<?php

namespace App\Livewire;

use App\Models\Syriatelcash as ModelsSyriatelcash;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Syriatelcash extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    #[Validate('required|numeric')]
    public $phone;
    #[Validate('required|numeric')]
    public $code;
    #[Validate('required')]
    public $username;
    #[Validate('required|numeric')]
    public $userid;
    public $type=1;
    public $codeOrder;
    public $userHistory;
    public $refreshBalance;
    public $MaarchentHistory;
    public $customerbalance;
    public $loading = false;
    public $currentKeyb = null;

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $results = ModelsSyriatelcash::query()
                ->when($this->search, function ($query) {
                    return $query->where('username', 'like', '%' . $this->search . '%')
                                ->orWhere('code', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                })
            ->orderByRaw("codeOrder")
            ->paginate(15);

        return view('livewire.syriatelcash',compact('results'))->layout('admin.layouts.livewire');
    }

     public function refreshBalancef($code_id)
    {
        $this->currentKeyb = $code_id;
        $this->loading = true;
        $client = new Client();
        $code = ModelsSyriatelcash::find($code_id);

        try{
                $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/features/ePayment/refresh_balance', [
                    // 'proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-st-aleppo-city-aleppo:uc28a3ecf573f05d0@118.193.58.115:2333',
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                        'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                        'Host' => 'cash-api.syriatel.sy',
                        'Connection' => 'Keep-Alive',
                        'Accept-Encoding' => 'gzip'
                    ],
                    'body' => $code->refreshBalance,
                    'timeout' => 120
                ]);

                $body = json_decode($response->getBody()->getContents());

                if($body->code==1 && $body->message=="تمت العملية بنجاح"){
                        $data =  $body->data->data;
                        $this->customerbalance =$data[0]->CUSTOMER_BALANCE;
                }
                $this->loading = false;
                return;
        }catch(GuzzleException $e){
            session()->flash('danger', trans('falied get balance'.PHP_EOL.$e->getMessage()));
            $this->loading = false;
            return;
        }
    }

     public function changeStatus($code_id)
    {
        $this->loading = true;
        $record = ModelsSyriatelcash::find($code_id);
        if (!$record) {
            return false; // أو ترمي استثناء حسب حاجتك
        }

        $record->status = !$record->status;
        $record->save();
        $this->loading = false;
        return;
    }
     public function addCode()
    {
        $this->validate();

        $orderValue = (ModelsSyriatelcash::max('codeOrder') ?? 0) + 1;

         $created = ModelsSyriatelcash::Create([
                "phone"=>$this->phone,
                "code"=>$this->code,
                "type"=>$this->type,
                "codeOrder"=> $orderValue,
                "username"=>$this->username,
                "userid"=>$this->userid,
                "userHistory"=>$this->userHistory,
                "refreshBalance"=>$this->refreshBalance,
                "MaarchentHistory"=>$this->MaarchentHistory
            ]);
            if($created){
            session()->flash('success', trans('Success create Syraitel code'));
            return;
        }else{
            session()->flash('danger', trans('falied create  Syraitel code'));
            return;
        }
    }
}
