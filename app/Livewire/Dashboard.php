<?php

namespace App\Livewire;

use App\Models\Affiliate;
use App\Models\Gift;
use App\Models\Withdraw;
use App\Models\Charge;
use App\Models\Chat;
use App\Models\Ichancy;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\Cookie;

class Dashboard extends Component
{
    public function render()
    {
        if(session('locale')!==null){
           App::setLocale(session('locale'));
        }else{
            App::setLocale(Cookie::get('locale', 'en'));
        }

        if(session('mode')==null){
           session(['mode' => Cookie::get('mode', 'light')]);
        }

        $pending = false;
        if(Withdraw::where('status','requested')->count()>0){
            $pending =true;
        }
        $chats = Chat::query();
        $chats_balance = $chats->sum('balance');
        $chats_count = $chats->count();
        $chats_today_count= $chats->whereDate('created_at', Carbon::today())->count();
        $chats_monthly_count = Chat::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();

        $charges_count = Charge::where('status', 'complete')->count();
        $charges_daily_count = Charge::where('status', 'complete')->whereDate('created_at', Carbon::today())->count();
        $charges_monthly_count = Charge::where('status', 'complete')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
        $dailyTotalAmountComplete = Charge::where('status', 'complete')->whereDate('created_at', Carbon::today())->sum('amount');
        $monthlyTotalAmountComplete = Charge::where('status', 'complete')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('amount');
        $totalAmountComplete = Charge::where('status', 'complete')->sum('amount');


        $withdraws_count = Withdraw::where('status', 'complete')->count();
        $withdraws_daily_count= Withdraw::where('status', 'complete')->whereDate('created_at', Carbon::today())->count();
        $withdraws_monthly_count = Withdraw::where('status', 'complete')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();

        $completewithdraw = Withdraw::where('status', 'complete');
        $withdraws_amount = $completewithdraw->sum('finalAmount');
        $withdraws_dis_amount = $completewithdraw->sum('discountAmount');

        $withdraw_daily = Withdraw::where('status', 'complete')->whereDate('created_at', Carbon::today());
        $withdraws_daily_amount= $withdraw_daily->sum('finalAmount');
        $withdraws_daily_dis_amount= $withdraw_daily->sum('discountAmount');
        $withdraw_total = Withdraw::where('status', 'complete')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
        $withdraws_monthly_amount = $withdraw_total->sum('finalAmount');
        $withdraws_monthly_dis__amount = $withdraw_total->sum('discountAmount');

        $ichancy_today_count= Ichancy::query()->whereDate('created_at', Carbon::today())->count();
        $ichancy_month_count= Ichancy::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();;
        $ichancy_count= Ichancy::query()->count();
        $siteBalance = ($totalAmountComplete - $withdraws_amount) - (Gift::where('status','complete')->sum('amount'))-(Affiliate::where('status','complete')->sum('amount'));
        $today = Carbon::now();
        $weekAgo = $today->copy()->subDays(15);
        $chargesByDay = Charge::selectRaw('DATE(created_at) as date, SUM(amount) as total_amount')
                ->whereBetween('created_at', [$weekAgo, $today])
                ->where('status', 'complete')
                ->groupBy('date')
                ->get();
        $withdrawsByDay = Withdraw::selectRaw('DATE(created_at) as date, SUM(amount) as total_amount')
                ->whereBetween('created_at', [$weekAgo, $today])
                ->where('status', 'complete')
                ->groupBy('date')
                ->get();
        $dates = $chargesByDay->pluck('date')->toArray(); // المصفوفة التي تحتوي على تواريخ الأيام
        $totalAmounts = $chargesByDay->pluck('total_amount')->toArray();
        $totalWitdhraws = $withdrawsByDay->pluck('total_amount')->toArray();



        $charges = Charge::orderBy('created_at', 'desc')
                ->take(5)
                ->get();

        $withdraws = Withdraw::orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        $transactions = $charges->merge($withdraws)->sortByDesc('created_at')->take(10);


        // $currentMonth = now()->format('Y-m');

    // $chatsPerMonth = Chat::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
    //     ->whereRaw("created_at >= DATE_SUB(LAST_DAY(DATE_SUB('$currentMonth-01', INTERVAL 6 MONTH)), INTERVAL 6 MONTH)")
    //     ->whereRaw("created_at <= LAST_DAY('$currentMonth-01')")
    //     ->groupBy('year', 'month')
    //     ->orderBy('year', 'asc') // Specify the ordering direction here
    //     ->orderBy('month', 'asc')
    //     ->get();
    $currentMonth = now(); // تاريخ ووقت اليوم
$previousFiveMonthsStart = $currentMonth->copy()->subMonths(11)->startOfMonth(); // بداية الشهر للشهور الخمس السابقة

$chatsPerMonth = Chat::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
    ->where('created_at', '>=', $previousFiveMonthsStart)
    ->where('created_at', '<=', $currentMonth->endOfMonth()) // نهاية الشهر الحالي
    ->groupBy('year', 'month')
    ->orderBy('year', 'asc')
    ->orderBy('month', 'asc')
    ->get();
        $labels = [];
        $data = [];

    foreach($chatsPerMonth as $chat) {
        $labels[] = $chat->year.'-'.$chat->month; // تاريخ كامل للشهر

        $data[] = $chat->count;
    }

        return view('livewire.dashboard',compact('labels','data','transactions','dates','totalAmounts','totalWitdhraws','siteBalance','pending','withdraws_monthly_dis__amount','withdraws_daily_dis_amount','withdraws_dis_amount','ichancy_count','ichancy_month_count','chats_balance','chats_monthly_count','withdraws_amount','withdraws_daily_amount','withdraws_monthly_amount','dailyTotalAmountComplete','monthlyTotalAmountComplete','totalAmountComplete','chats_count','chats_today_count','charges_count','charges_daily_count','charges_monthly_count','withdraws_count','withdraws_daily_count','withdraws_monthly_count','withdraws_daily_count','ichancy_today_count'));
    }
}
