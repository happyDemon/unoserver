<?php

namespace HappyDemon\UnoServer\Sources;

class Document extends Source
{
    public const TYPE = 'document';

    public function validateFormat(string $format): bool
    {
        return in_array(
            $format,
            [
                'pdf', 'html', 'doc', 'docx', 'epub'
            ]
        );
    }
}
