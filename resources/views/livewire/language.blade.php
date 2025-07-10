{{-- <div>
    <i class="fas fa-globe"></i> <select class="border-o" wire:change="switchLanguage($event.target.value)">
        <option value="en" {{ $selectedLanguage == 'en' ? 'selected' : '' }}>En</option>
        <option value="ar" {{ $selectedLanguage == 'ar' ? 'selected' : '' }}>Ar</option>
        <!-- Add more language options as needed -->
    </select>
</div> --}}


<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle langdrop" data-bs-toggle="dropdown">
        <i class="fas fa-globe"></i> <span class="d-none d-md-inline">@lang(App::currentLocale())</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0" style="box-shadow: 0pc 3px 3px #c9c9c9;">
        <a href="#" class="dropdown-item" wire:click="english">
                <div class="ms-2">
                    <h6 class="fw-normal mb-0">En</h6>
                </div>

        </a>
        <hr class="dropdown-divider">
        <a href="#" class="dropdown-item" wire:click="arabic">

                <div class="ms-2">
                    <h6 class="fw-normal mb-0">@lang('AR')</h6>
                </div>

        </a>

    </div>
</div>
@script
    <script>
            $wire.on('languageChanged', function () {
                window.location.reload();
            });
    </script>
    @endscript
