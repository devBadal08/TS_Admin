<x-filament::widget>
    <x-filament::card class="w-full !p-4">
        <div class="flex flex-col gap-6">

            <!-- TOP SECTION -->
            <div class="flex items-center justify-between">
                <!-- Icon & Counts -->
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 shadow-sm">
                        <x-heroicon-o-document-text class="h-6 w-6 text-blue-500" style="color: #3b82f6;" />
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoices</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $total }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total invoices created</p>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="flex-shrink-0 max-w-[200px]">
                    <img src="{{ asset('images/invoice.png') }}" alt="Invoices" class="h-16 w-auto object-contain" />
                </div>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-gray-200 dark:border-gray-700"></div>

            <!-- BOTTOM SECTION: Paid / Bar / Unpaid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">

                <!-- Paid Block -->
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-500/10">
                        <x-heroicon-o-check-circle class="h-6 w-6 text-emerald-500" style="color: #10b981;" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-amber-600">Paid Invoices</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $paid }}</span>
                            <span class="text-xs font-semibold text-emerald-600">({{ $paidPercent }}%)</span>
                        </div>
                    </div>
                </div>

                <!-- Middle Progress Bar -->
                <div class="flex flex-col items-center justify-center border-x border-gray-100 dark:border-gray-800 px-4">

                    <div class="flex items-center gap-2">

                        <span class="text-xs font-medium text-gray-400">
                            Paid Amount
                        </span>

                        @if($showAmounts)

                            <button wire:click="$set('showAmounts', false)">
                                <x-heroicon-o-eye-slash class="w-5 h-5 text-gray-500"/>
                            </button>

                        @else

                            <button
                                x-on:click="$dispatch('open-modal', { id: 'unlockAmountModal' })">

                                <x-heroicon-o-eye class="w-5 h-5 text-gray-500"/>

                            </button>

                        @endif

                    </div>

                    @if($showAmounts)

                        <span class="text-lg font-bold text-emerald-600">
                            ₹{{ number_format($paidAmount,2) }}
                        </span>

                        <span class="text-[11px] text-gray-400">
                            Pending: ₹{{ number_format($unpaidAmount,2) }}
                        </span>

                    @else

                        <span class="text-lg font-bold text-gray-400">
                            ********
                        </span>

                        <span class="text-[11px] text-gray-400">
                            ********
                        </span>

                    @endif

                </div>

                <!-- Unpaid Block -->
                <div class="flex items-center justify-end gap-3 text-right">
                    <div>
                        <p class="text-xs font-semibold text-amber-600">Unpaid Invoices</p>
                        <div class="flex items-baseline justify-end gap-2">
                            <span class="text-xs font-semibold text-red-600">({{ $unpaidPercent }}%)</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $unpaid }}</span>
                        </div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/10">
                        <x-heroicon-o-x-circle class="h-6 w-6 text-red-500" style="color: #ef4444;" />
                    </div>
                </div>

            </div>
        </div>
    </x-filament::card>

    <!-- Password Modal -->
    <x-filament::modal id="unlockAmountModal">

        <x-slot name="heading">
            Enter Password
        </x-slot>

        <div class="space-y-4">
            <div x-data="{ show: false }" class="relative flex items-center">
                <input
                    :type="show ? 'text' : 'password'"
                    wire:model.defer="password"
                    placeholder="Enter password"
                    class="block w-full rounded-xl border border-gray-300 py-3 pl-4 pr-10 focus:border-primary-500 focus:ring-primary-500"
                >

                <button
                    type="button"
                    @click="show = !show"
                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); z-index: 10;"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <x-heroicon-o-eye x-show="!show" class="w-5 h-5" />
                    <x-heroicon-o-eye-slash x-show="show" class="w-5 h-5" />
                </button>
            </div>

            <x-filament::button
                wire:click="unlockAmounts"
                class="w-full">
                Unlock
            </x-filament::button>
        </div>

    </x-filament::modal>
</x-filament::widget>