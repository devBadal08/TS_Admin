@php
    $paidPercentage = $total > 0 ? round(($paid / $total) * 100, 2) : 0;
    $unpaidPercentage = $total > 0 ? round(($unpaid / $total) * 100, 2) : 0;
@endphp

<x-filament::card>
    <div class="grid grid-cols-1 gap-8 items-center">
        
        <!-- LEFT CONTENT BLOCK -->
        <div class="space-y-3">
            
            <!-- Top Section: Icon, Total & Right-Aligned Image -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                
                <!-- Icon & Counts -->
                <div class="flex items-center gap-4">
                    <div class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 shadow-md">
                        <x-heroicon-o-document-text class="h-6 w-6 text-blue-500" style="color: #3b82f6;" />
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoices</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $total }}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Total invoices</p>
                    </div>
                </div>

                <!-- Right Side Custom Image (Resized & Constrained) -->
                <div class="flex-shrink-0 max-w-[200px]">
                    <img src="{{ asset('images/invoice.png') }}" alt="Invoices" class="h-16 w-auto object-contain" />
                </div>

            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-blue-400/40 dark:border-blue-700/50 my-3"></div>

            <!-- Bottom Sub-Metrics: Paid & Unpaid -->
            <div class="flex flex-col sm:flex-row gap-6 sm:justify-between mt-4">

                <!-- Paid -->
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/20 shadow-sm">
                        <x-heroicon-o-check-circle class="h-6 w-6 text-green-600" style="color: #10B981;" />
                    </div>

                    <div>
                        <p class="text-xs font-semibold" style="color: #D97706;">
                            Paid Invoices
                        </p>

                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $paid }}
                        </p>

                        <p class="text-sm font-medium text-green-600">
                            {{ $paidPercentage }}%
                        </p>
                    </div>
                </div>

                <!-- Unpaid -->
                <div class="flex items-start gap-3 text-right">

                    <div>
                        <p class="text-xs font-semibold" style="color: #D97706;">
                            Unpaid Invoices
                        </p>

                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $unpaid }}
                        </p>

                        <p class="text-sm font-medium text-red-600">
                            {{ $unpaidPercentage }}%
                        </p>
                    </div>

                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-danger-50 dark:bg-danger-500/20 shadow-sm">
                        <x-heroicon-o-x-circle class="h-6 w-6 text-danger-600"/>
                    </div>

                </div>

            </div>
        </div>

    </div>
</x-filament::card>