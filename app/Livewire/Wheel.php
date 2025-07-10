<?php

namespace App\Livewire;

use App\Models\Wheel as ModelsWheel;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Wheel extends Component
{
    public $search = '';
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
         $results = ModelsWheel::query()
                ->when($this->search, function ($query) {
                    return $query->where('chat_id', 'like', '%' . $this->search . '%');
                })
            ->orderByRaw("created_at DESC")
            ->paginate(10);
        return view('livewire.wheel',compact('results'))->layout('admin.layouts.livewire');
    }

    public function deletewheel($wheelid){
         ModelsWheel::destroy($wheelid);
        session()->flash('success', trans('Success delete withdraw').': '.$wheelid);
        return $this->redirect(route('wheels'), navigate: true);
    }
}
