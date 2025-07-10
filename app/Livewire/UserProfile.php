<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserProfile extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'password' => 'nullable|min:8|confirmed',
        ];
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    public function save()
    {
         $this->validate($this->rules());

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;

        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        session()->flash('message', 'تم تحديث بياناتك بنجاح.');
    }

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.user-profile')->layout('admin.layouts.livewire');
    }
}

