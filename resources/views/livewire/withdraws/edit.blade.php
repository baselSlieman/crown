<div class="container my-3">
    <h4 class="ps-1"><i class="fas fa-edit"></i> @lang('Edit Withdraw'):</h4>
    {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}
    <form wire:submit="save">
        <div class="my-3">
            <ul class="list-group">
                <li class="list-group-item active" aria-current="true">@lang('Withdraw Info') {{ $withdraw->id}} :</li>
                <li class="list-group-item"><i class="far fa-user me-2"></i> {{ $withdraw->chat->username}} - {{ $withdraw->chat->id }}</li>
                <li class="list-group-item"><i class="fas fa-info-circle me-2"></i> @lang('Status'): {{$withdraw->status}}</li>
                <li class="list-group-item"><i class="far fa-calendar-alt me-2"></i> {{ $withdraw->created_at }}</li>
                <li class="list-group-item"><i class="fas fa-coins me-2"></i> @lang('Final'): {{ $withdraw->finalAmount }} NSP</li>
              </ul>
        </div>
        <div class="mb-3">
            <label for="code" class="form-label"><i class="fas fa-qrcode"></i> @lang('code/phone'):</label>
            <input type="text" name="code" id="code"  wire:model.live="code" class="form-control @error('code')  is-invalid @enderror" placeholder=""
                value="{{ old('code',$withdraw->code) }}" aria-describedby="helpId" />
                <div id="validationServer05Feedback" class="invalid-feedback">
                    @error('code') {{ $message }} @enderror
                  </div>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label"><i class="fas fa-coins"></i> @lang('Amount'):</label>
            <input type="text" class="form-control @error('amount')  is-invalid @enderror"  wire:model.live="amount" name="amount" id="amount" value="{{ old('description',$withdraw->amount) }}">
            <div id="validationServer05Feedback" class="invalid-feedback">
                    @error('amount') {{ $message }} @enderror
                  </div>
        </div>


        <button type="submit" class="btn btn-success me-3" ><span wire:loading.remove wire:target="save"><i class="fas fa-check"></i> @lang('Save')</span><span wire:loading wire:target="save"><i class="fas fa-spinner"></i> @lang('Saving..')</span></button>
        <button type="button"  class="btn btn-primary" onclick="history.back()">@lang('Back') <i class="fas fa-arrow-right"></i></button>
        @if ($withdraw->status == "rejected")
        | <a  class="btn btn-success" href="{{route('withdraws.completeOrder',$withdraw)}}"><i class="fas fa-check text-light"></i> @lang('Accept')</a>
        @endif


        </from>
</div>
