<?php

namespace App\Livewire;

use App\Models\Ichancy as ModelsIchancy;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Ichancy extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    public $loading = false;
    public $value = null;
    public $currentKey = 1;
    public $currentUsername;
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $ichancies = ModelsIchancy::query()
        ->when($this->search, function ($query) {
            return $query->whereHas('chat', function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')->orWhere('id', $this->search);
            })->orWhere('username',$this->search)->orWhere('identifier',$this->search);
        })
        ->orderByRaw("status = 'complete' DESC, created_at DESC")
        ->with('chat')
        ->paginate(10);
        return view('livewire.ichancy',compact('ichancies'));
    }

    public function getPlayerId($username)
    {
        $this->currentUsername = $username;
        $this->loading = true;
        $ichancy = ModelsIchancy::where("username",$username)->first();
        $client = new Client();
        $jsonFile = file_get_contents(public_path('data.json'));
        $data = json_decode($jsonFile, true);
        $pass = false;
        do{
                        $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayersForCurrentAgent', [
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
                        'cookie' => $data['cookies']
                    ],
                    'body' => '{"start":0,"limit":20,"filter":{},"isNextPage":false,"searchBy":{"getPlayersFromChildrenLists":"'.$username.'"}}'
                    ]);
                    $body2 = json_decode($response2->getBody()->getContents());


                    if (is_object($body2->result)) {
                        if(!empty($body2->result->records)){
                            $users = collect($body2->result->records);
                            $playerId =  data_get($users->firstWhere('username', $username), 'playerId', null);
                            $ichancy->identifier=$playerId;
                            $saved = $ichancy->save();
                            if($saved){
                                session()->flash('success', trans('Success get playerId'));
                                $this->loading = false;
                                return;
                            }else{session()->flash('danger', trans('Failed get bonus'));return;}
                        }else{
                            return response()->json(["status"=>"success","message"=>"error_playerId"]);
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
                        'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
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
                        // session(['cookies' => $cookies]);
                        $data['cookies'] = $cookies;
                        $updatedJson = json_encode($data);
                        file_put_contents(public_path('data.json'), $updatedJson);
                        }
                    }while(!$pass);
    }


    public function getData($identifier)
    {
        $this->loading = true;
        $ichancyBalance=null;
        $playerId = $identifier;

        $client = new Client();
        $jsonFile = file_get_contents(public_path('data.json'));
        $data = json_decode($jsonFile, true);
        $pass = false;
        do{
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayerBalanceById', [
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
            'cookie' => $data['cookies'],
        ],
        'body' => '{"playerId":"'.$playerId.'"}'
        ]);
        $body2 = json_decode($response2->getBody()->getContents());

        if (is_array($body2->result)) {
            $ichancyBalance = data_get(($body2->result)[0],"balance",null);
            $pass=true;
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
            'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
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
            $data['cookies'] = $cookies;
            $updatedJson = json_encode($data);
            file_put_contents(public_path('data.json'), $updatedJson);
            }
        }while(!$pass);
        $this->currentKey = $identifier;
        $this->value = $ichancyBalance." NSP";
        $this->loading = false;

    }

}
