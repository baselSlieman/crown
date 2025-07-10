<div class="container mt-3 mb-5">
    <h4 class="ps-1"><i class="fas fa-edit"></i> @lang('Edit Charge'):</h4>
    <form wire:submit="save">
        <div class="my-3">
            <ul class="list-group">
                <li class="list-group-item active" aria-current="true">@lang('Charge Info') {{ $charge->id }}:</li>
                <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $charge->chat->username }}</li>
                <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $charge->chat_id }}</li>
                <li class="list-group-item"><i class="far fa-calendar-alt me-2"></i> {{ $charge->created_at }}</li>
                <li class="list-group-item"><i class="fas fa-coins me-2"></i> @lang('amount'): {{ $charge->amount }} NSP</li>
                <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> @lang('PID'): {{ $charge->processid }}</li>
                <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> @lang('status'): {{__($charge->status)}}</li>
            </ul>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">@lang('amount'):</label>
            <input type="text" wire:model.live.debounce.500ms="amount" name="amount" id="amount"
                class="form-control @error('amount') is-invalid @enderror" placeholder=""
                value="{{ old('amount', $charge->amount) }}" aria-describedby="helpId" />
            <div id="validationServer05Feedback" class="invalid-feedback">
                @error('amount')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="processid" class="form-label">@lang('PID'):</label>
            <input type="text" wire:model.live.debounce.500ms="processid" name="processid" id="processid"
                class="form-control @error('processid') is-invalid @enderror" placeholder=""
                value="{{ old('processid', $charge->processid) }}" aria-describedby="helpId" />
            <div id="validationServer05Feedback" class="invalid-feedback">
                @error('processid')
                    {{ $message }}
                @enderror
            </div>
        </div>

<div class="d-flex justify-content-between align-items-center mt-4">
        <button type="submit" class="btn btn-success me-3"><span wire:loading.remove wire:target="save"><i
                    class="fas fa-check"></i> @lang('Save')</span><span wire:loading wire:target="save"><i
                    class="fas fa-spinner"></i> @lang('Saving..')</span></button> |
        <button type="button" class="btn btn-primary mx-3" onclick="history.back()">@lang('Back') <i
                class="fas fa-arrow-right"></i></button> |
        <button type="button" class="btn btn-danger ms-3" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                class="far fa-trash-alt"></i> @lang('Delete')</button>
            </div>
    </form>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Delete Charge'):</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                    <p>@lang('Are you sure you want to delete this item?')</p>
                </div>
                <div class="modal-footer" id="mfooter">
                    <form class="d-inline-block" wire:submit="delete">
                        <button type="submit" class="btn btn-danger me-3"><span wire:loading.remove
                                wire:target="delete"><i class="fas fa-trash me-1"></i>@lang('Confirm')</span><span wire:loading
                                wire:target="delete"><i class="fas fa-spinner"></i> @lang('Loading..')</span></button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
