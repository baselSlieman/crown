<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Facades\Cookie;

class Language extends Component
{
    public $selectedLanguage = 'en';

    public function arabic()
    {
        Cookie::queue('locale', 'ar', 60 * 24 * 30);
        session(['locale' => 'ar']);
        $this->dispatch('languageChanged');
    }

    public function english()
    {
        Cookie::queue('locale', 'en', 60 * 24 * 30);
        session(['locale' => 'en']);
        $this->dispatch('languageChanged');
    }

    public function render()
    {
        if(session('locale')==null){
            session(['locale' =>Cookie::get('locale', 'en')]);
        }
        Log::error(Cookie::get('locale'));
        return view('livewire.language');
    }
}
