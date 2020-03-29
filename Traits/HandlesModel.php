<?php 

namespace Pingu\Field\Traits;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;

trait HandlesModel
{
    abstract protected function getModel(): string;

    /**
     * @inheritDoc
     */
    public function uncastSingleValue($value)
    {
        if (is_a($value, $this->getModel())) {
            return $value->getKey();
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castToSingleFormValue($value)
    {
        return $value ? $value->getKey() : null;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValueFromDb($value)
    {
        return $value ? (int)$value : null;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValue($value)
    {
        return $value ? $this->getModel()::find($value) : null;
    }

    /**
     * @inheritDoc
     */
    public function toSingleDbValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where('value', '=', $value);
    }
}