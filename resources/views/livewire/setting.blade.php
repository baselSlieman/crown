<div class="container my-3">
    <div class="row justify-content-center g-2 gx-3">
        <div class="col col-md-12 px-3">



            <div class="row align-items-center">
                <h3 class="col-6"><i class="fas fa-tools"></i> @lang('Settings'): </h3>


                <div class="col-6 text-end">
                    <button onclick="history.back()" class="btn btn-outline-danger me-2"><i
                            class="fas @if (App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
                </div>
            </div>
            @if ($errors->any())
                            <div class="alert alert-danger my-3 alert-dismissible">

                                @foreach ($errors->all() as $error)
                                    <p class="mb-0">{{ $error }}</p>
                                @endforeach

                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
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


            <div class="m-3 mt-4 p-3 bg-body-secondary rounded-top">
                    <form wire:submit="updateBonus">
                    <div class="row gx-0 gx-md-3 align-items-center">
                        <div class="col-6 text-start d-flex flex-column align-items-start">
                            <p class="mb-1">

                                 @if ($bootStatus)
                                        <i class="fas fa-check-circle px-1 text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle  px-1 text-danger"></i>
                                    @endif @lang("Boot"):</p>
                        </div>

                        <div class="col-6 text-end d-flex flex-column align-items-end">
                            @if ($bootStatus)
                           <button wire:click="changeBootStatus()"
                                    class="btn btn-danger btn-sm  me-0 me-md-3">
                                        <i class="fas  fa-times me-1"></i> @lang("OFF")
                                            </button>
                                            <span wire:loading wire:target="changeBonusStatus()">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </span>
                            @else
                            <button wire:click="changeBootStatus()"
                                    class="btn btn-success btn-sm me-0 me-md-3">
                                        <i class="fas fa-check-circle me-1"></i> @lang("ON")
                                    @endif
                                    <span wire:loading wire:target="changeBonusStatus()">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                        </div>
                    </div>
                </form>
                </div>

                <div class="m-3 p-3  mt-5 bg-body-secondary rounded-top">
                    <form wire:submit="updateBonus">
                    <div class="row gx-0 gx-md-3 align-items-center">
                        <div class="col-4 text-start d-flex flex-column align-items-start">
                            <p class="mb-1">
                                <button wire:click="changeBonusStatus()"
                                    class="btn btn-outline border-0 px-1">
                                    @if ($bonusStatus)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                    <span wire:loading wire:target="changeBonusStatus()">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                                @lang("Bonus"):</p>
                        </div>
                        <div class="col-4 text-center d-flex flex-column align-items-center">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">%</span>
                            <input type="number" class="form-control  text-center" name="userid" id="userid"
                                aria-describedby="helpId" value="{{$settings->bonus}}"
                                wire:model.defer="bonus" />
                            </div>
                        </div>
                        <div class="col-4 text-end d-flex flex-column align-items-end">
                            <button type="submit" class="btn btn-success me-0 me-md-3 btn-sm">
                            <span wire:loading.remove wire:target="confirm"><i class="fas fa-plus me-1"></i>
                                @lang('Save')</span><span wire:loading wire:target="confirm"><i
                                    class="fas fa-spinner"></i> @lang('Saving..')</span>
                        </button>
                        </div>
                    </div>
                </form>
                </div>











                <div class="m-3 mt-5 p-3 bg-body-secondary rounded-top">
                    <form wire:submit="updateAffilliate">
                    <div class="row gx-0 gx-md-3  align-items-center">
                        <div class="col-4 text-start d-flex flex-column align-items-start">
                            <p class="mb-1">
                                <button wire:click="changeaffStatus()"
                                    class="btn btn-outline px-1 border-0">
                                    @if ($affilliateStatus)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                    <span wire:loading wire:target="changeaffStatus()">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                                @lang("affillate"):</p>
                        </div>
                        <div class="col-4 text-center d-flex flex-column align-items-center">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">%</span>
                            <input type="number" class="form-control text-center" name="userid" id="userid"
                                aria-describedby="helpId" value="{{$settings->affilliate}}"
                                wire:model.defer="affilliate" />
                            </div>
                        </div>
                        <div class="col-4 text-end d-flex flex-column align-items-end">
                            <button type="submit" class="btn btn-success me-0 me-md-3  btn-sm">
                            <span wire:loading.remove wire:target="confirm"><i class="fas fa-plus me-1"></i>
                                @lang('Save')</span><span wire:loading wire:target="confirm"><i
                                    class="fas fa-spinner"></i> @lang('Saving..')</span>
                        </button>
                        </div>
                    </div>
                </form>
                </div>









                <div class="m-3 mt-5 p-3 bg-body-secondary rounded-top">
                    <form wire:submit="updatewithdraw">
                    <div class="row gx-0 gx-md-3  align-items-center">
                        <div class="col-4 text-start d-flex flex-column align-items-start">
                            <p class="mb-1">
                                <span
                                    class=" px-1 ">
                                        <i class="fas fa-check-circle text-success"></i>
                                </span>
                                @lang("Withdraw"):</p>
                        </div>
                        <div class="col-4 text-center d-flex flex-column align-items-center">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">%</span>
                            <input type="number" class="form-control text-center" name="userid" id="userid"
                                aria-describedby="helpId" value="{{$settings->extra_col}}"
                                wire:model.defer="withdraw" />
                            </div>
                        </div>
                        <div class="col-4 text-end d-flex flex-column align-items-end">
                            <button type="submit" class="btn btn-success me-0 me-md-3  btn-sm">
                            <span wire:loading.remove wire:target="confirm"><i class="fas fa-plus me-1"></i>
                                @lang('Save')</span><span wire:loading wire:target="confirm"><i
                                    class="fas fa-spinner"></i> @lang('Saving..')</span>
                        </button>
                        </div>
                    </div>
                </form>
                </div>







        </div>





    </div>


</div>
