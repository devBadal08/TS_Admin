<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'employee_code',
        'phone',
        'designation',
        'joining_date',
        'pan',
        'uan',
        'pf_number',
        'bank_details',
        'basic_salary',
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
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'bank_details' => 'array',
        'status' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($employee) {

            if (empty($employee->employee_code)) {

                $lastEmployee = self::latest('id')->first();

                $nextNumber = $lastEmployee
                    ? ((int) substr($lastEmployee->employee_code, 2)) + 1
                    : 1;

                $employee->employee_code = 'TS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    
}