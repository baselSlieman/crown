<div class="col col-md-12 px-3">

    {{-- <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0"><i class="fas fa-wallet"></i> Charges:</h2>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-danger me-2"><i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="mt-3"><input wire:model.live.debounce.500ms="search" type="search" class="form-control" placeholder="search..." /></div> --}}


    <div class="container">
        <div class="row align-items-center clearfix">
            <div class="col-8 col-md-4 order-1 order-md-1 px-0">
                <div>
                    <h3 class="mb-0"><i class="fas fa-download"></i> @lang('Charges'):</h3>
                </div>
            </div>
            <div class="col-12 col-md-4 order-3 order-md-2 px-0 mt-3 mt-md-0">
                <div><input wire:model.live.debounce.500ms="search" type="search" class="form-control"
                        placeholder="@lang('Search')..." /></div>
            </div>
            <div class="col-4 col-md-4 order-2 order-md-3 px-0">
                <div>
                    <button onclick="history.back()" class=" btn btn-sm btn-md-lg btn-outline-danger float-end"><i
                            class="fas @if (App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
                </div>
            </div>
        </div>
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
    <div class="table-responsive">
        <table class="table mt-3 table-bordered" style="vertical-align: middle;">
            <thead>
                <tr>
                    <th>id</th>
                    <th class=" text-secondary"><i class="fas fa-user"></i> @lang('User')</th>
                    <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> @lang('info')</th>
                    <th class="d-none d-md-table-cell"><i class="fas fa-cog"></i> @lang('option')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($charges as $charge)
                    <tr>
                        <td>{{ $charge->id }}</td>
                        <td>
                            <p class="mt-1"><i class="far fa-user"></i> @lang('username'):
                                {{ $charge->chat->username }}</p>
                            <p class="mt-1"><i class="fas fa-user"></i> @lang('chat id'): {{ $charge->chat_id }}</p>
                            <p class="mb-1 mt-3"><i class="fas fa-coins"></i> @lang('amount'): <strong
                                    class="text-danger">{{ '' . number_format($charge->amount, 0) }}</strong> NSP</p>
                        </td>



                        <td class="d-none d-md-table-cell">
                            <p class="d-none d-md-block"><i class="far fa-calendar-alt"></i> {{ $charge->created_at }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                            <div><i class="fas fa-hashtag"></i> @lang('PID'): {{ $charge->processid }}</div>
                            <div wire:key="charge-{{ $charge->id }}">
                                <i class="fas fa-coins me-1"></i><span>@lang('wallet')</span>
                                <button wire:click="getBalance({{$charge->id}})"
                                    class="btn btn-sm btn-outline-danger border-0 py-0 px-1 fs-6 fw-bold">
                                    @if ($currentKeyb == $charge->id)
                                        <strong>{{ number_format($balanceb, 0) }}</strong>
                                    @else
                                        <span>{{ number_format($charge->chat->balance, 0) }}</span>
                                    @endif
                                </button>
                                NSP
                                <span wire:loading wire:target="getBalance({{$charge->id}})">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                            </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <p>
                                    @switch($charge->status)
                                        @case('complete')
                                            <i class="far fa-check-circle text-success"></i>
                                        @break

                                        @case('pending')
                                            <i class="far fa-hourglass text-primary"></i>
                                        @break

                                        @default
                                    @endswitch {{ __($charge->status) }}
                                </p>
                                <p>
                                    @switch($charge->method)
                                        @case('سيريتل كاش')
                                            <img src="{{ asset('dashboard/img/chah.jpg') }}" width="50" />
                                        @break

                                        @case('بنك بيمو')
                                            <img src="{{ asset('dashboard/img/bemo.jpg') }}" width="40" />
                                        @break

                                        @case('MTN كاش')
                                            <img src="{{ asset('dashboard/img/mtn.jpg') }}" width="40" />
                                        @break

                                        @default
                                            {{ $charge->method }}
                                    @endswitch
                                </p>
                            </div>
                        </td>

                        <td class="d-none d-md-table-cell">
                            <div class="text-center mt-2">
                                <a href="{{ route('chats.createMessage', $charge->chat) }}" wire:navigate.hover
                                    class="btn btn-primary"><i class="fab fa-telegram-plane"></i></a>
                                <a href="{{ route('charges.edit', $charge) }}" class="btn btn-success"
                                    wire:navigate.hover><i class="fas fa-marker"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr class="d-table-row d-md-none">
                        <td colspan="2">
                            <i class="far fa-calendar-alt me-2"></i> <span class="fw-bold">@lang('date'):</span>
                            {{ $charge->created_at }}<br>
                            <i class="fas fa-info-circle me-2"></i> <span class="fw-bold">@lang('PID'):</span>
                            {{ $charge->processid }}<br>
                            <i class="fas fa-info-circle me-2"></i> <span class="fw-bold">@lang('status'):</span>
                            @switch($charge->status)
                                @case('complete')
                                    <i class="far fa-check-circle text-success"></i>
                                @break

                                @case('pending')
                                    <i class="far fa-hourglass text-primary"></i>
                                @break

                                @default
                            @endswitch {{ __($charge->status) }}<br>
                            <div class="d-flex justify-content-between align-items-center">
                                <div wire:key="charge-{{ $charge->id }}">
                                <i class="fas fa-coins me-1"></i><span>@lang('wallet')</span>
                                <button wire:click="getBalance({{$charge->id}})"
                                    class="btn btn-sm btn-outline-danger border-0 py-0 px-1 fs-6 fw-bold">
                                    @if ($currentKeyb == $charge->id)
                                        <strong>{{ number_format($balanceb, 0) }}</strong>
                                    @else
                                        <span>{{ number_format($charge->chat->balance, 0) }}</span>
                                    @endif
                                </button>
                                NSP
                                <span wire:loading wire:target="getBalance({{$charge->id}})">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                            </div>
                                <div>

                                    @switch($charge->method)
                                        @case('سيريتل كاش')
                                            <img src="{{ asset('dashboard/img/chah.jpg') }}" width="50" />
                                        @break

                                        @case('بنك بيمو')
                                            <img src="{{ asset('dashboard/img/bemo.jpg') }}" width="40" />
                                        @break

                                        @case('MTN كاش')
                                            <img src="{{ asset('dashboard/img/mtn.jpg') }}" width="40" />
                                        @break

                                        @default
                                            {{ $charge->method }}
                                    @endswitch

                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="d-table-row d-md-none">
                        <td colspan="2" class="text-center bg-body-tertiary ">


                            <a href="{{ route('chats.createMessage', $charge->chat) }}"
                                class="btn btn-primary btn-sm mx-3" wire:navigate.hover><i
                                    class="fab fa-telegram-plane"></i></a>
                            <a href="{{ route('charges.edit', $charge) }}" class="btn btn-success btn-sm mx-3"
                                wire:navigate.hover><i class="fas fa-marker"></i></a>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="messageModal{{ $charge->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Message</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="post" action="{{ route('charges.store', $charge) }}">
                                    <div class="modal-body">
                                        @csrf
                                        <input type="text" name="message" class="form-control"
                                            placeholder="type here..." />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <h3>@lang('No charges')</h3>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{ $charges->links() }}
    </div>
