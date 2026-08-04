@php
    use App\Models\Invoice;

    $paid = Invoice::where('payment_status', true)->count();
    $unpaid = Invoice::where('payment_status', false)->count();
    $total = $paid + $unpaid;

    $paidPercentage = $total > 0 ? round(($paid / $total) * 100, 2) : 0;
    $unpaidPercentage = $total > 0 ? round(($unpaid / $total) * 100, 2) : 0;
@endphp

<x-filament::card>
    <div class="space-y-2">
        <h2 class="text-base font-semibold text-gray-950 dark:text-white">
            Invoice Status
        </h2>

        <div class="border-t border-gray-200 dark:border-gray-800 my-2"></div>

        <!-- 3-COLUMN FLEX LAYOUT -->
        <div class="flex items-center justify-between gap-2 py-2">

            <!-- LEFT: Paid Stats -->
            <div class="flex items-center gap-2 text-left">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/20 shadow-sm flex-shrink-0">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-green-600" style="color: #10B981;" />
                </div>
                <div>
                    <p class="text-xs font-semibold" style="color: #10B981;">Paid Invoices</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $paid }}</p>
                    <p class="text-xs font-medium text-green-600">{{ $paidPercentage }}%</p>
                </div>
            </div>

            <!-- CENTER: Native Filament Chart Container -->
            <div 
                x-data="chart({
                    cachedData: @js($this->getCachedData()),
                    options: @js($this->getOptions()),
                    type: @js($this->getType()),
                })"
                class="relative w-28 h-28 flex-shrink-0"
            >
                <canvas x-ref="canvas" class="w-full h-full"></canvas>
            </div>

            <!-- RIGHT: Unpaid Stats -->
            <div class="flex items-center gap-2 text-right">
                <div>
                    <p class="text-xs font-semibold" style="color: #F59E0B;">Unpaid Invoices</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $unpaid }}</p>
                    <p class="text-xs font-medium text-amber-600">{{ $unpaidPercentage }}%</p>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-danger-50 dark:bg-danger-500/20 shadow-sm flex-shrink-0">
                    <x-heroicon-o-x-circle class="h-5 w-5 text-danger-600" />
                </div>
            </div>

        </div>
    </div>
</x-filament::card>