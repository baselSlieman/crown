<div class="col col-md-12">


    <div class="container">
        <div class="row align-items-center clearfix">
            <div class="col-6 col-md-4 order-1 order-md-1 px-0"><div><h3 class="mb-0"><i class="fas fa-upload"></i> @lang('withdraws'):</h3></div></div>
            <div class="col-12 col-md-4 order-3 order-md-2 px-0 mt-3 mt-md-0"><div><input wire:model.live.debounce.500ms="search" type="search" class="form-control" placeholder="@lang('search')..." /></div></div>
            <div class="col-6 col-md-4 order-2 order-md-3 px-0"><div>
                <button onclick="history.back()" class=" btn btn-sm btn-md-lg btn-outline-danger float-end"><i class="fas @if(App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
            </div></div>
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
    <div class="table-responsive">
        <table class="table mt-3 text-center table-bordered" style="vertical-align: middle;">
            <thead>
                <tr>
                    <th>id</th>
                    <th><i class="fas fa-user"></i> @lang('User')</th>
                    <th><i class="fas fa-money-bill-wave"></i> @lang('amount')</th>
                    <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> @lang('info')</th>
                    <th class="d-none d-md-table-cell"><i class="fas fa-cog"></i> @lang('options')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdraws as $withdraw)
                    <tr>
                        <td>{{ $withdraw->id }}</td>
                        <td>
                            <p class="mt-1" title="{{ $withdraw->chat->balance }} NSP"><i class="far fa-user"></i>
                                {{ $withdraw->chat->username }}</p>
                            <span class="badge rounded-pill text-bg-primary bg-primary">
                                <i class="fas fa-hashtag"></i> <a class="text-light"
                                    href="#">{{ $withdraw->chat->id }}</a>
                            </span>
                            <p class="mb-1 mt-3"><i style="cursor: pointer" class="far fa-copy"></i> <code
                                    onclick="javascript:navigator.clipboard.writeText(this.innerText);">{{ $withdraw->code }}</code>
                            </p>
                        </td>

                        <td>
                            <p class="mt-1 text-primary fs-6 text-break text-nowrap"><i class="fas fa-coins"></i>
                                @lang('amount'): {{ $withdraw->amount }} NSP</p>
                            <p class="text-danger"><i class="fas fa-coins"></i> @lang('Final'): {{ $withdraw->finalAmount }} NSP
                            </p>
                            <p class="mb-1 text-success"><i class="fas fa-coins"></i> @lang('Discount'):
                                {{ $withdraw->discountAmount }} NSP</p>
                        </td>

                        <td class="d-none d-md-table-cell">
                            <p class="my-3 @if ($withdraw->status == 'requested') badge text-bg-warning
                            @elseif ($withdraw->status == 'reject')
                            badge text-bg-danger
                            @elseif ($withdraw->status == 'canceled')
                            badge text-bg-info
                            @else
                            badge text-bg-success @endif">{{__($withdraw->status)}}</p>
                            <p class="d-none d-md-block"><i class="far fa-calendar-alt"></i>
                                {{ $withdraw->created_at }}</p>
                            <p>


                                @switch($withdraw->method)
                                    @case('سيريتل')
                                        <img src="{{ asset('dashboard/img/chah.jpg') }}" width="100" />
                                    @break

                                    @case('الهرم')
                                        <img src="{{ asset('dashboard/img/haram.jpg') }}" width="100" />
                                    @break

                                    @case('بيمو')
                                        <img src="{{ asset('dashboard/img/bemo.jpg') }}" width="100" />
                                    @break

                                    @case('الفؤاد')
                                        <img src="{{ asset('dashboard/img/fouad.jpg') }}" width="100" />
                                    @break

                                    @case('MTN')
                                        <img src="{{ asset('dashboard/img/mtn.jpg') }}" width="100" />
                                    @break

                                    @default
                                    {{ $withdraw->method }}
                                @endswitch
                                @isset($withdraw->subscriber)
                                    <span class="d-block">{{ $withdraw->subscriber }}</span>
                                @endisset


                            </p>
                        </td>

                        <td class="d-none d-md-table-cell">
                            @if ($withdraw->status == 'requested')
                                <div class="text-center">
                                    {{-- <a href="{{ route('withdraws.completeOrder', $withdraw) }}" class="btn btn-success"><i class="fas fa-check text-light"></i></a> --}}
                                    <button class="btn btn btn-success" onclick="openModal('complete',{{$withdraw->id}})"><i class="fas fa-check text-light"></i></button>
                                    <button class="btn btn-warning" onclick="openModal('reject',{{$withdraw->id}})"><i class="far fa-times-circle"></i></button>
                                </div>
                            @endif
                            <div class="text-center mt-2">
                                <a href="{{ route('withdraws.edit', $withdraw) }}" class="btn btn-primary" wire:navigate.hover><i
                                        class="fas fa-marker"></i></a>
                                {{-- <form method="POST" class="d-inline-block"
                                    action="{{ route('withdraws.destroy', $withdraw) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                </form> --}}
                                <button class="btn btn-danger" onclick="openModal('delete',{{$withdraw->id}})"><i
                                        class="fas fa-trash"></i></button>
                            </div>


                        </td>
                    </tr>
                    <tr class="d-table-row d-md-none">
                        <td colspan="2">
                            <div
                                class="py-1 mb-0 d-inline-block
                            @if ($withdraw->status == 'requested') badge text-bg-warning
                            @elseif ($withdraw->status == 'reject')
                            badge text-bg-danger
                            @elseif ($withdraw->status == 'canceled')
                            badge text-bg-info
                            @else
                            badge text-bg-success @endif">
                                <i class="fas fa-info-circle me-1"></i>{{__($withdraw->status)}}
                            </div>
                            @switch($withdraw->method)
                                @case('سيريتل')
                                    <img src="{{ asset('dashboard/img/chah.jpg') }}" width="50" />
                                @break

                                @case('الهرم')
                                    <img src="{{ asset('dashboard/img/haram.jpg') }}" width="50" />
                                @break

                                @case('بيمو')
                                    <img src="{{ asset('dashboard/img/bemo.jpg') }}" width="50" />
                                @break

                                @case('الفؤاد')
                                    <img src="{{ asset('dashboard/img/fouad.jpg') }}" width="50" />
                                @break

                                @case('MTN')
                                    <img src="{{ asset('dashboard/img/mtn.jpg') }}" width="50" />
                                @break

                                @default
                                {{ $withdraw->method }}
                            @endswitch
                            @isset($withdraw->subscriber)
                                <small class="d-block">{{ $withdraw->subscriber }}</small>
                            @endisset
                        </td>
                        <td colspan="3"><i class="far fa-calendar-alt me-2"></i> {{ $withdraw->created_at }}</td>
                    </tr>
                    <tr class="bg-dark d-table-row d-md-none">
                        <td colspan="5" class="bg-body-secondary">
                            @if ($withdraw->status == 'requested')
                                {{-- <a href="{{ route('withdraws.completeOrder', $withdraw) }}" class="btn btn-success btn-sm"><i class="fas fa-check text-light"></i></a> --}}
                                <button class="btn btn-success btn-sm" onclick="openModal('complete',{{$withdraw->id}})"><i class="fas fa-check text-light"></i></button>
                                    <button class="btn btn-warning mx-3 btn-sm" onclick="openModal('reject',{{$withdraw->id}})"><i class="far fa-times-circle"></i></button>
                            @endif

                            <a href="{{ route('withdraws.edit', $withdraw) }}" class="btn btn-primary me-3 btn-sm" wire:navigate.hover><i
                                    class="fas fa-marker"></i></a>
                                    <button class="btn btn-danger btn-sm" onclick="openModal('delete',{{$withdraw->id}})"><i
                                        class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    {{-- <div class="modal fade" id="messageModal{{ $withdraw->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Message</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="post" action="{{ route('withdraws.rejectOrder', $withdraw) }}">
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
                    </div> --}}
                    <!-- Modal -->

                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <h3>@lang('No Withdraws')</h3>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                </div>
                <div class="modal-footer" id="mfooter">

                </div>
            </div>
        </div>
    </div>
        {{ $withdraws->links() }}
    </div>

    <script  data-navigate-once>
        function openModal(action,withdrawid) {
            if (action === 'delete') {
                $('#dynamicModal').find('.modal-title').text(@json(__('Delete Item:')));
                $('#modal-body').html(
                    '<p>'+@json(__('Are you sure you want to delete this item?'))+'</p>'
                    );
                $('#dynamicModal').find('.modal-footer').html(
                    '<form wire:submit="delete('+ withdrawid + ')" class="d-inline-block"><button type="submit" class="btn btn-danger me-3"><span wire:loading.remove wire:target="delete"><i class="fas fa-trash me-1"></i> '+@json(__('Confirm'))+'</span><span wire:loading wire:target="delete"><i class="fas fa-spinner"></i> '+@json(__('Deleting..'))+'</span></button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'+@json(__('Close'))+'</button></form>'
                    );
                $('#dynamicModal').find('#mfooter').removeClass('d-none');
            } else if (action === 'reject') {
                $('#dynamicModal').find('.modal-title').text(@json(__('Message:')));
                $('#modal-body').html(
                    '<form  wire:submit="reject('+ withdrawid + ')"><input type="text" name="message" wire:model="rejectedMessage" required class="form-control" placeholder="'+@json(__('type here...'))+'" /></div><div class="modal-footer"><button type="button" class="btn btn-secondary"data-bs-dismiss="modal">'+@json(__('Close'))+'</button><button type="submit" class="btn btn-primary"><span wire:loading.remove wire:target="reject"><i class="fas fa-check me-1"></i> '+@json(__('Confirm'))+'</span><span wire:loading wire:target="reject"><i class="fas fa-spinner"></i> '+@json(__('Rejecting..'))+'</span></button></div></form>'
                );
                $('#dynamicModal').find('#mfooter').addClass('d-none');
            }else if (action === 'complete') {
                $('#dynamicModal').find('.modal-title').text(@json(__('Complete Withdraw:')));
                $('#modal-body').html(
                    '<p>'+@json(__('Are you sure you want to confirm this withdraw?'))+'</p>'
                    );
                $('#dynamicModal').find('.modal-footer').html(
                    '<form wire:submit="complete('+ withdrawid + ')" class="d-inline-block"><button type="submit" class="btn btn-success me-3"><span wire:loading.remove wire:target="complete"><i class="fas fa-check me-1"></i> '+@json(__('Confirm'))+'</span><span wire:loading wire:target="complete"><i class="fas fa-spinner"></i> '+@json(__('Loading..'))+'</span></button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'+@json(__('Close'))+'</button></form>'
                    );
                $('#dynamicModal').find('#mfooter').removeClass('d-none');
            }

            var myModal = new bootstrap.Modal(document.getElementById('dynamicModal'), {});
            myModal.show();
        }
    </script>

{{-- <script  data-navigate-once>
    function openModal(action,withdrawid) {
        if (action === 'delete') {
            $('#dynamicModal').find('.modal-title').text('Delete Item:');
            $('#modal-body').html(
                '<p>Are you sure you want to delete this item?</p>'
                );
            $('#dynamicModal').find('.modal-footer').html(
                '<form method="POST" class="d-inline-block" action="{{ url("withdraws") }}/' + withdrawid + '">@csrf @method('DELETE')<button type="submit" class="btn btn-danger me-3"><i class="fas fa-trash me-1"></i>Confirm</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></form>'
                );
            $('#dynamicModal').find('#mfooter').removeClass('d-none');
        } else if (action === 'reject') {
            $('#dynamicModal').find('.modal-title').text('Message');
            $('#modal-body').html(
                '<form method="post" action="{{ url("withdraws/rejectOrder") }}/' + withdrawid + '"> @csrf <input type="text" name="message" required class="form-control" placeholder="type here..." /></div><div class="modal-footer"><button type="button" class="btn btn-secondary"data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Confirm</button></div></form>'
            );
            $('#dynamicModal').find('#mfooter').addClass('d-none');
        }

        var myModal = new bootstrap.Modal(document.getElementById('dynamicModal'), {});
        myModal.show();
    }
</script> --}}
