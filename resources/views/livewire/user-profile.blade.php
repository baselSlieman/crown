<div class="container my-3">
    <h4 class="ps-1"><i class="fas fa-users-cog"></i> @lang('User Profile'):</h4>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="save">


        <div class="mb-3">
            <label for="name" class="form-label">@lang("username"):</label>
            <input type="text" name="name" id="name"  wire:model.lazy="name" class="form-control text-start" placeholder="" aria-describedby="helpId" />
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>



        <div class="mb-3">
            <label for="email" class="form-label">@lang("Email Address"):</label>
            <input type="email" name="email" id="email"  wire:model.lazy="email" class="form-control text-start" placeholder="" aria-describedby="helpId" />
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>


        <div class="mb-3">
            <label for="password" class="form-label">@lang("Password"):</label>
            <input type="password" name="password" id="password"  wire:model.lazy="password" class="form-control text-start" placeholder="" aria-describedby="helpId"  autocomplete="new-password"/>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">@lang("password_confirmation"):</label>
            <input type="password"  wire:model.lazy="password_confirmation" class="form-control text-start" placeholder="" aria-describedby="helpId"/>
        </div>




        <div class="my-3">
            <button type="submit" class="btn btn-success"><span wire:loading.remove wire:target="save"><i class="fas fa-check"></i> @lang('Save')</span><span wire:loading wire:target="save"><i class="fas fa-spinner"></i> @lang('Saving..')</span></button>
            <button type="button" class="btn btn-danger ms-2" onclick="history.back()">@lang('Back') <i class="fas fa-arrow-right"></i></button>
        </div>
    </form>
</div>
