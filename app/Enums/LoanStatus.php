<?php

declare(strict_types=1);

namespace App\Enums;

enum LoanStatus: string
{
    case Open = 'open';
    case FullyFunded = 'fully_funded';
}
