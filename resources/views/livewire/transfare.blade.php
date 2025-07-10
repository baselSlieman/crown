<div class="container my-3">
    <div class="row justify-content-center g-2 gx-3">
        <div class="col col-md-12 px-3">



            <div class="row align-items-center">
                <h3 class="col-6 order-1 order-md-1 col-md-5 mb-0 text-danger"><i class="fas fa-compress-arrows-alt"></i>
                    @lang('Transfers'): </h3>

                <div class="col-12 col-md-4 order-3 order-md-2 mt-3 mt-md-0 text-end text-md-center"><input
                        wire:model.live.debounce.500ms="search" type="search" class="form-control"
                        placeholder="@lang('Search').." /></div>
                <div class="col-6 col-md-3 order-2 order-md-3 mt-md-0 text-end">
                    <button onclick="history.back()" class="btn btn-outline-danger me-2"><i
                            class="fas @if (App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
                </div>
            </div>

            <div class="mt-4">
            @forelse ($results as $result)
                <div class="mx-0 my-3 my-md-3 mx-md-3 p-3 bg-body-secondary rounded-top">
                    <div class="row gx-0 gx-md-3">
                        <div class="col-4 text-start d-flex flex-column align-items-start">
                            <p class="mb-1">@lang('Sender')</p>
                            <p class="mb-1">
                                {{ $result->sender_number }}
                            </p>
                        </div>
                        <div class="col-4 text-center d-flex flex-column align-items-center">
                            <p class="mb-1">@lang('Reciver')</p>
                            <p class="mb-1">{{ $result->receiver_number }}</p>
                        </div>
                        <div class="col-4 text-end d-flex flex-column align-items-end">
                            <p class="mb-1">@lang('amount')</p>
                            <p class="mb-1">{{ $result->amount }}</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3">{{$result->id}}</div>
                        <div class="col-9 text-end"><span><i class="far fa-calendar-check"></i> {{ $result->created_at }}</span></div>
                        </div>
                </div>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        <h3 class="text-center mt-5 alert alert-danger">@lang('Empty')</h3>
                    </td>
                </tr>
            @endforelse
            </div>
        </div>
    </div>


 {{ $results->links() }}
</div>
