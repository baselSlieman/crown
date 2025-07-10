<div class="container my-3">
    <div class="row justify-content-center g-2 gx-3">
        <div class="col col-md-12 px-3">



            <div class="row align-items-center">
                <h3 class="col-6 order-1 order-md-1 col-md-5 mb-0"><i class="fas fa-percent"></i> @lang('Wheel'):</h3>

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


                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>

                                <td>
                                    <p>@lang('chatId'): {{ $result->chat_id }}</p>
                                    <p class="mt-1">@lang('rotation'): {{ $result->rotation }}</p>
                                    <p class="mt-1">@lang('amount'): {{ $result->amount }}</p>
                                    <p class="mt-1">@lang('difference'): {{ $result->difference }}</p>
                                    <p class="mt-1">@lang('created_at'): {{ $result->created_at }}</p>
                                    <p class="mt-1">@lang('updated_at'): {{ $result->updated_at }}</p>
                                    <p class="mt-1">@lang('status'):
                                        @if($result->status)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @else
                                            <i class="fas fa-hourglass text-danger"></i>
                                        @endif
                                        <button wire:click="deletewheel('{{ $result->id }}')" class="btn btn-outline-danger border-0"><i class="far fa-trash-alt"></i></button>
                                    </p>





                            </td>


                        </tr>



                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <h3>No wheels</h3>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{ $results->links() }}
    </div>
</div>



</div>
