<?php

namespace App\Livewire;

use App\Models\Setting as ModelsSetting;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Setting extends Component
{
    #[Validate('required|numeric')]
    public $bonus;
    #[Validate('required|numeric')]
    public $affilliate;
    public $withdraw;
    public $bonusStatus;
    public $affilliateStatus;
    public $bootStatus;
    public $loading;


    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $settings = ModelsSetting::first();
        $this->bonus=$settings->bonus;
        $this->affilliate=$settings->affilliate;

        $this->bonusStatus=$settings->bonusStatus;
        $this->affilliateStatus=$settings->affilliateStatus;
        $this->bootStatus=$settings->bootStatus;
        $this->withdraw=$settings->extra_col;
        return view('livewire.setting',compact("settings"))->layout('admin.layouts.livewire');
    }


    public function changeBonusStatus()
    {
        $this->loading = true;
        $setting = ModelsSetting::first();
        if (!$setting) {
            return false; // أو ترمي استثناء حسب حاجتك
        }

        $setting->bonusStatus = !$setting->bonusStatus;
        $setting->save();
        $this->loading = false;
        return;
    }

public function changeBootStatus()
    {
        $this->loading = true;
        $setting = ModelsSetting::first();
        if (!$setting) {
            return false; // أو ترمي استثناء حسب حاجتك
        }

        $setting->bootStatus = !$setting->bootStatus;
        $setting->save();
        $this->loading = false;
        return;
    }

    public function changeaffStatus()
    {
        $this->loading = true;
        $setting = ModelsSetting::first();
        if (!$setting) {
            return false; // أو ترمي استثناء حسب حاجتك
        }

        $setting->affilliateStatus = !$setting->affilliateStatus;
        $setting->save();
        $this->loading = false;
        return;
    }


    public function updateBonus()
    {
        $this->validate();
        $setting = ModelsSetting::first();
        // تعديل القيمة المطلوبة
        $setting->bonus = $this->bonus; // أو القيمة التي تريدها
        // حفظ التعديلات
        $setting->save();
        session()->flash('success', trans('Success update bonus'));
            return;
    }

    public function updateAffilliate()
    {
        $this->validate();
        $setting = ModelsSetting::first();
        // تعديل القيمة المطلوبة
        $setting->affilliate = $this->affilliate; // أو القيمة التي تريدها
        // حفظ التعديلات
        $setting->save();
        session()->flash('success', trans('Success update affilliate percent'));
            return;
    }


    public function updatewithdraw()
    {
        $this->validate();
        $setting = ModelsSetting::first();
        // تعديل القيمة المطلوبة
        $setting->extra_col = $this->withdraw; // أو القيمة التي تريدها
        // حفظ التعديلات
        $setting->save();
        session()->flash('success', trans('Success update Withdraw percent'));
            return;
    }

}
