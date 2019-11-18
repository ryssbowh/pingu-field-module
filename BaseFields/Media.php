<?php

namespace Pingu\Field\BaseFields;

use Pingu\Forms\Support\Fields\MediaField;
use Pingu\Media\Entities\Media as MediaModel;

class Media extends Model
{
    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        $extensions = \Media::getAvailableFileExtensions();
        return [$this->machineName => 'file_extension:'.implode(',', $extensions).'|max:'.config('media.maxFileSize')];
    }
    
    /**
     * @inheritDoc
     */
    protected function defaultFormFieldClass(): string
    {
        return MediaField::class;
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        if ($value instanceof UploadedFile) {
            $value = $this->field->uploadFile($value);
        }
        return MediaModel::find($value);
    }
}