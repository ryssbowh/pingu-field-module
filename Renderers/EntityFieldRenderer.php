<?php

namespace Pingu\Field\Renderers;

use Illuminate\Support\Collection;
use Pingu\Core\Support\Renderers\ViewModeRenderer;
use Pingu\Entity\Entities\DisplayField;
use Pingu\Entity\Entities\ViewMode;
use Pingu\Entity\Support\Entity;
use Pingu\Forms\Support\ClassBag;

class EntityFieldRenderer extends ViewModeRenderer
{   
    /**
     * @var DisplayField
     */
    protected $display;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var DisplayerContract
     */
    protected $displayer;

    /**
     * @var FirldContract
     */
    protected $field;

    public function __construct(Entity $entity, ViewMode $viewMode, DisplayField $display)
    {
        $this->display = $display;
        $this->displayer = $display->displayer;
        $this->entity = $entity;
        $this->field = $entity->fields()->get($this->display->field);
        parent::__construct($entity, $viewMode);
    } 

    /**
     * @inheritDoc
     */
    public function getHookName(): string
    {
        return 'field';
    }

    /**
     * View identifier for the field displayer
     * 
     * @return string
     */
    protected function displayerIdentifier(): string
    {
        return \Str::kebab($this->displayer::machineName());
    }
    
    
    /**
     * @inheritDoc
     */
    protected function viewFolder(): string
    {
        return 'fields';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultData(): Collection
    {
        return collect([
            'classes' => $this->getClasses(),
            'labelClasses' => $this->getLabelClasses(),
            'entity' => $this->entity,
            'displayer' => $this->displayer,
            'showLabel' => $this->display->label,
            'label' => $this->field->name(),
            'values' => $this->displayer->getFieldValues($this->entity)
        ]);
    }

    /**
     * Field classes
     * 
     * @return ClassBag
     */
    protected function getClasses(): ClassBag
    {
        return new ClassBag([
            'field', 
            'field-'.$this->display->field, 
            'field-'.$this->viewIdentifier(),
            'field-'.$this->displayerIdentifier()
        ]);
    }

    /**
     * Label classes
     * 
     * @return ClassBag
     */
    protected function getLabelClasses(): ClassBag
    {
        return new ClassBag([
            'field-label', 
            'field-label-'.$this->viewIdentifier(),
            'field-label-'.$this->display->field,
            'field-label-'.$this->displayerIdentifier()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultViews(): array
    {
        $displayerId = $this->displayerIdentifier();
        $folder = $this->viewFolder();
        $id = $this->viewIdentifier();
        return [
            $folder.'.'.$id.'_'.$this->entity->id.'_'.$this->viewMode->machineName.'_'.$displayerId,
            $folder.'.'.$id.'_'.$this->entity->id.'_'.$displayerId,
            $folder.'.'.$id.'_'.$this->viewMode->machineName.'_'.$displayerId,
            $folder.'.'.$id.'_'.$displayerId,
            $folder.'.'.$displayerId,
            $this->displayer->systemView(),
            'field@fields.field'
        ];
    }
}