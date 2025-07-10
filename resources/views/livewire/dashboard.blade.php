

    <!-- Sale & Revenue Start -->
    <div>
    <div class="container-fluid pt-4 px-4">

        <div class="row g-4">

            <div class="col-12 col-md-9 order-2 order-md-1">
                <div class="bg-light rounded p-3">
                    <div class="row">
                        <h5 class="col-10 col-md-6"><i class="fab fa-telegram text-primary"></i> @lang('Telegram Chats')</h5>
                        <h5 class="col-2 col-md-6 text-end"><a href="{{route('chats.index')}}" wire:navigate><i class="fas @if(App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></a></h5>
                        {{-- <h5 class="col-md-6 text-end"><i class="fas fa-user-lock text-primary"></i> {{''. number_format($chats_balance, 1)}} <small>NSP</small></h5> --}}
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-4"><i class="fas fa-users"></i> @lang('Today'): {{ $chats_today_count }}</div>
                        <div class="col-4 text-center"><i class="fas fa-users"></i> @lang('Month'): {{ $chats_monthly_count }}</div>
                        <div class="col-4 text-end"><i class="fas fa-users"></i> @lang('Total'): {{ $chats_count }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 order-1 order-md-2">
                <div class="bg-light rounded p-3" style="height:100%">
                        <h5><i class="fas fa-wallet text-primary"></i> @lang('Site Balance'):</h5>
                        <h4 class="mt-3 mb-0 text-center text-success">{{''. number_format($siteBalance, 1)}} <small>SYP</small></h4>
                </div>
            </div>





            <div class="col-12 col-md-5 order-3 order-md-3">
                <div class="bg-light rounded p-2">
                    <div class="d-flex justify-content-between align-items-center">
                    <h5><svg width="21px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M23 4C23 2.34315 21.6569 1 20 1H4C2.34315 1 1 2.34315 1 4V14C1 15.6569 2.34315 17 4 17H5C5.55228 17 6 16.5523 6 16C6 15.4477 5.55228 15 5 15H4C3.44772 15 3 14.5523 3 14L3 8H21V14C21 14.5523 20.5523 15 20 15H19C18.4477 15 18 15.4477 18 16C18 16.5523 18.4477 17 19 17H20C21.6569 17 23 15.6569 23 14V4ZM21 6V4C21 3.44772 20.5523 3 20 3H4C3.44772 3 3 3.44771 3 4V6H21Z" fill="#096cff"></path> <path d="M13 22C13 22.5523 12.5523 23 12 23C11.4477 23 11 22.5523 11 22L11 16.4069L9.70714 17.6996C9.31657 18.0903 8.68346 18.0903 8.29289 17.6996C7.90239 17.3093 7.90239 16.676 8.29289 16.2856L11.2924 13.2923C11.683 12.9024 12.3156 12.9028 12.7059 13.293L15.705 16.2922C16.0956 16.6828 16.0956 17.3159 15.705 17.7065C15.3145 18.0969 14.6813 18.0969 14.2908 17.7065L13 16.4157L13 22Z" fill="#096cff"></path> </g></svg> @lang('Charges'):</h5>
                    <div><a href="{{route('charges.index')}}" wire:navigate><i class="fas @if(App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></a></div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-3 col-md-4">@lang('Today'):   </div>
                        <div class="col-3 col-md-3"><i class="fas fa-code-branch"></i> {{ $charges_daily_count }}</div>
                        <div class="col-6 col-md-5"><i class="fas fa-coins text-success"></i> {{''. number_format($dailyTotalAmountComplete,1)}} <small>NSP</small></div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-3 col-md-4">@lang('Month'):</div>
                        <div class="col-3 col-md-3"><i class="fas fa-code-branch"></i> {{$charges_monthly_count }}</div>
                        <div class="col-6 col-md-5"><i class="fas fa-coins text-success"></i> {{''. number_format($monthlyTotalAmountComplete,1)}} <small>NSP</small></div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-3 col-md-4">@lang('Total'): </div>
                        <div class="col-3 col-md-3"><i class="fas fa-code-branch"></i> {{ $charges_count }}</div>
                        <div class="col-6 col-md-5"><i class="fas fa-coins text-success"></i> {{''. number_format($totalAmountComplete,1)}} <small>NSP</small></div>
                    </div>

                </div>
            </div>

            <div class="col-12 col-md-2 order-4 order-md-4">
                <div class="bg-light rounded p-2">
                    <h5 style="padding-bottom:8px"><img src="https://img.icons8.com/fluency/21/bar-chart.png"> @lang('Diff'):</h5>
                    <div>
                        <p class="mb-2 mt-3"><span>@lang('Today'):</span> <span>{{''. number_format($dailyTotalAmountComplete - $withdraws_daily_amount, 0)}}</span></p>
                        <p class="mb-2 mt-3"><span>@lang('Month'):</span> <span>{{''. number_format($monthlyTotalAmountComplete - $withdraws_monthly_amount, 0)}}</span></p>
                        <p class="mb-0 mt-3"><span>@lang('Total'):</span> <span>{{''. number_format($totalAmountComplete - $withdraws_amount, 0)}}</span></p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5 order-5 order-md-5">
                <div class="bg-light rounded p-2">
                    <div class="d-flex justify-content-between align-items-center">
                    <h5><svg width="21px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="" transform="rotate(45)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 9C11.4477 9 11 9.44771 11 10V15.5856L9.70711 14.2928C9.3166 13.9024 8.68343 13.9024 8.29292 14.2928C7.90236 14.6834 7.90236 15.3165 8.29292 15.7071L11.292 18.7063C11.6823 19.0965 12.3149 19.0968 12.7055 18.707L15.705 15.7137C16.0955 15.3233 16.0955 14.69 15.705 14.2996C15.3145 13.909 14.6814 13.909 14.2908 14.2996L13 15.5903V10C13 9.44771 12.5523 9 12 9Z" fill="#096cff"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M21 1C22.6569 1 24 2.34315 24 4V8C24 9.65685 22.6569 11 21 11H19V20C19 21.6569 17.6569 23 16 23H8C6.34315 23 5 21.6569 5 20V11H3C1.34315 11 0 9.65685 0 8V4C0 2.34315 1.34315 1 3 1H21ZM22 8C22 8.55228 21.5523 9 21 9H19V7H20C20.5523 7 21 6.55229 21 6C21 5.44772 20.5523 5 20 5H4C3.44772 5 3 5.44772 3 6C3 6.55229 3.44772 7 4 7H5V9H3C2.44772 9 2 8.55228 2 8V4C2 3.44772 2.44772 3 3 3H21C21.5523 3 22 3.44772 22 4V8ZM7 7V20C7 20.5523 7.44772 21 8 21H16C16.5523 21 17 20.5523 17 20V7H7Z" fill="#096cff"></path> </g></svg> @lang('Withdraws'):</h5>
                    <div><a href="{{route('withdraws.index')}}" wire:navigate><i class="fas @if(App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></a></div>
                    </div>
                        @if($pending)
                            <sup><i class="fas fa-hourglass-start text-danger"></i></sup>
                    @endif </h5>
                    <div class="row g-2 mt-2">
                        <div class="col-3">@lang('Today'):   </div>
                        <div class="col-3"><i class="fas fa-code-branch"></i> {{ $withdraws_daily_count }}</div>
                        <div class="col-6"><i class="fas fa-coins text-success"></i> {{ ''. number_format($withdraws_daily_amount, 0) }}<span class="text-danger ms-2"><i class="fas fa-percentage"></i></span> {{''. number_format($withdraws_daily_dis_amount, 0)}}</div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-3">@lang('Month'):</div>
                        <div class="col-3"><i class="fas fa-code-branch"></i> {{$withdraws_monthly_count }}</div>
                        <div class="col-6"><i class="fas fa-coins text-success"></i> {{''. number_format($withdraws_monthly_amount, 0)}}<span class="text-danger ms-2"><i class="fas fa-percentage"></i></span> {{''. number_format($withdraws_monthly_dis__amount, 0)}}</div>
                    </div>
                    <div>
                        <div class="row g-2 mt-2">
                            <div class="col-3">@lang('Total'): </div>
                            <div class="col-3"><i class="fas fa-code-branch"></i> {{ $withdraws_count }}</div>
                            <div class="col-6"><i class="fas fa-coins text-success"></i> {{ ''. number_format($withdraws_amount, 0) }}<span class="text-danger ms-2"><i class="fas fa-percentage"></i></span> {{''. number_format($withdraws_dis_amount, 0)}}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <div class="col-12 col-md-9 order-6 order-md-6">
                    <div class="bg-light rounded p-3">
                        <div class="row">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-rocket text-primary"></i> @lang('iChancy')</h5>
                                <div><a href="{{route('ichancies.index')}}" wire:navigate><i class="fas @if(App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></a></div>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-4"><i class="fas fa-bolt text-warning"></i> @lang('Today'): {{ $ichancy_today_count }}</div>
                            <div class="col-4 text-center"><i class="fas fa-bolt text-warning"></i> @lang('Month'): {{ $ichancy_month_count }}</div>
                            <div class="col-4 text-end"><i class="fas fa-bolt text-warning"></i> @lang('Total'): {{ $ichancy_count }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 order-7 order-md-7">
                <div class="bg-light rounded p-3" style="height:100%">
                        <h5><i class="fas fa-user-lock text-primary"></i> @lang('Users Balance'):</h5>
                        <h4 class="mt-3 mb-0 text-center text-success">{{''. number_format($chats_balance, 1)}} <small>SYP</small></h4>
                </div>
            </div>
            </div>
    </div>


    <!-- Sales Chart Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">@lang('chats')</h6>
                    </div>
                    <canvas id="worldwide-sales"></canvas>
                </div>
            </div>
            <div class="col-sm-12 col-xl-6">
                <div class="bg-light text-center rounded p-4">
                    <div class="mb-4">
                        <h6 class="mb-0">@lang('Charges & Withdraws')</h6>
                    </div>
                    <canvas id="salse-revenue"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Sales Chart End -->


    <!-- Recent Sales Start -->
    <div class="container-fluid pt-4 px-4 mb-5">
        <div class="bg-light text-center rounded p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="mb-0">@lang('Recent transactions')</h6>
            </div>
            <div class="table-responsive">
                <table class="table text-start align-middle table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-dark">
                            <th scope="col" class="text-center">id</th>
                            <th scope="col">@lang('Date')</th>
                            <th scope="col">@lang('Action')</th>
                            <th scope="col">@lang('Customer')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col" class="text-center">@lang('Status')</th>
                        </tr>
                    </thead>
                    <tbody>

                            @foreach ($transactions as $transaction)
                            <tr>
                            <td class="text-center">{{$transaction->id}}</td>
                            <td>{{$transaction->created_at}}</td>
                            <td>@isset($transaction->processid)
                                <i class="fas fa-download text-success"></i>
                            @endisset
                            @isset($transaction->code)
                            <i class="fas fa-upload text-danger"></i>
                            @endisset <span class="d-block d-md-inline">{{$transaction->method}}</span></td>
                            <td>{{$transaction->chat_id}}</td>
                            <td>{{''. number_format($transaction->amount, 0)}}<small class="text-primary"> NSP</small></td>
                            <td class="text-center">
                                @switch($transaction->status)
                                @case("complete")<i class="far fa-check-circle text-success"></i>@break
                                @case("requested")<i class="far fa-hourglass text-warning"></i>@break
                                @case("reject")<i class="far fa-times-circle text-danger"></i>@break
                            @endswitch
                            </td>
                        </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Recent Sales End -->


    {{-- <!-- Widgets Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-md-6 col-xl-4">
                <div class="h-100 bg-light rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0">Messages</h6>
                        <a href="">Show All</a>
                    </div>
                    <div class="d-flex align-items-center border-bottom py-3">
                        <img class="rounded-circle flex-shrink-0" src="{{ asset('dashboard/img/user.jpg') }}" alt=""
                            style="width: 40px; height: 40px;">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-0">Jhon Doe</h6>
                                <small>15 minutes ago</small>
                            </div>
                            <span>Short message goes here...</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom py-3">
                        <img class="rounded-circle flex-shrink-0" src="{{ asset('dashboard/img/user.jpg') }}" alt=""
                            style="width: 40px; height: 40px;">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-0">Jhon Doe</h6>
                                <small>15 minutes ago</small>
                            </div>
                            <span>Short message goes here...</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom py-3">
                        <img class="rounded-circle flex-shrink-0" src="{{ asset('dashboard/img/user.jpg') }}"
                            alt="" style="width: 40px; height: 40px;">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-0">Jhon Doe</h6>
                                <small>15 minutes ago</small>
                            </div>
                            <span>Short message goes here...</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-3">
                        <img class="rounded-circle flex-shrink-0" src="{{ asset('dashboard/img/user.jpg') }}"
                            alt="" style="width: 40px; height: 40px;">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-0">Jhon Doe</h6>
                                <small>15 minutes ago</small>
                            </div>
                            <span>Short message goes here...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-xl-4">
                <div class="h-100 bg-light rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Calender</h6>
                        <a href="">Show All</a>
                    </div>
                    <div id="calender"></div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-xl-4">
                <div class="h-100 bg-light rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">To Do List</h6>
                        <a href="">Show All</a>
                    </div>
                    <div class="d-flex mb-2">
                        <input class="form-control bg-transparent" type="text" placeholder="Enter task">
                        <button type="button" class="btn btn-primary ms-2">Add</button>
                    </div>
                    <div class="d-flex align-items-center border-bottom py-2">
                        <input class="form-check-input m-0" type="checkbox">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <span>Short task goes here...</span>
                                <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom py-2">
                        <input class="form-check-input m-0" type="checkbox">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <span>Short task goes here...</span>
                                <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom py-2">
                        <input class="form-check-input m-0" type="checkbox" checked>
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <span><del>Short task goes here...</del></span>
                                <button class="btn btn-sm text-primary"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom py-2">
                        <input class="form-check-input m-0" type="checkbox">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <span>Short task goes here...</span>
                                <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-2">
                        <input class="form-check-input m-0" type="checkbox">
                        <div class="w-100 ms-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <span>Short task goes here...</span>
                                <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Widgets End --> --}}
    <script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>
    <script src="{{asset('dashboard/lib/chart/chart.min.js')}}"></script>
    <script>

        var ctx2 = $("#salse-revenue").get(0).getContext("2d");
        var myChart2 = new Chart(ctx2, {
            type: "line",
            data: {
                labels: @php echo json_encode($dates); @endphp,
                datasets: [{
                        label: "Charges",
                        data: @php echo json_encode($totalAmounts); @endphp,
                        backgroundColor: "rgba(0, 156, 255, .5)",
                        fill: true
                    },
                    {
                        label: "Withdraws",
                        data: @php echo json_encode($totalWitdhraws); @endphp,
                        backgroundColor: "rgba(0, 156, 255, .3)",
                        fill: true
                    }
                ]
                },
            options: {
                responsive: true
            }
        });


        var ctx1 = $("#worldwide-sales").get(0).getContext("2d");
var myChart1 = new Chart(ctx1, {
    type: "bar",
    data: {
        labels: @php echo json_encode($labels); @endphp,
        datasets: [{
            label: "Chats",
            data: @php echo json_encode($data); @endphp,
            backgroundColor: "rgba(0, 156, 255, .7)"
        }]
    },
    options: {
        responsive: true
    }
});

    </script>




</div>
