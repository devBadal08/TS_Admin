<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use NumberFormatter;

class PayslipController extends Controller
{
    public function download(Payslip $payslip)
    {
        $payslip->load('employee');

        // Convert Net Salary to Words
        $amountInWords = '';
        if (class_exists('NumberFormatter')) {
            $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
            $amountInWords = ucwords($formatter->format($payslip->net_salary ?? 0)) . ' Only';
        }

        $pdf = Pdf::loadView('pdf.payslip', [
            'payslip'         => $payslip,
            'employee'        => $payslip->employee,
            'amount_in_words' => $amountInWords,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'Payslip-' .
            ($payslip->employee->employee_code ?? 'EMP') .
            '-' .
            $payslip->salary_month->format('M-Y') .
            '.pdf'
        );
    }
}