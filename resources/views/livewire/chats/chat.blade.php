<div class="col col-md-12 px-3">


    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="fs-2 fw-medium mb-0"><i class="fab fa-telegram"></i> @lang('Chats'):</p>
        <div><input wire:model.live.debounce.500ms="search" type="search" class="form-control"
                placeholder="@lang('Search')..." /></div>
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
    <div class="table-responsive">
        <table class="table mt-3 table-bordered" style="vertical-align: middle;">
            <thead class="bg-dark-subtle">
                <tr>
                    <th class="text-center"><i class="fas fa-user-astronaut"></i> @lang('user_id')</th>
                    <th><i class="fas fa-user"></i> @lang('User')  <button  wire:click="sortBy()" class="btn btn-sm m-0 p-0"><i class="fas fa-caret-down"></i></button></th>
                    <th class="d-none d-md-table-cell"><i class="fas fa-info-circle"></i> @lang('info')</th>
                    <th class="d-none d-md-table-cell text-center"><i class="fas fa-cog"></i> @lang('option')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chats as $chat)
                    <tr wire:key="chat-{{ $chat->id }}">
                        <td class="text-center">{{ $chat->id }}</td>
                        <td>
                            <p class="mt-1"><i class="far fa-user"></i> @lang('username'): {{ $chat->username }}</p>
                            <p class="mt-1"><i class="fas fa-user"></i> @lang('fullname'): {{ $chat->first_name }}
                                {{ $chat->last_name }}</p>
                            <p class="mb-1 mt-3">
                                <i class="fas fa-wallet"></i> @lang('wallet'):
                                <button wire:click="getBalance('{{ $chat->id }}')"
                                    class="btn btn-sm btn-outline-danger border-0 py-0 px-1 fs-6 fw-bold">

                                    @if ($currentKeyb == $chat->id)
                                        <strong>{{ '' . number_format($balanceb, 0) }}</strong>
                                    @else
                                        <span wire:ignore>{{ '' . number_format($chat->balance, 0) }}</span>
                                    @endif
                                </button> NSP
                                <a class="btn btn-sm btn-outline-success py-0 ms-3 d-none d-md-inline-block"
                                    href="{{ route('charges.index', ['chat_id' => $chat->id]) }}"
                                    wire:navigate.hover><i class="fas fa-level-down-alt"></i></a>
                                <a class="btn btn-sm btn-outline-danger py-0 ms-3 d-none d-md-inline-block"
                                    href="{{ route('withdraws.index', ['chat_id' => $chat->id]) }}"
                                    wire:navigate.hover><i class="fas fa-level-up-alt"></i></a>

                                <button wire:click="getData('{{ $chat->id }}')"
                                    class="btn btn-outline-warning py-0 ms-1 ms-md-3 btn-sm d-none d-md-inline-block"><i
                                        class="fas fa-coins"></i></button>
                                <a class="btn btn-outline-primary py-0 ms-1 ms-md-3 btn-sm d-none d-md-inline-block"
                                href="{{ route('manualCharge', $chat) }}" wire:navigate.hover><i
                                    class="fas fa-plus"></i></a>
                                <span wire:loading wire:target="getData('{{ $chat->id }}')">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                                <span wire:loading wire:target="getBalance('{{ $chat->id }}')">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                                @if ($currentKey == $chat->id)
                                    <span class="text-danger d-block mt-3"><i class="fas fa-money-bill-alt"></i>
                                        @lang('ichancy balance'): {{ $value }}</span>
                                @endif

                            </p>
                        </td>






                        <td class="d-none d-md-table-cell">
                            <p class="d-none d-md-block mt-1"><i class="far fa-calendar-alt"></i>
                                {{ $chat->created_at }}</p>
                            <p class="mt-1">
                            @php
                                $ichancies = json_decode($chat->ichancies, true);
                            @endphp
                            <i class="fas fa-bolt me-1"></i> @lang('ichancy'):
                            @if (isset($ichancies[0]['e_username']))

                                <a style="color:rgba(108, 117, 125, 1) !important;text-decoration:none" wire:navigate.hover href="{{ route('ichancies.ichancy_transaction',['ichancyid' => $ichancies[0]['id']]) }}">{{ $ichancies[0]['e_username'] }}</a>
                            @else
                                @lang("None")
                            @endif
                            </p>
                            @if ($chat->info)
                                <p class="mt-1"><i class="fas fa-info-circle"></i> {{ $chat->info }}</p>
                            @endif
                            @if ($chat->affiliate_code)
                                <p class="mb-1 mt-3"><i class="fas fa-users"></i> {{ $chat->affiliate_code }}</p>
                            @endif
                        </td>

                        <td class="d-none d-md-table-cell">
                            <div class="text-center mt-2">
                                <div>
                                    <a href="{{ route('chats.createMessage', $chat) }}" class="btn btn-primary"
                                        wire:navigate.hover><i class="fab fa-telegram-plane"></i></a>
                                    <a href="{{ route('chats.edit', $chat) }}" class="btn btn-success"
                                        wire:navigate.hover><i class="fas fa-marker"></i></a>
                                </div>
                                <div class="mt-1">
                                    <a href="{{ route('chats.userAffiliates', $chat) }}" class="btn btn-danger"
                                        wire:navigate.hover><i class="fas fa-percent"></i></a>
                                    <a href="{{ route('chats.usergifts', $chat) }}" class="btn btn-warning"
                                        wire:navigate.hover><i class="fas fa-gift"></i></a>
                                </div>
                            </div>
                        </td>
                        {{-- <td class="d-none d-md-table-cell">
                            <div class="text-center mt-2">
                                <div class="container-relative mx-auto">
                                 <div>
                                    <a href="{{ route('chats.createMessage', $chat) }}" class="btn btn-primary"
                                        wire:navigate.hover><i class="fab fa-telegram-plane"></i></a>
                                    <a href="{{ route('chats.edit', $chat) }}" class="btn btn-success"
                                        wire:navigate.hover><i class="fas fa-marker"></i></a>
                                </div>
                                <div class="mt-1">
                                    <a href="{{ route('chats.userAffiliates', $chat) }}" class="btn btn-danger"
                                        wire:navigate.hover><i class="fas fa-percent"></i></a>
                                    <a href="{{ route('chats.usergifts', $chat) }}" class="btn btn-warning"
                                        wire:navigate.hover><i class="fas fa-gift"></i></a>
                                </div>
                                    <a href="{{ route('chats.usergifts', $chat) }}" class="btn btn-secondary rounded-circle  btn-sm overlay-center"
                                    wire:navigate.hover><i class="fas fa-rocket"></i></a>
                                </div>
                            </div>
                        </td> --}}
                    </tr>
                    <tr class="d-table-row d-md-none">
                        <td colspan="2">
                            <p class="mt-1"><i class="far fa-calendar-alt me-2"></i>@lang('join'):
                                {{ $chat->created_at }}</p>
                            @if ($chat->affiliate_code)
                                <p class="mt-1"><i class="fas fa-users me-2"></i>@lang('affiliate_agent'):
                                    {{ $chat->affiliate_code }}</p>
                            @endif
                            @if ($chat->info)
                                <p class="mt-1"><i class="fas fa-info-circle me-2"></i>@lang('info'):
                                    {{ $chat->info }}</p>
                            @endif
                            <p class="mt-1 mb-1">
                            @php
                                $ichancies = json_decode($chat->ichancies, true);
                            @endphp
                            <i class="fas fa-bolt me-1"></i> @lang('ichancy'):
                            @if (isset($ichancies[0]['e_username']))
                                <a style="color:rgba(108, 117, 125, 1) !important;text-decoration:none" wire:navigate.hover href="{{ route('ichancies.ichancy_transaction',['ichancyid' => $ichancies[0]['id']]) }}">{{ $ichancies[0]['e_username'] }}</a>
                            @else
                                @lang("None")
                            @endif
                            </p>
                        </td>
                    </tr>
                    <tr class="d-table-row d-md-none">
                        <td colspan="2" class="text-center bg-body-secondary">
                            <a href="{{ route('chats.createMessage', $chat) }}" class="btn btn-primary btn-sm me-2"
                                wire:navigate.hover><i class="fab fa-telegram-plane"></i></a>
                            <a href="{{ route('chats.edit', $chat) }}" class="btn btn-success btn-sm mx-2"
                                wire:navigate.hover><i class="fas fa-marker"></i></a>
                            <a href="{{ route('chats.userAffiliates', $chat) }}" class="btn btn-danger btn-sm mx-2"
                                wire:navigate.hover><i class="fas fa-percent"></i></a>
                            <a href="{{ route('chats.usergifts', $chat) }}" class="btn btn-warning btn-sm mx-2"
                                wire:navigate.hover><i class="fas fa-gift"></i></a>
                            <a class="btn btn-success btn-sm mx-2"
                                href="{{ route('charges.index', ['chat_id' => $chat->id]) }}" wire:navigate.hover><i
                                    class="fas fa-level-down-alt"></i></a>
                            <a class="btn  btn-danger btn-sm mx-2"
                                href="{{ route('withdraws.index', ['chat_id' => $chat->id]) }}" wire:navigate.hover><i
                                    class="fas fa-level-up-alt"></i></a>

                            <div class="mt-2">
                            <button wire:click="getData('{{ $chat->id }}')"
                                class="btn  btn-primary btn-sm mx-2"><i class="fas fa-coins"></i></button>

                                <a class="btn  btn-info btn-sm mx-2"
                                href="{{ route('manualCharge', $chat) }}" wire:navigate.hover><i
                                    class="fas fa-plus"></i></a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="messageModal{{ $chat->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Message</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="post" action="{{ route('chats.store', $chat) }}">
                                    <div class="modal-body">
                                        @csrf
                                        <input type="text" name="message" class="form-control"
                                            placeholder="type here..." />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <h3>@lang('No chats')</h3>
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{ $chats->links() }}
</div>
