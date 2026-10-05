<?php

namespace App\Enums;

enum StockMovementsEnum :string
{
    case IN = 'in';
    case OUT = 'out';
    case TRANSFER = 'transfer';
}
