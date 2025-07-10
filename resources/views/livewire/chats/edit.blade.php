<div class="container my-3">
    <h4 class="ps-1"><i class="fas fa-edit"></i> @lang('Edit Chat'):</h4>
    @if ($errors->any())
        <div class="alert alert-danger  alert-dismissible">

                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @elseif (session('danger'))
                    <div class="alert alert-danger mt-3">
                        {{ session('danger') }}
                    </div>
                @endif
    <form wire:submit="save" class="mt-3">
        <div class="my-3">
            <ul class="list-group">
                <li class="list-group-item active" aria-current="true">@lang('Chat Info') {{ $chat->id}}:</li>
                <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $chat->username}}</li>
                <li class="list-group-item"><i class="far fa-calendar-alt me-2"></i> {{ $chat->created_at }}</li>
                <li class="list-group-item"><i class="fas fa-wallet me-2"></i> @lang('wallet'): {{ $chat->balance }} NSP</li>
                <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> @lang('info'): {{$chat->info}}</li>
              </ul>
        </div>
        <div class="mb-3">
            <label for="balance" class="form-label">@lang("wallet"):</label>
            <input type="text" name="balance" id="balance"  wire:model.live="balance" class="form-control" placeholder=""
                value="{{$balance}}" aria-describedby="helpId" />
        </div>

        <div class="mb-3">
            <label for="info" class="form-label">@lang('info'):</label>
            <textarea class="form-control" name="info" id="info"  wire:model="info" rows="3">{{ old('info',$chat->info) }}</textarea>
        </div>

    <div class="d-flex justify-content-between d-md-block">
        <button type="submit" class="btn btn-success me-3"><span wire:loading.remove wire:target="save"><i class="fas fa-check"></i> @lang('Save')</span><span wire:loading wire:target="save"><i class="fas fa-spinner"></i> @lang('Saving..')</span></button> |
        <button type="button" class="btn btn-primary mx-3"  onclick="history.back()">@lang('Back') <i class="fas fa-arrow-right"></i></button> |
        <button type="button" class="btn btn-danger ms-3" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="far fa-trash-alt"></i> @lang('Delete')</button>
    </div>
    </form>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">@lang('Delete Chat'):</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <p>@lang('Are you sure you want to delete this item?')</p>
                        <span class="text-danger">@lang('This will lead to the deletion of all data associated with it.')</span>
                    </div>
                    <div class="modal-footer" id="mfooter">
                        <form class="d-inline-block" wire:submit="delete">
                            <div class="text-success pe-2" wire:loading  wire:target="delete">
                                <i class="fas fa-spinner"></i> @lang('Loading...')
                            </div>
                            <button type="submit" class="btn btn-danger me-3"><i
                                    class="fas fa-trash me-1"></i>@lang('Confirm')</button><button type="button"
                                class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button></form>

                    </div>
                </div>
            </div>
        </div>
</div>
