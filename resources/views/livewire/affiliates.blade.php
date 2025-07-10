<div class="container my-3">
    <div class="row justify-content-center g-2 gx-3">
        <div class="col col-md-12 px-3">



            <div class="row align-items-center">
                <h3 class="col-6 order-1 order-md-1 col-md-5 mb-0"><i class="fas fa-percent"></i> @lang('Affiliates'):</h3>

                <div class="col-12 col-md-4 order-3 order-md-2 mt-3 mt-md-0 text-end text-md-center"><input
                        wire:model.live.debounce.500ms="search" type="search" class="form-control"
                        placeholder="@lang('Search').." /></div>
                <div class="col-6 col-md-3 order-2 order-md-3 mt-md-0 text-end">
                    <button onclick="history.back()" class="btn btn-outline-danger me-2"><i
                            class="fas @if (App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table mt-3 table-bordered table-striped" style="vertical-align: middle;">
                    <thead class="bg-dark-subtle">
                        <tr>

                            <th><i class="fas fa-user"></i> @lang('User')</th>
                            <th>@lang('total affilliats')</th>
                            <th>@lang('affiliate_amount')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>

                                <td>
                                    <p></p>
                                    <p class="mt-1">{{ $result->chat_id }} -
                                        @isset($result->chat_username)
                                            {{ $result->chat_username }}
                                        @endisset
                                    @empty($result->chat_username)
                                        @lang('no username')
                                    @endempty
                                </p>




                            </td>
                            <td>
<p class="mb-1 mt-3"><strong
                                        class="text-danger">{{ $result->follower_chats_count }}</strong></p>

                            </td>
                            <td><p class="mb-1 mt-3"><strong
                                        >{{ $result->sumaff }}NSP</strong></p></td>



                            {{-- <td class="d-none d-md-table-cell">
                                <p class="mt-1"><i class="fas fa-receipt"></i> @lang('code'): <span
                                        class="text-primary">{{ $result->code }}</span>
                                </p>
                                <p class="mt-1"><i class="far fa-calendar-alt"></i> {{ $result->created_at }}</p>
                                <p class="mb-1 mt-3"><i class="fas fa-info-circle"></i> {{ __($result->status) }}
                                </p>
                            </td> --}}

                        </tr>



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
