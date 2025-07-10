<?php

namespace App\Livewire;

use App\Models\Transfer;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Transfare extends Component
{public $search = '';
    public function render()
    { if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
         $results = Transfer::query()
                ->when($this->search, function ($query) {
                    return $query->where('sender_number', 'like', '%' . $this->search . '%')
                                ->orWhere('receiver_number', 'like', '%' . $this->search . '%');
                })
            ->orderByRaw("created_at DESC")
            ->paginate(10);
        return view('livewire.transfare',compact('results'))->layout('admin.layouts.livewire');
    }
}
