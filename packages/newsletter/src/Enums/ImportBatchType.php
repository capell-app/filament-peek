<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

enum ImportBatchType: string
{
    case Import = 'import';
    case Export = 'export';
}
