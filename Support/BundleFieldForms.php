<?php

namespace Pingu\Field\Support;

use Pingu\Field\Forms\CreateBundleFieldForm;
use Pingu\Field\Forms\EditBundleFieldForm;
use Pingu\Forms\Support\BaseForms;
use Pingu\Forms\Support\Form;

class BundleFieldForms extends BaseForms
{
    /**
     * @inheritDoc
     */
    public function create(array $args): Form
    {
        return new CreateBundleFieldForm($this->model, ...$args);
    }

    /**
     * @inheritDoc
     */
    public function edit(array $args): Form
    {
        return new EditBundleFieldForm($this->model, ...$args);
    }
}