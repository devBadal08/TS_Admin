@php
    $amount = round($invoice->amount, 2);

    $rupees = floor($amount);
    $paise = round(($amount - $rupees) * 100);

    $formatter = \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT);

    $words = ucwords($formatter->format($rupees)) . ' Indian Rupees';

    if ($paise > 0) {
        $words .= ' And ' . ucwords($formatter->format($paise)) . ' Paise';
    }

    $words .= ' Only';
@endphp
<!DOCTYPE html> 
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>

    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #000;
            line-height: 1.4;
        }

        /* Top Bar Segment Outside/Above Main Frame Box */
        .top-label-bar {
            width: 100%;
            margin-bottom: 5px;
        }

        .top-label-bar td {
            padding: 0;
            border: none;
        }

        .invoice-type-title {
            font-size: 16px;
            font-weight: bold;
            color: #1b75bc;
            letter-spacing: 1.5px;
            text-align: center;
        }

        .invoice-container {
            border: 1px solid #1b75bc; /* Primary Logo Blue */
            width: 100%;
        }

        /* Generic table resets for strict framing layout */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        .grid-table td, .grid-table th {
            padding: 5px 8px;
            vertical-align: top;
            border: 1px solid #1b75bc; /* Balanced with primary blue grid lines */
        }

        .section-heading {
            background-color: #f0f6fb; /* Soft Brand Blue Tint */
            color: #1b75bc;
            font-size: 10px;
            text-align: center;
            font-weight: bold;
            padding: 4px !important;
        }

        /* Item Grid Elements */
        .items-table th {
            background-color: #ffffff;
            color: #1b75bc;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Dynamic helper to build consistent heights for item rows */
        .item-row td {
            border-top: none;
            border-bottom: none;
            padding: 2px 8px !important;
            line-height: 1.1;
        }

        .tax-row td {
            border-top: none;
            border-bottom: none;
            padding: 50px 8px 2px 8px !important;
        }

        .tax-row2 td {
            border-top: none;
            border-bottom: none;
            padding: 2px 8px 20px 8px !important;
        }

        .tax-payable-text {
            font-weight: bold;
            font-style: italic;
            text-align: right;
            padding-right: 15px !important;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f0f6fb;
            color: #1b75bc;
        }

        /* Footer Conditions and terms block */
        .terms-box {
            font-size: 10px;
            line-height: 1.3;
        }

        .computer-generated-note {
            font-size: 10px;
            color: #555;
            padding: 5px 8px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- TOPMOST META HEADER BLOCK (Outside Main Border Frame) -->
    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <td class="invoice-type-title" style="text-align:center;">
                INVOICE
            </td>
        </tr>
    </table>

<div>

    <!-- MAIN STRUCTURAL THREE-COLUMN BLOCK -->
    <table class="grid-table">
        <tr>
            <!-- Column 1: Provider / Seller Details -->
            <td width="38%" style="padding: 6px;">
                <img src="{{ public_path('images/logo.png') }}" height="40" style="margin-bottom: 4px;"><br>
                <strong style="font-size: 15px; color: #1b75bc;">TECHSTROTA</strong><br>
                <span style="font-size: 10px; color: #444; line-height: 1.3;">
                    {{ $invoice->seller['address'] ?? 'Plot No A 64, Road No 21, Wagle Indl Estate, Mumbai, Maharashtra - 400604' }}<br>
                    <strong>GSTIN/UIN:</strong> {{ $invoice->seller['gstno'] ?? '24AAVFT0941Q1ZF' }}<br>
                    <strong>State Name:</strong> {{ $invoice->seller['state_name'] ?? 'Gujarat' }}, <strong>Code :</strong> {{ $invoice->seller['state_code'] ?? '24' }}<br>
                    <strong>Email:</strong> info@techstrota.com<br>
                    <strong>Web</strong> : www.techstrota.com
                </span>
            </td>

            <!-- Column 2: Consignee / Buyer Details -->
            <td width="34%" style="padding: 0;">
                <table class="grid-table" style="border: none;">
                    <tr><td class="section-heading" style="border-top:none; border-left:none; border-right:none; text-align: left;">Buyer (Bill to)</td></tr>
                    <tr>
                        <td style="border: none; font-size: 10px; line-height: 1.4;">
                            <strong>Name:</strong> {{ $invoice->customer['name'] ?? '-' }}<br>
                            <strong>Address:</strong> {{ $invoice->customer['address'] ?? '-' }}<br>
                            <strong>GSTIN/UIN:</strong> {{ $invoice->customer['gstno'] ?? '-' }}<br>
                            <strong>State Name:</strong> {{ $invoice->customer['state_name'] ?? '-' }}<br>
                            <strong>State Code:</strong> {{ $invoice->customer['state_code'] ?? '-' }}
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Column 3: Invoice Processing Metadata metrics -->
            <td width="28%" style="padding: 0;">
                <table class="grid-table" style="border: none;">
                    <tr>
                        <td style="border-top:none; border-left:none; border-right:none; font-size: 10px;">
                            <span style="color: #555;">Invoice No.</span><br>
                            <strong>{{ $invoice->invoice_no }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left:none; border-right:none; font-size: 10px;">
                            <span style="color: #555;">Invoice Date</span><br>
                            <strong>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- MAIN LINE ITEMS GRID -->
    <table class="grid-table items-table">
        <thead>
            <tr>
                <th width="7%">Sr. No.</th>
                <th width="48%">Description</th>
                <th width="12%">HSN / SAC</th>
                <th width="10%">Qty</th>
                <th width="11%">Rate</th>
                <th width="12%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; $total_qty = 0; @endphp
            @foreach($invoice->items as $index => $item)
                @php
                    $line = ($item['qty'] ?? 0) * ($item['rate'] ?? 0);
                    $subtotal += $line;
                    $total_qty += ($item['qty'] ?? 0);
                @endphp
                <tr class="item-row">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item['description'] }}</strong></td>
                    <td class="text-center">{{ $item['hsn_sac'] ?? '8302' }}</td>
                    <td class="text-center">{{ $item['qty'] }} NOS</td>
                    <td class="text-right">{{ number_format($item['rate'], 3) }}</td>
                    <td class="text-right">{{ number_format($line, 3) }}</td>
                </tr>
            @endforeach

            <!-- Padding rows to mirror precise layout structure safely -->
            @for ($i = count($invoice->items); $i < 3; $i++)
                <tr class="item-row">
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endfor

            <!-- Dynamic Tax Processing matching image_ba4d48.jpg -->
            @php 
                $cgstAmount = 0;
                $sgstAmount = 0;
                $igstAmount = 0;
                
                if($invoice->gst_type === 'cgst_sgst') {
                    $cgstAmount = ($subtotal * ($invoice->gst_rate['cgst'] ?? 0)) / 100;
                    $sgstAmount = ($subtotal * ($invoice->gst_rate['sgst'] ?? 0)) / 100;
                } elseif($invoice->gst_type === 'igst') {
                    $igstAmount = ($subtotal * ($invoice->gst_rate['igst'] ?? 0)) / 100;
                }

                $rawTotal = $subtotal + $cgstAmount + $sgstAmount + $igstAmount;
                $finalTotal = round($rawTotal);
                $roundOff = $finalTotal - $rawTotal;
            @endphp

            @if($invoice->gst_type === 'cgst_sgst')
                <!-- CGST Line Row -->
                <tr class="tax-row">
                    <td></td>
                    <td class="tax-payable-text">CGST PAYABLE</td>
                    <td></td><td></td><td></td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($cgstAmount, 3) }}</td>
                </tr>
                <!-- SGST Line Row -->
                <tr class="item-row">
                    <td></td>
                    <td class="tax-payable-text">SGST PAYABLE</td>
                    <td></td><td></td><td></td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($sgstAmount, 3) }}</td>
                </tr>
            @elseif($invoice->gst_type === 'igst')

                <tr class="item-row">
                    <td></td>
                    <td class="tax-payable-text">IGST PAYABLE</td>
                    <td></td><td></td><td></td>
                    <td class="text-right">{{ number_format($igstAmount, 3) }}</td>
                </tr>

            @endif

            <!-- Round Off Row -->
            <tr class="tax-row2">
                <td></td>
                <td class="tax-payable-text">Round Off</td>
                <td></td><td></td><td></td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($roundOff, 3) }}</td>
            </tr>

            <!-- Content Base Frame Summary Border -->
            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-center">{{ $total_qty }} NOS</td>
                <td></td>
                <td class="text-right">₹ {{ number_format($finalTotal, 3) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTAL IN WORDS STRIP -->
    <table class="grid-table">
        <tr>
            <td>
                <span style="font-size: 9px; color: #555;">Total Amount Chargeable (in words)</span><br>
                <strong style="color: #1b75bc;">
                    {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($finalTotal)) }} Indian Rupees Only
                </strong>
                <span style="float: right; font-size: 9px; font-weight: bold; color: #555;">E. & O.E</span>
            </td>
        </tr>
    </table>

    <!-- TAX BREAKDOWN TABLE MODULE -->
    @if($invoice->gst_type === 'cgst_sgst')
        <table class="grid-table text-center">
            <thead>
                <tr style="background-color: #f0f6fb; font-weight: bold; color: #1b75bc;">
                    <td rowspan="2" width="30%">HSN/SAC</td>
                    <td rowspan="2" width="16%">Taxable Value</td>
                    <td colspan="2" width="22%">CGST</td>
                    <td colspan="2" width="22%">SGST</td>
                    <td rowspan="2" width="10%">Total Tax Amount</td>
                </tr>
                <tr style="background-color: #f0f6fb; font-weight: bold; color: #1b75bc;">
                    <td>Rate</td>
                    <td>Amount</td>
                    <td>Rate</td>
                    <td>Amount</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->items[0]['hsn_sac'] ?? '85258020' }}</td>
                    <td class="text-right">{{ number_format($subtotal, 3) }}</td>
                    <td>{{ $invoice->gst_rate['cgst'] ?? 9 }}%</td>
                    <td class="text-right">{{ number_format($cgstAmount, 3) }}</td>
                    <td>{{ $invoice->gst_rate['sgst'] ?? 9 }}%</td>
                    <td class="text-right">{{ number_format($sgstAmount, 3) }}</td>
                    <td class="text-right">{{ number_format($cgstAmount + $sgstAmount, 3) }}</td>
                </tr>
                <tr style="font-weight: bold; background-color: #f0f6fb; color: #1b75bc;">
                    <td class="text-right">Total</td>
                    <td class="text-right">{{ number_format($subtotal, 3) }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($cgstAmount, 3) }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($sgstAmount, 3) }}</td>
                    <td class="text-right">{{ number_format($cgstAmount + $sgstAmount, 3) }}</td>
                </tr>
            </tbody>
        </table>

    @elseif($invoice->gst_type === 'igst')
            <table class="grid-table text-center">
                <thead>
                    <tr style="background-color:#f0f6fb; font-weight:bold; color:#1b75bc;">
                        <td width="30%">HSN/SAC</td>
                        <td width="25%">Taxable Value</td>
                        <td width="15%">IGST Rate</td>
                        <td width="20%">IGST Amount</td>
                        <td width="10%">Total Tax</td>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>{{ $invoice->items[0]['hsn_sac'] ?? '' }}</td>
                        <td class="text-right">{{ number_format($subtotal,3) }}</td>
                        <td>{{ $invoice->gst_rate['igst'] ?? 18 }}%</td>
                        <td class="text-right">{{ number_format($igstAmount,3) }}</td>
                        <td class="text-right">{{ number_format($igstAmount,3) }}</td>
                    </tr>

                    <tr style="font-weight:bold; background:#f0f6fb; color:#1b75bc;">
                        <td class="text-right">Total</td>
                        <td class="text-right">{{ number_format($subtotal,3) }}</td>
                        <td></td>
                        <td class="text-right">{{ number_format($igstAmount,3) }}</td>
                        <td class="text-right">{{ number_format($igstAmount,3) }}</td>
                    </tr>
                </tbody>
            </table>
    @endif

    <!-- TAX IN WORDS STRIP -->
    @if($invoice->gst_type !== 'no_gst')
        <table class="grid-table">
            <tr>
                <td style="font-weight: bold; font-size: 10px; color: #333;">
                    Tax Amount (in words) :
                    <span style="color: #1b75bc;">

                        @php
                            $taxAmount = $invoice->gst_type === 'igst'
                                ? $igstAmount
                                : ($cgstAmount + $sgstAmount);
                        @endphp

                        {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format(floor($taxAmount))) }}
                        Indian Rupees

                        @if(round(($taxAmount - floor($taxAmount)) * 100) > 0)
                            And
                            {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format(round(($taxAmount - floor($taxAmount)) * 100))) }}
                            Paise
                        @endif

                        Only
                    </span>
                </td>
            </tr>
        </table>
    @endif

    <!-- BANK DETAILS, TERMS AND SIGNATURE GRID MODULE -->
    <table class="grid-table">
        <tr>
            <!-- Left Data Column: Bank Details & Terms -->
            <td width="55%" style="padding: 0; border-right: 1px solid #1b75bc;">
                <table class="grid-table" style="border: none;">
                    <tr>
                        <td colspan="2" class="section-heading" style="border-top: none; border-left: none; border-right: none; text-align: left;">Bank Details</td>
                    </tr>
                    <tr>
                        <td width="55%" style="border: none; font-size: 10px;">
                            <strong>Company's PAN</strong>: AAVFT0941Q <br>
                            <strong>Branch</strong>: {{ $invoice->bank_details['branch'] ?? '' }}<br>
                            <strong>Account No.</strong>: {{ $invoice->bank_details['account'] ?? '' }}<br>
                            <strong>IFSC</strong>: {{ $invoice->bank_details['ifsc'] ?? 'ICIC045F' }}
                        </td>
                        <td width="45%" style="border: none; text-align: center; vertical-align: middle;">
                            <img src="{{ public_path('images/qr.jpeg') }}" width="130" height="130" style="display:block; margin: 0 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="section-heading" style="border-left: none; border-right: none; text-align: left;">Terms and Conditions</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="terms-box" style="border: none;">
                            {{ $invoice->terms }}
                            <br>
                            <strong>Declaration:</strong>
                            {{ $invoice->declaration }}

                        </td>
                    </tr>
                </table>
            </td>

            <!-- Right Data Column: Authorised Signatory Area -->
            <td width="45%" style="padding: 0; position: relative;">
                <div style="font-size: 9px; text-align: center; padding: 4px; border-bottom: 1px solid #1b75bc; font-style: italic; color: #444;">
                    Certified that the particulars given above are true and correct.
                </div>
                <div style="text-align: center; font-weight: bold; margin-top: 10px; color: #1b75bc;">
                    For TECHSTROTA
                </div>

                <div style="position: absolute; bottom: 0; left: 0; right: 0; border-top: 1px solid #1b75bc; text-align: center; font-weight: bold; padding: 5px; background-color: #f0f6fb; color: #1b75bc;">
                    Authorised Signatory
                </div>
            </td>
        </tr>
    </table>

    <div class="computer-generated-note">
        This is a Computer Generated Invoice.
    </div>

</div>

</body>
</html>