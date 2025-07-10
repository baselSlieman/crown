<?php

namespace App\Livewire;

use App\Models\Syriatelcash as ModelsSyriatelcash;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use PgSql\Lob;

class Syriatelcash extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = "bootstrap";
    public $search = '';
    #[Validate('required|numeric')]
    public $phone;
    #[Validate('required|numeric')]
    public $code;
    #[Validate('required')]
    public $username;
    #[Validate('required|numeric')]
    public $userid;
    public $type = 1;
    public $codeOrder;
    public $userHistory;
    public $refreshBalance;
    public $MaarchentHistory;
    public $customerbalance;
    public $loading = false;
    public $currentKeyb = null;

    public function render()
    {
        if (session('locale') !== null) {
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

        return view('livewire.syriatelcash', compact('results'))->layout('admin.layouts.livewire');
    }

    public function refreshBalancef($code_id)
    {
        $this->currentKeyb = $code_id;
        $this->loading = true;
        $client = new Client([
            'verify' => false,
        ]);
        $code = ModelsSyriatelcash::find($code_id);
        $breakme = true;
        do{
            try {
                $response = Http::withOptions([
                    'verify' => false,  // لتعطيل التحقق من شهادة SSL (للاختبار فقط)
                    'proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@118.193.58.115:2333',
                    'timeout' => 120,
                ])->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 13; 23106RN0DA Build/TP1A.220624.014)',
                    'Host' => 'cash-api.syriatel.sy',
                    'Connection' => 'Keep-Alive',
                    'Accept-Encoding' => 'gzip'
                ])->send('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/features/ePayment/refresh_balance', [
                    'body' => $code->refreshBalance,
                ]);


                $body = json_decode($response->getBody()->getContents());

                if ($body->code == 1 && $body->message == "تمت العملية بنجاح") {
                    $data =  $body->data->data;
                    $this->customerbalance = $data[0]->CUSTOMER_BALANCE;
                }
                $this->loading = false;
                $breakme==false;
                return;
            } catch (GuzzleException $e) {
                // تحقق إذا كان هناك استجابة في الاستثناء
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $bodyContent = $response->getBody()->getContents();
                    // حاول تفكيك JSON أو التعامل مع النص حسب الحاجة
                    $body = json_decode($bodyContent);
                    if ($body === null) {
                        Log::error('chargecash ---- استجابة غير صالحة: ' . $bodyContent);
                        // return response()->json(["status" => "failedsy", "message" => "فشلت التحقق الآلي من عملية الدفع"]);
                    }else{
                        $bodyres = json_decode($response->getBody()->getContents());
                        if ($bodyres->code == 1 && $bodyres->message == "تمت العملية بنجاح") {
                            $data =  $bodyres->data->data;
                            $this->customerbalance = $data[0]->CUSTOMER_BALANCE;
                        }
                        $this->loading = false;
                        $breakme==false;
                    }
                    // تابع العملية بناءً على البيانات المستلمة
                } else {
                    // لا توجد استجابة في الاستثناء، سجل الخطأ وأعد الرد بالخطأ
                    Log::error("chargecash ---- " . $e->getMessage());
                    // return response()->json(["status" => "failedsy", "message" => "فشلت التحقق الآلي من عملية الدفع"]);
                }
            } catch (Exception $e) {
                Log::error("chargecash ---- " . $e->getMessage());
                // return response()->json([
                //     "status" => "failed",
                //     "message" => "حدث خطأ غير متوقع، الرجاء المحاولة لاحقاً"
                // ]);
            }
        }while($breakme);
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
            "phone" => $this->phone,
            "code" => $this->code,
            "type" => $this->type,
            "codeOrder" => $orderValue,
            "username" => $this->username,
            "userid" => $this->userid,
            "userHistory" => $this->userHistory,
            "refreshBalance" => $this->refreshBalance,
            "MaarchentHistory" => $this->MaarchentHistory
        ]);
        if ($created) {
            session()->flash('success', trans('Success create Syraitel code'));
            return;
        } else {
            session()->flash('danger', trans('falied create  Syraitel code'));
            return;
        }
    }
}
