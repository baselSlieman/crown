<?php

namespace App\Livewire\chats;

use App\Models\Chat as ModelsChat;
use App\Models\Ichancy;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Chat extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = "bootstrap";
    public $search = '';
    public $loading = false;
    public $value = null;
    public $currentKey = 1;
    public $currentKeyb = null;
    public $balanceb  = null;


    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function render()
    {
        if (session('locale') !== null) {
            App::setLocale(session('locale'));
        }
        $chats = ModelsChat::query()
            ->when($this->search, function ($query) {
                return $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', 'like', '%' . $this->search . '%')->orWhere('info', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.chats.chat', compact('chats'));
    }


     public function sortBy()
    {
        if ($this->sortField === 'created_at') {
            $this->sortField = 'balance';
        } else {
            $this->sortField = 'created_at';
        }
        $this->sortDirection = 'desc'; // دائمًا تنازلي
    }




    public function getBalance($chat_id)
    {
        $this->loading = true;
        $this->currentKeyb = $chat_id;
        $this->balanceb = ModelsChat::where('id', $chat_id)->first()->balance;
        $this->loading = false;
    }

    public function getData($chat_id)
    {

        $player = Ichancy::where('chat_id', $chat_id)->first();
        if ($player && $player->identifier) {
            $playerId = $player->identifier;
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
            $this->currentKey = $chat_id;
            $this->value = $ichancyBalance . " NSP";
            $this->loading = false;
        }
    }
}
