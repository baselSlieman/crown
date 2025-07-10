<div class="container my-3">
    <div class="row justify-content-center g-2 gx-3">
        <div class="col col-md-12 px-3">



            <div class="row align-items-center">
                <h3 class="col-10"><i class="fas fa-edit"></i> @lang('Edit SyriatelCash'):</h3>
                <div class="col-2 text-end">
                    <button onclick="history.back()" class="btn btn-outline-danger me-2"><i
                            class="fas @if (App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
                </div>
            </div>

            <div class="mb-5">
                    <form wire:submit="update">
                    <div class="modal-body" id="modal-body">


                        <div class="mb-3">
                            <label for="type" class="form-label">@lang('type'):</label>
                            <select wire:model="codesy.type" name="type" class="form-select" id="type"
                                aria-label="Default select example">
                                <option @selected($codesy["type"] == 1) value="1">@lang('user')</option>
                                <option @selected($codesy["type"] == 2) value="2">@lang('Merchant')</option>
                            </select>
                            <small id="helpId" class="form-text text-muted">@lang('User/Marchant')</small>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="codeOrder" id="codeOrder"
                                aria-describedby="helpId" wire:model.defer="codesy.codeOrder" />
                            <small id="helpId" class="form-text text-muted">@lang('order')</small>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="code" id="code"
                                aria-describedby="helpId" wire:model.defer="codesy.code" />
                            <small id="helpId" class="form-text text-muted">@lang('Transfer Code')</small>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="phone" id="phone"
                                aria-describedby="helpId" placeholder="@lang('phone')"
                                wire:model.defer="codesy.phone" />
                            <small id="helpId" class="form-text text-muted">@lang('phone number')</small>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="username" id="username"
                                aria-describedby="helpId" placeholder="@lang('username')"
                                wire:model.defer="codesy.username" />
                            <small id="helpId" class="form-text text-muted">@lang('number owner')</small>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="userid" id="userid"
                                aria-describedby="helpId" placeholder="@lang('userid')"
                                wire:model.defer="codesy.userid" />
                            <small id="helpId" class="form-text text-muted">@lang('user identifier')</small>
                        </div>

                        <div class="mb-3">
                            <textarea rows="3" class="form-control" name="userHistory" id="userHistory" aria-describedby="helpId"
                                placeholder="@lang('userHistory')" wire:model.defer="codesy.userHistory"></textarea>
                                <small id="helpId" class="form-text text-muted">userHistory</small>
                        </div>

                        <div class="mb-3">
                            <textarea class="form-control" rows="3" name="refreshBalance" id="refreshBalance" aria-describedby="helpId"
                                placeholder="@lang('refreshBalance')" wire:model.defer="codesy.refreshBalance"></textarea>
                            <small id="helpId" class="form-text text-muted">refreshBalance</small>
                        </div>

                        <div class="mb-3">
                            <textarea class="form-control" rows="3" name="MaarchentHistory" id="MaarchentHistory"
                                aria-describedby="helpId" placeholder="@lang('MaarchentHistory')" wire:model.defer="codesy.MaarchentHistory"></textarea>
                                <small id="helpId" class="form-text text-muted">MaarchentHistory</small>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger  alert-dismissible">

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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @elseif (session('danger'))
                            <div class="alert alert-danger mt-3 alert-dismissible">
                                {{ session('danger') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <div>


<div class="d-flex justify-content-between d-md-block">
        <button type="submit" class="btn btn-success me-3"><span wire:loading.remove wire:target="save"><i class="fas fa-check"></i> @lang('Save')</span><span wire:loading wire:target="save"><i class="fas fa-spinner"></i> @lang('Saving..')</span></button> |
        <button type="button" class="btn btn-primary mx-3"  onclick="history.back()">@lang('Back') <i class="fas fa-arrow-right"></i></button> |
        <button type="button" class="btn btn-danger ms-3" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="far fa-trash-alt"></i> @lang('Delete')</button>
    </div>
                    </div>
                </form>

                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">@lang('Delete Code'):</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <p>@lang('Are you sure you want to delete this item?')</p>
                    </div>
                    <div class="modal-footer" id="mfooter">
                        <form class="d-inline-block" wire:submit="delete">
                            <div class="text-success pe-2" wire:loading  wire:target="delete">
                                <i class="fas fa-spinner"></i> @lang('Loading...')
                            </div>
                            <button type="submit" data-bs-dismiss="modal" class="btn btn-danger me-3"><i
                                    class="fas fa-trash me-1"></i>@lang('Confirm')</button><button type="button"
                                class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button></form>

                    </div>
                </div>
            </div>
        </div>
            </div>

        </div>
    </div>
</div>
