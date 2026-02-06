<?php

namespace App\Enums;

enum MasterVoucherType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
