<?php

namespace App\Enum;

enum UseCaseSystemNamesEnum: string
{
    case CAT_LIST = 'cat.list';
    case CAT_GET = 'cat.get';
    case CAT_CREATE = 'cat.create';
    case CAT_UPDATE = 'cat.update';
    case CAT_DELETE = 'cat.delete';
}
