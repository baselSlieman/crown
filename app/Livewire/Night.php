<?php

namespace App\Livewire;

use Livewire\Component;

use function PHPSTORM_META\type;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class Night extends Component
{
    public $mode=false;
    public $icon='sun';
    public $type='light';
    public function render()
    {
        Log::error("cook".Cookie::get('mode'));
        if(session('mode')==null){
            session(['mode' => Cookie::get('mode', 'light')]);
        }
        Log::error("sess ".session('mode'));
        return view('livewire.night');
    }
    public function UpdatedMode(){
        $this->mode != $this->mode;
        if(session('mode')==='light'){
            Cookie::queue('mode', 'dark', 60 * 24 * 30);
            session(['mode' => 'dark']);
        }else{
            Cookie::queue('mode', 'light', 60 * 24 * 30);
            session(['mode' => 'light']);
        }
        $this->dispatch('change-theme');
    }
}
