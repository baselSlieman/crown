<div class="container my-3">
    <h4 class="ps-1"><i class="fas fa-wallet"></i> @lang('Manuel Charge'):</h4>
    @if ($errors->any())
        <div class="alert alert-danger  alert-dismissible">

                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
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
    <form wire:submit="save" class="mt-3">
        <div class="my-3">
            <ul class="list-group">
                <li class="list-group-item active" aria-current="true">@lang('Chat Info') {{ $chat}}:</li>
              </ul>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">@lang("amount"):</label>
            <input type="text" name="amount" id="amount"  wire:model.live="amount" class="form-control" placeholder=""
                value="{{$amount}}" aria-describedby="helpId" />
        </div>

        <div class="mb-3">
            <label for="processid" class="form-label">@lang("PID"):</label>
            <input type="text" name="processid" id="processid"  wire:model.live="processid" class="form-control" placeholder=""
                value="{{$processid}}" aria-describedby="helpId" />
        </div>

    <div class="d-flex justify-content-between d-md-block">
        <button type="submit" class="btn btn-success me-3"><span wire:loading.remove wire:target="save"><i class="fas fa-check"></i> @lang('Save')</span><span wire:loading wire:target="save"><i class="fas fa-spinner"></i> @lang('Saving..')</span></button> |
        <button type="button" class="btn btn-primary ms-3"  onclick="history.back()">@lang('Back') <i class="fas fa-arrow-right"></i></button>
    </div>
    </form>
</div>
