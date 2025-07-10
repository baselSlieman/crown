<div class="container my-3">
    <div class="row justify-content-center g-2 gx-3">
        <div class="col col-md-12 px-3">



            <div class="row align-items-center">
                <h3 class="col-6 order-1 order-md-1 col-md-5 mb-0 text-danger"><i class="fas fa-qrcode"></i> @lang('SyriatelCash'): </h3>

                <div class="col-12 col-md-4 order-3 order-md-2 mt-3 mt-md-0 text-end text-md-center"><input
                        wire:model.live.debounce.500ms="search" type="search" class="form-control"
                        placeholder="@lang('Search').." /></div>
                <div class="col-6 col-md-3 order-2 order-md-3 mt-md-0 text-end"><button type="button"
                        class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="fas fa-plus"></i></button>
                    <button onclick="history.back()" class="btn btn-outline-danger me-2"><i
                            class="fas @if (App()->isLocale('en')) fa-arrow-right @else fa-arrow-left @endif"></i></button>
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

            @foreach ($results as $result)
                <div class="m-3 p-3 bg-body-secondary rounded-top">
                    <div class="row gx-0 gx-md-3">
                        <div class="col-4 text-start d-flex flex-column align-items-start">
                            <p class="mb-1"><a class="btn btn-sm btn-outline border-0 px-1" wire:navigate.hover href="{{ route('Syriatelcashedit', $result->id) }}"><i class="fa fa-edit"></i></a> @lang('code')</p>
                            <p class="mb-1">
                                @if ($result->type == 1)
                                    <i class="far fa-user"></i>
                                @else
                                    <i class="fas fa-user-lock"></i>
                                @endif {{ $result->code }}
                            </p>
                        </div>
                        <div class="col-4 text-center d-flex flex-column align-items-center">
                            <p class="mb-1">@lang('order')</p>
                            <p class="mb-1">{{ $result->codeOrder }}</p>
                        </div>
                        <div class="col-4 text-end d-flex flex-column align-items-end">
                            <p class="mb-1">@lang('phone')</p>
                            <p class="mb-1">{{ $result->phone }}</p>
                        </div>
                    </div>
                    <div class="row mt-3 gx-0 gx-md-3">
                        <div class="col-4 text-start d-flex flex-column align-items-start">
                            <p class="mb-1">@lang('user')</p>
                            <p class="mb-1">{{ $result->username }}</p>
                        </div>
                        <div class="col-4 text-center d-flex flex-column align-items-center">
                            <p class="mb-1">@lang('status')</p>
                            <p class="mb-1">
                                <button wire:click="changeStatus('{{ $result->id }}')"
                                    class="btn btn-outline border-0 py-0">
                                    @if ($result->status)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                    <span wire:loading wire:target="changeStatus('{{ $result->id }}')">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </p>
                        </div>
                        <div class="col-4 text-end d-flex flex-column align-items-end">
                            <p class="mb-1">@lang('balance')</p>
                            <p class="mb-1">
                                <button wire:click="refreshBalancef({{ $result->id }})"
                                    class="btn btn-outline border-0 p-0">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <span wire:loading wire:target="refreshBalancef({{ $result->id }})">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                                @if ($this->currentKeyb == $result->id)
                                    <span class="text-danger fw-bold">{{ $customerbalance }} @lang('sp')</span>
                                @endif

                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('New Syriatel Cash'):</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="addCode">
                    <div class="modal-body" id="modal-body">


                        <div class="mb-3">
                            <label for="type" class="form-label">@lang('type'):</label>
                            <select wire:model="type" name="type" class="form-select" id="type"
                                aria-label="Default select example">
                                <option value="1">@lang('user')</option>
                                <option value="2">@lang('Merchant')</option>
                            </select>
                            <small id="helpId" class="form-text text-muted">@lang('User/Marchant')</small>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="code" id="code"
                                aria-describedby="helpId" placeholder="@lang('code')" wire:model.defer="code" />
                            <small id="helpId" class="form-text text-muted">@lang('Transfer Code')</small>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="phone" id="phone"
                                aria-describedby="helpId" placeholder="@lang('phone')"
                                wire:model.defer="phone" />
                            <small id="helpId" class="form-text text-muted">@lang('phone number')</small>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="username" id="username"
                                aria-describedby="helpId" placeholder="@lang('username')"
                                wire:model.defer="username" />
                            <small id="helpId" class="form-text text-muted">@lang('number owner')</small>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="userid" id="userid"
                                aria-describedby="helpId" placeholder="@lang('userid')"
                                wire:model.defer="userid" />
                            <small id="helpId" class="form-text text-muted">@lang('user identifier')</small>
                        </div>

                        <div class="mb-3">
                            <textarea rows="3" class="form-control" name="userHistory" id="userHistory" aria-describedby="helpId"
                                placeholder="@lang('userHistory')" wire:model.defer="userHistory"></textarea>
                        </div>

                        <div class="mb-3">
                            <textarea class="form-control" rows="3" name="refreshBalance" id="refreshBalance" aria-describedby="helpId"
                                placeholder="@lang('refreshBalance')" wire:model.defer="refreshBalance"></textarea>

                        </div>

                        <div class="mb-3">
                            <textarea class="form-control" rows="3" name="MaarchentHistory" id="MaarchentHistory"
                                aria-describedby="helpId" placeholder="@lang('MaarchentHistory')" wire:model.defer="MaarchentHistory"></textarea>
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

                    <div class="modal-footer" id="mfooter">
                        <button type="submit" class="btn btn-success me-3">
                            <span wire:loading.remove wire:target="confirm"><i class="fas fa-plus me-1"></i>
                                @lang('add')</span><span wire:loading wire:target="confirm"><i
                                    class="fas fa-spinner"></i> @lang('adding..')</span>
                        </button><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
