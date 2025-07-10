<?php

namespace App\Livewire;

use App\Models\Syriatelcash;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Syriatelcashedit extends Component
{
    public $code_id;
    public $codesy;  // خاصية تخزن الكائن

    public function mount($code_id){
       $this->code_id = $code_id;
        $record = Syriatelcash::find($code_id);
        $this->codesy = $record ? $record->toArray() : [];
    }

    public function render()
    {
        if(session('locale') !== null){
            App::setLocale(session('locale'));
        }
        return view('livewire.syriatelcashedit')->layout('admin.layouts.livewire');
    }


    public function update()
    {
        $record = Syriatelcash::find($this->code_id);
        if ($record) {
            $record->update($this->codesy);
            session()->flash('success', __('Success update code'));
        }
    }


    public function delete()
    {
        $record = Syriatelcash::find($this->code_id);
        $record->delete();
        session()->flash('successs', trans('Success delete code') . ': ' . $this->codesy["code"]);
        return $this->redirect(route('syriatelcash'), navigate: true);
    }

}
