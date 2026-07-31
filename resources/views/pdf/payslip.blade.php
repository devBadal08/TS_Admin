<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $payslip->salary_month?->format('F Y') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }

        .header-table td {
            vertical-align: middle; /* Keeps logo, text, and title aligned horizontally */
        }

        .logo-cell {
            width: 15%;
        }

        .logo-img {
            max-width: 150px;
            height: auto;
            display: block;
        }

        .company-cell {
            width: 45%;
            padding-left: 10px;
        }

        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            line-height: 1.2;
        }

        .company-details {
            font-size: 13px;
            color: #555555;
            line-height: 1.4;
        }

        .payslip-cell {
            width: 40%;
            text-align: right;
        }

        .payslip-title {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #F59E0B; /* Amber-500 */
            text-transform: uppercase;
        }

        .payslip-subtitle {
            margin: 4px 0 0 0;
            font-size: 14px;
            font-weight: normal;
            color: #666666;
        }

        .divider {
            border-bottom: 1px solid #e2e8f0;
            margin: 10px 0;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-box {
            background-color: #FFFBEB; /* Amber-50 Light Background */
            border: 1px solid #FCD34D; /* Amber-300 Border */
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .net-pay-amount {
            font-size: 20px;
            font-weight: bold;
            color: #3B82F6; /* Blue-500 */
        }

        .net-pay-label {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .info-label {
            color: #666;
            font-weight: normal;
            width: 35%;
        }

        .info-value {
            color: #111;
            font-weight: bold;
        }

        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .breakdown-table th {
            background-color: #3B82F6; /* Blue-500 Background */
            color: #ffffff; /* White Text */
            font-size: 11px;
            text-transform: uppercase;
            padding: 8px 12px;
            border-bottom: 1px solid #3B82F6;
            text-align: left;
        }

        .breakdown-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #EFF6FF; /* Blue-50 Light Background */
            font-weight: bold;
            color: #1D4ED8; /* Blue-700 */
        }

        .total-row td {
            border-top: 1px solid #BFDBFE; /* Blue-200 Border */
            border-bottom: none;
        }

        .net-payable-container {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 15px;
            background-color: #ffffff;
        }

        .net-payable-table {
            width: 100%;
            border-collapse: collapse;
        }

        .net-payable-table td {
            padding: 12px;
        }

        .payable-box {
            background-color: #FFFBEB; /* Amber-50 Light Background */
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #F59E0B; /* Amber-500 */
            border-left: 1px solid #e2e8f0;
            width: 30%;
        }

        .words-section {
            margin-top: 15px;
            text-align: right;
            font-size: 11px;
            color: #444;
        }

        .footer-note {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-label {
            color: #666;
            white-space: nowrap;
        }
        .info-value {
            font-weight: bold;
            color: #111;
            word-break: break-all;
            padding-right: 15px; /* Adds space before the 2nd column starts */
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo.png') }}" 
                    alt="Company Logo" 
                    class="logo-img">
            </td>
            <td class="company-cell">
                <div class="company-title">TechStrota</div>
                <div class="company-details">503, Sterling Centre, R C Dutt Road, Alkapuri,<br/>Vadodara, Gujarat 390007 India</div>
            </td>
            <td class="payslip-cell">
                <h2 class="payslip-title">Payslip For the Month</h2>
                <h3 class="payslip-subtitle">{{ $payslip->salary_month?->format('F Y') }}</h3>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Employee Summary Section -->
     <h4 style="margin:15px 0 8px; font-size:13px; color:#555; text-transform:uppercase;">
        Employee Details
    </h4>
    <table class="summary-table">
        <tr>
            <td style="width: 65%; vertical-align: top;">
                <table style="width: 100%; border-spacing: 0 4px;">
                    <tr>
                            <td style="width:150px;" class="info-label">Employee Name</td>
                            <td style="width:15px;">:</td>
                            <td class="info-value">{{ $employee->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="width:150px;" class="info-label">Designation</td>
                        <td style="width:15px;">:</td>
                        <td class="info-value">{{ $employee->designation ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="width:150px;" class="info-label">Employee ID</td>
                        <td style="width:15px;">:</td>
                        <td class="info-value">{{ $employee->employee_code ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="width:150px;" class="info-label">Date of Joining</td>
                        <td style="width:15px;">:</td>
                        <td class="info-value">{{ $employee->joining_date ? $employee->joining_date->format('d-m-Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 35%; vertical-align: top; padding-left: 15px;">
                <div class="summary-box">
                    <div class="net-pay-amount">₹ {{ number_format($payslip->net_salary ?? 0, 2) }}</div>
                    <div class="net-pay-label">Employee Net Pay</div>
                    <div class="divider" style="margin: 8px 0; border-bottom: 1px solid #FCD34D;"></div>
                    <table style="width: 100%; font-size: 10px; color: #555;">
                        <tr>
                            <td>Paid Days : <strong>{{ $payslip->paid_days ?? 0 }}</strong></td>
                            <td style="text-align: right;">LOP Days : <strong>{{ $payslip->lop ?? 0 }}</strong></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <hr style="border:0; border-top:1px dashed #cbd5e1; margin:12px 0 15px;">

    <!-- Pan, Bank Acc no and other details Table -->
   <table class="info-table" style="width:100%; table-layout:fixed; border-collapse:collapse;">
        <tr>
            <!-- Left Side (50% total) -->
            <td style="width:18%;" class="info-label">PAN Number</td>
            <td style="width:2%;">:</td>
            <td style="width:30%; word-break:break-all; padding-right:10px;" class="info-value">
                {{ $employee->pan ?? 'N/A' }}
            </td>

            <!-- Right Side (50% total) -->
            <td style="width:18%;" class="info-label">Bank Account No</td>
            <td style="width:2%;">:</td>
            <td style="width:30%; word-break:break-all;" class="info-value">
                {{ data_get($employee->bank_details, 'Bank Account No', 'N/A') }}
            </td>
        </tr>

        <tr>
            <td class="info-label">PF A/C Number</td>
            <td>:</td>
            <td class="info-value" style="word-break:break-all; padding-right:10px;">
                {{ $employee->pf_number ?? 'N/A' }}
            </td>

            <td class="info-label">UAN</td>
            <td>:</td>
            <td class="info-value" style="word-break:break-all;">
                {{ $employee->uan ?? 'N/A' }}
            </td>
        </tr>

        @php
            $bankDetails = $employee->bank_details ?? [];
            // Remove Bank Account No because it's already displayed above
            unset($bankDetails['Bank Account No']);
        @endphp

        @foreach($bankDetails as $key => $value)
        <tr>
            <td class="info-label">{{ $key }}</td>
            <td>:</td>
            <td class="info-value" style="word-break:break-all; padding-right:10px;">
                {{ $value ?: 'N/A' }}
            </td>

            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
    </table>

    <!-- Earnings & Deductions Breakdown Table -->
    <table class="breakdown-table">
        <thead>
            <tr>
                <th style="width: 35%;">Earnings</th>
                <th style="width: 15%; text-align: right;">Amount</th>
                <th style="width: 35%; border-left: 1px solid #BFDBFE;">Deductions</th>
                <th style="width: 15%; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic</td>
                <td class="text-right">₹{{ number_format($payslip->basic ?? 0, 2) }}</td>
                <td style="border-left: 1px solid #f1f5f9;">PF</td>
                <td class="text-right">₹{{ number_format($payslip->pf ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>HRA</td>
                <td class="text-right">₹{{ number_format($payslip->hra ?? 0, 2) }}</td>
                <td style="border-left: 1px solid #f1f5f9;">ESIC</td>
                <td class="text-right">₹{{ number_format($payslip->esic ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>LTA</td>
                <td class="text-right">₹{{ number_format($payslip->lta ?? 0, 2) }}</td>
                <td style="border-left: 1px solid #f1f5f9;">NPS</td>
                <td class="text-right">₹{{ number_format($payslip->nps ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Special Allowance</td>
                <td class="text-right">₹{{ number_format($payslip->special_allowance ?? 0, 2) }}</td>
                <td style="border-left: 1px solid #f1f5f9;">Labour Welfare Fund</td>
                <td class="text-right">₹{{ number_format($payslip->labour_welfare_fund ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Conveyance Allowance</td>
                <td class="text-right">₹{{ number_format($payslip->conveyance_allowance ?? 0, 2) }}</td>
                <td style="border-left: 1px solid #f1f5f9;">Professional Tax</td>
                <td class="text-right">₹{{ number_format($payslip->professional_tax ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Corporate NPS</td>
                <td class="text-right">₹{{ number_format($payslip->corporate_nps ?? 0, 2) }}</td>
                <td style="border-left: 1px solid #f1f5f9;">Income Tax</td>
                <td class="text-right">₹{{ number_format($payslip->income_tax ?? 0, 2) }}</td>
            </tr>

            <!-- Total Row -->
            <tr class="total-row">
                <td>Gross Salary</td>
                <td class="text-right">₹{{ number_format($payslip->gross_salary ?? 0, 2) }}</td>
                <td style="border-left: 1px solid #BFDBFE;">Total Deductions</td>
                <td class="text-right">₹{{ number_format($payslip->total_deduction ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Total Net Payable Box -->
    <div class="net-payable-container">
        <table class="net-payable-table">
            <tr>
                <td>
                    <strong style="font-size: 13px; color: #1e293b;">TOTAL NET PAYABLE</strong><br>
                    <span style="font-size: 10px; color: #64748b;">Gross Salary - Total Deductions</span>
                </td>
                <td class="payable-box">
                    ₹{{ number_format($payslip->net_salary ?? 0, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Amount in Words -->
    @if(!empty($amount_in_words))
    <div class="words-section">
        <strong>Amount In Words :</strong> {{ $amount_in_words }}
    </div>
    @endif

    <!-- Footer System Note -->
    <div class="footer-note">
        -- This is a system-generated document. --
    </div>

</body>
</html>