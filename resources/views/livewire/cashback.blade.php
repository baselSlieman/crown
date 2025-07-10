@php
    $itr = 1;
@endphp
<div class="container my-3">
    <div class="row justify-content-center g-2 gx-3">
        <div class="col col-md-12 px-3">



            <div class="row align-items-center">
                <h3 class="col-6 order-1 order-md-1 col-md-5 mb-0"><i class="fas fa-retweet"></i>
                    @lang('Cashback'):</h3>

                <div class="col-12 col-md-4 order-3 order-md-2 mt-3 mt-md-0 text-end text-md-center"><input
                        wire:model.live.debounce.500ms="search" type="search" class="form-control"
                        placeholder="@lang('Search').." /></div>
                <div class="col-6 col-md-3 order-2 order-md-3 mt-md-0 text-end">
                    <button onclick="history.back()" class="btn btn-outline-danger me-2"><i
                            class="fas @if (App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
                </div>
            </div>
@if (session('success'))
                <div class="alert alert-success mt-3 alert-dismissible">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif (session('danger'))
                <div class="alert alert-danger mt-3 alert-dismissible">
                    {{ session('danger') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive mb-3">
                <table class="table mt-3 table-bordered table-striped" style="vertical-align: middle;">
                    <thead class="bg-dark-subtle">
                        <tr>
                            <th></th>
                            <th><i class="fas fa-user"></i> @lang('User')</th>
                            <th>@lang('summary')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>
                                <td rowspan="2" class="text-center fw-bold border-bottom border-danger">{{ $itr++ }}</td>
                                <td>
                                    {{ $result->id }}<br>
                                    {{ $result->username }}
                                </td>
                                <td>
                                    <p class="mb-1">@lang('charge'): <span
                                            class="text-success">{{ $result->total_charges }}</span></p>
                                    <p class="mb-1">@lang('wallet'): <span
                                            class="text-primary">{{ $result->balance }}</span></p>
                                    <p class="mb-1">@lang('withdraw'): <span
                                            class="text-danger">{{ $result->total_withdraws }}</span></p>
                                    <p class="mb-1">@lang('difference'): <span
                                            class="fw-bold">{{ $result->difference }}</span></p>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="mb-1">@lang('ichancy'):<button
                                            wire:click="getData('{{ $result->identifier }}')"
                                            class="btn btn-outline-success py-0 ms-1 ms-md-3 btn-sm"><i
                                                class="fas fa-coins"></i></button><span wire:loading
                                            wire:target="getData('{{ $result->identifier }}')">
                                            <i class="fa fa-spinner fa-spin text-danger ms-2"></i>
                                        </span>
                                        @if ($currentKey == $result->identifier)
                                            <span class="text-danger d-block mt-3">
                                                @lang('ichancy'): {{ $value }}</span>
                                        @endif
                                    </p>
                                </td>
                                <td>
                                    <p class="mb-1">@lang('wheel'): <span
                                            class="text-success">{{ $result->total_wheels }} / {{$result->count_amount}}</span>
                                    </p>
                                    <P>
                                        @lang('can'): @if($result->canwheel || $result->count_amount==0)
                                                <i class="far fa-check-circle text-success"></i>
                                                <button
                                            wire:click="notify('{{ $result->id }}')"
                                            class="btn btn-outline-success py-0 ms-1 ms-md-3 btn-sm"><i
                                                class="far fa-bell"></i></button>
                                                @else
                                                <i class="fas fa-ban text-danger"></i>
                                                <button
                                            wire:click="giveWheel('{{ $result->id }}')"
                                            class="btn btn-outline-success  py-0 ms-1 ms-md-3 btn-sm"><i
                                                class="fas fa-plus"></i></button>
                                            @endif
                                    </P>
                                </td>
                            </tr>

                            {{-- <td class="d-none d-md-table-cell">
                                <p class="mt-1"><i class="fas fa-receipt"></i> @lang('code'): <span
                                        class="text-primary">{{ $result->code }}</span>
                                </p>
                                <p class="mt-1"><i class="far fa-calendar-alt"></i> {{ $result->created_at }}</p>
                                <p class="mb-1 mt-3"><i class="fas fa-info-circle"></i> {{ __($result->status) }}
                                </p>
                            </td> --}}





                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <h3>No affiliates</h3>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- {{ $results->links() }} --}}
        </div>
    </div>



</div>
