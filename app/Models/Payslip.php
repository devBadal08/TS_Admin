<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_month',

        'working_days',
        'paid_days',
        'leaves',
        'lop',

        'basic',
        'hra',
        'lta',
        'special_allowance',
        'conveyance_allowance',
        'corporate_nps',

        'pf',
        'esic',
        'nps',
        'labour_welfare_fund',
        'professional_tax',
        'income_tax',

        'gross_salary',
        'total_deduction',
        'net_salary',
    ];

    protected $casts = [
        'salary_month' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}