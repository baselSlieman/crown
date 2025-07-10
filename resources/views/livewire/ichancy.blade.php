<div class="col col-md-12 px-3">

    <div class="row align-items-center">
        <h3 class="col-6 col-md-5 mb-0"><i class="fas fa-rocket text-primary"></i> @lang('Ichancy'):</h3>
        <div class="col-6 col-md-4 text-end text-md-start"><a href="{{ route('ichancies.ichancy_transaction',"withdraw") }}" class="btn btn-outline-danger me-2"><i class="fas fa-upload"></i></a><a href="{{ route('ichancies.ichancy_transaction','charge') }}" class="btn btn-outline-success"><i class="fas fa-download"></i></a></div>
        <div class="col-12 col-md-3 mt-2 mt-md-0"><input wire:model.live.debounce.500ms="search" type="search" class="form-control" placeholder="@lang('Search').."/></div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @elseif (session('danger'))
        <div class="alert alert-danger mt-3">
            {{ session('danger') }}
        </div>
    @endif
    <div class="table-responsive mt-1 mt-md-3">
    <table class="table mt-3 table-bordered" style="vertical-align: middle;">
        <thead>
            <tr>
                <th class="text-center">id</th>
                <th class=" text-secondary"><i class="fas fa-user"></i> @lang('User')</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-rocket"></i> @lang('Ichancy')</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-cog"></i> @lang('options')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ichancies as $ichancy)
                <tr>
                    <td class="text-center">{{ $ichancy->id }}</td>
                    <td>
                        <p class="mb-2"><i class="fas fa-chess-king"></i> @lang('Player Id'): <strong class="text-dark">{{$ichancy->identifier}} @if (!$ichancy->identifier)
                            <button  wire:click="getPlayerId('{{$ichancy->username}}')" class="btn btn-outline-light text-success btn-sm border-0"><i class="fas fa-undo-alt"></i></button>
                            <span wire:loading wire:target="getPlayerId('{{$ichancy->username}}')">
                            <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        @endif</strong></p>
                        <p class="mb-2">
                            <i class="fas fa-user-secret"></i> @lang('Player'): <strong>{{$ichancy->username}}</strong>
                            @isset($ichancy->identifier)
                            <button  wire:click="getData('{{$ichancy->identifier}}')" class="btn btn-outline-light text-success btn-sm border-0"><i class="fas fa-coins"></i></button>
                            <span wire:loading wire:target="getData('{{$ichancy->identifier}}')">
                            <i class="fa fa-spinner fa-spin"></i>
                            </span>
                            @if($currentKey === $ichancy->identifier)
                                <span class="text-danger d-block d-md-inline"><i class="fas fa-money-bill-alt"></i> @lang('ichancy balance'): {{$value}}</span>
                            @endif
                            @endisset
                        </p>
                        <p class="mb-2"><i class="fas fa-lock"></i> @lang('pass'): <strong>{{$ichancy->password}}</strong></p>
                        <p class="mb-0">
                            @switch($ichancy->status)
                            @case("complete")<i class="far fa-check-circle text-success"></i>@break
                            @case("pending")<i class="far fa-hourglass text-primary"></i>@break
                            @default

                        @endswitch {{__($ichancy->status)}}</p>

                    </td>



                    <td class="d-none d-md-table-cell">
                        <p class="mb-0 mb-md-3 mt-1"><i class="far fa-user"></i> @lang('username'): {{ $ichancy->chat->username}}</p>
                        <p class="mb-0 mb-md-3 mt-1"><i class="fas fa-user"></i> @lang('chat id'): {{ $ichancy->chat_id }}
                        <p><i class="far fa-calendar-alt"></i> {{ $ichancy->created_at }}</p>
                    </td>

                    <td class="d-none d-md-table-cell">
                        <div class="text-center mt-2">
                            <div>
                                <a href="{{ route('chats.createMessage', $ichancy->chat) }}"  wire:navigate.hover class="btn btn-primary"><i class="fab fa-telegram-plane"></i></a>
                                <a href="{{ route('ichancies.edit', $ichancy) }}" class="btn btn-success"><i class="fas fa-marker"></i></a>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('ichancies.ichancy_transaction',['type' => 'withdraw', 'ichancyid' => $ichancy->id]) }}" class="btn btn-danger"><i class="fas fa-upload"></i></a>
                                <a href="{{ route('ichancies.ichancy_transaction',['type' => 'charge', 'ichancyid' => $ichancy->id]) }}" class="btn btn-warning"><i class="fas fa-download"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="d-table-row d-md-none">
                    <td colspan="2">
                        <i class="far fa-user me-2"></i> <span class="fw-bold">@lang('username'):</span> {{ $ichancy->chat->username}}<br>
                        <i class="far fa-user me-2"></i> <span class="fw-bold">@lang('chat id'):</span> {{ $ichancy->chat_id}}<br>
                        <i class="far fa-calendar-alt me-2"></i> <span class="fw-bold">@lang('date'):</span> {{$ichancy->created_at}}<br>



                    </td>
                </tr>
                <tr class="d-table-row d-md-none" >
                    <td colspan="2" class="text-center bg-body-tertiary ">


                        <a href="{{ route('chats.createMessage', $ichancy->chat) }}"  wire:navigate.hover class="btn btn-primary btn-sm mx-3"><i class="fab fa-telegram-plane"></i></a>
                        <a href="{{ route('ichancies.edit', $ichancy) }}" class="btn btn-success btn-sm mx-3"><i class="fas fa-marker"></i></a>
                        <a href="{{ route('ichancies.ichancy_transaction',['type' => 'withdraw', 'ichancyid' => $ichancy->id]) }}" class="btn btn-danger btn-sm mx-3"><i class="fas fa-upload"></i></a>
                        <a href="{{ route('ichancies.ichancy_transaction',['type' => 'charge', 'ichancyid' => $ichancy->id]) }}" class="btn btn-warning btn-sm mx-3"><i class="fas fa-download"></i></a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        <h3>@lang('No Ichancy Account')</h3>
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>

    {{ $ichancies->links() }}
</div>

