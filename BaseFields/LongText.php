<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Query\Builder;
use Pingu\Field\Displayers\DefaultTextDisplayer;
use Pingu\Field\Displayers\TrimmedTextDisplayer;
use Pingu\Forms\Support\Fields\TextInput;
use Pingu\Forms\Support\Fields\Textarea;

class LongText extends Text
{
    protected static $availableWidgets = [Textarea::class];

    protected static $availableFilterWidgets = [TextInput::class];

    protected static $displayers = [DefaultTextDisplayer::class, TrimmedTextDisplayer::class];
}