<div class="nav-item  form-check form-switch">
    <label style="cursor: pointer" class="form-check-label" for="darkModeSwitch">@if (session('mode')==='dark')<i class="far fa-lightbulb text-warning nightmode"></i>@else<i class="far fa-moon nightmode"></i>@endif</label>
    <input class="d-none form-check-input" wire:model.live="mode" type="checkbox" id="darkModeSwitch" aria-label="Switch between light and dark mode" data-bs-toggle="tosoltip" data-bs-placement="top" title="Switch between light and dark mode" >
</div>


