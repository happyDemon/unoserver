<?php

namespace HappyDemon\UnoServer\Sources;

class Spreadsheet extends Source
{
    public const TYPE = 'spreadsheet';

    public function validateFormat(string $format): bool
    {
        return in_array(
            $format,
            [
                'pdf', 'html', 'xls', 'xlsx', 'csv'
            ]
        );
    }
}
