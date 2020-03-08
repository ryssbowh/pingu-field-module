<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Query\Builder;
use Pingu\Forms\Support\Fields\Textarea;

class LongText extends Text
{
    protected static $availableWidgets = [Textarea::class];
}