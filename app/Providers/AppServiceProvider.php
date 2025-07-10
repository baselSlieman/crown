<?php

namespace App\Providers;

use App\Livewire\charges\Charge;
use App\Livewire\charges\Edit as ChargesEdit;
use App\Livewire\chats\Affiliate;
use App\Livewire\chats\Chat;
use App\Livewire\chats\Edit;
use App\Livewire\chats\Gift;
use App\Livewire\chats\Message;
use App\Livewire\withdraws\Edit as WithdrawsEdit;
use App\Livewire\withdraws\Withdraw;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Route::model('user', 'App\Models\User');
        Livewire::component('Gift', Gift::class);
        Livewire::component('Affiliate', Affiliate::class);
        Livewire::component('Chat', Chat::class);
        Livewire::component('Edit', Edit::class);
        Livewire::component('Message', Message::class);

        Livewire::component('Charge', Charge::class);
        Livewire::component('ChargesEdit', ChargesEdit::class);

        Livewire::component('Withdraw', Withdraw::class);
        Livewire::component('WithdrawsEdit', WithdrawsEdit::class);

    }
}
