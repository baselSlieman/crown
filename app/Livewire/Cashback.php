<?php

namespace App\Livewire;

use App\Jobs\SendTelegramMessage;
use App\Models\Ichancy;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Telegram\Bot\Exceptions\TelegramResponseException;

class Cashback extends Component
{
    public $loading = false;
    public $value = null;
    public $currentKey = 1;

    public function render()
    {

        if (session('locale') !== null) {
            App::setLocale(session('locale'));
        }

        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $chargesSums = DB::table('charges')
            ->select('chat_id', DB::raw('SUM(amount) as total_charges'))
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderBy("created_at", "DESC")
            ->where('status', 'complete')
            ->groupBy('chat_id');

        $withdrawsSums = DB::table('withdraws')
            ->select('chat_id', DB::raw('SUM(amount) as total_withdraws'))
            ->whereBetween('updated_at', [$startOfDay, $endOfDay])
            ->orderBy("updated_at", "DESC")
            ->where('status', 'complete')
            ->groupBy('chat_id');

        // $wheelSums = DB::table('wheels')
        // ->select('chat_id', DB::raw('SUM(amount) as total_wheels'))
        // ->whereBetween('updated_at', [$startOfDay, $endOfDay])
        // ->orderBy("updated_at", "DESC")
        // ->where('status', true)
        // ->groupBy('chat_id');

        $wheelSums = DB::table('wheels')
            ->select(
                'chat_id',
                DB::raw('SUM(amount) as total_wheels'),
                DB::raw('COUNT(amount) as count_amount'),
                DB::raw('min(canwheel) as canwheel')
            )
            ->whereBetween('updated_at', [$startOfDay, $endOfDay])
            ->where('status', true)
            ->groupBy('chat_id')
            ->orderBy('updated_at', 'DESC');

        $results = DB::table('chats')
            ->joinSub($chargesSums, 'charge_totals', function ($join) {
                $join->on('charge_totals.chat_id', '=', 'chats.id');
            })
            ->leftJoinSub($withdrawsSums, 'withdraw_totals', function ($join) {
                $join->on('withdraw_totals.chat_id', '=', 'chats.id');
            })
            ->leftJoinSub($wheelSums, 'wheel_totals', function ($join) {
                $join->on('wheel_totals.chat_id', '=', 'chats.id');
            })
            ->leftJoin('ichancies', 'ichancies.chat_id', '=', 'chats.id')
            ->select(
                'chats.id',
                'chats.username',
                'chats.balance',
                'charge_totals.total_charges',
                DB::raw('COALESCE(withdraw_totals.total_withdraws, 0) as total_withdraws'),
                DB::raw('(charge_totals.total_charges - (COALESCE(withdraw_totals.total_withdraws, 0) + chats.balance)) as difference'),
                DB::raw('COALESCE(wheel_totals.total_wheels, 0) as total_wheels'),
                DB::raw('COALESCE(wheel_totals.count_amount, 0) as count_amount'),
                'wheel_totals.canwheel',
                'ichancies.identifier'
            )
            ->get();

        return view('livewire.cashback', compact('results'))->layout('admin.layouts.livewire');
    }



    public function giveWheel($chatid)
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        // تعديل القيم
        $updatedRows = DB::table('wheels')
            ->where('chat_id', $chatid)  // قم باستبدال $chatId بالقيمة المطلوبة
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->update(['canwheel' => 1]);
        if ($updatedRows > 0) {
            SendTelegramMessage::dispatch($chatid, "🎡 لقد حصلت على ضربة مجانية" . PHP_EOL . "🔅 في عجلة الحظ.");
            session()->flash('success', trans('Success give user a wheel') . ' ' . $chatid);
        } else {
            session()->flash('danger', trans('Failed give user a wheel') . ' ' . $chatid);
        }
    }

    public function notify($chatid)
    {
        try {
            SendTelegramMessage::dispatch($chatid, "🎡 لديك ضربة مجانية" . PHP_EOL . "🔅 في عجلة الحظ.");
            session()->flash('success', trans('Success notify') . ' ' . $chatid);
        } catch (TelegramResponseException $e) {
            // تحقق من نص الخطأ
            if (str_contains($e->getMessage(), 'chat not found')) {
                session()->flash('danger', trans('Failed to send: chat not found for user ') . $this->chat->id);
            } else {
                session()->flash('danger', trans('Telegram API error: ') . $e->getMessage());
            }
            return $this->redirect(route('chats.index'), navigate: true);
        } catch (Exception $e) {
            session()->flash('danger', trans('Unexpected error: ') . $e->getMessage());
            return $this->redirect(route('chats.index'), navigate: true);
        }
    }

    public function getData($identifier)
    {

        $player = Ichancy::where('identifier', $identifier)->first();
        if ($player && $player->identifier) {
            $playerId = $identifier;
            $this->loading = true;
            $ichancyBalance = null;
            $client = new Client();
            $jsonFile = file_get_contents(public_path('data.json'));
            $data = json_decode($jsonFile, true);
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
                    $data['cookies'] = $cookies;
                    $updatedJson = json_encode($data);
                    file_put_contents(public_path('data.json'), $updatedJson);
                }
            } while (!$pass);
            $this->currentKey = $playerId;
            $this->value = $ichancyBalance;
            $this->loading = false;
        }
    }
}
