# Fields API

Provides with an API to attach field to models, validate them and saves their values in the model.
Provides with an API to define bundle fields, that can be defined in bundles and attached to entities in revisions, and manage those revisions.

The fields attached to a model or entity is accessible through `MyModel::fields()`, this instance is unique throughout the application.

The validator for those fields is accessible through `MyModel::validator()`, this instance is unique throughout the application.

## Base fields

The Base fields defined by this module are :

- \_Float
- \_List : defines a list of items
- Boolean
- Datetime
- Email
- Integer
- LongText
- ManyModel
- Media
- Model
- Password
- Text

All those fields have a cardinality of 1, meaning only one value can be attached to them.

You will need to define a FieldRepository and a FieldValidator for every new Model you create.

Each Base Field can define default validation rules that will be applied to every validation process.
Each base Field define how it's rendered in a form. That can be overriden by your model field repository.
Each Base Field defines how the value is casted from and to a form value, and how that value is stored in the model, it can be a regular model's attribute, a 'single' relationship or a 'multiple' relationship.

## Bundle fields

Bundle fields are a more complicated sort of fields, they are themselves a Model since they need to be saved in database, and they inherit all the Base fields functionnalities.
Every Bundle field must implement `BundleFieldContract` and be registered in the application through the Field Facade :

`\Field::registerBundleFields($fields)`

A bundle field can have multiple values attached to it, so a value for a field for an entity will always be returned as an array.
You can define a bundle field to have a cardinality of 1 (only one value), but the value for an entity for that field will also return an array (of 1 element).

## Validators

Validators comes with handy methods to validate a form through those fields. It lets you define your validation rules for every field, and will take into account the default validation rules of each field.

## Cache

The fields repositories, validation rules and messages will be stored in Cache so they are not built every time.
This happens in the `Field` facade, and uses `ArrayCache` so that you can empty all the cache for an object in one go.

The config `field.useCache` controls whether you use this cache or not. it's off if your APP_DEBUG is set to true.

## Revisions

Revisions are activated on the entity.
They will be saved in database and are handled by the `RevisionRepository` class.
The config `field.numberRevisionsToKeep` controls how many revisions to keep in database.
Revisions will be saved for any field defined in the entity field repository and any bundle field if this entity is bundled.

### Form layouts

Each entity has a form layout, which defines the layout for the form that adds/edits that entity. The layout definition includes the place of the field in the form, the widget used to display it and the options associated to that widget.

for bundled entity that layout is the bundle layout that can be edited through the UI for each bundle.
Basic entities form layout is not editable atm.

Form layouts are registered and accessible in the Field facade 

## Commands

- artisan:module:make-entity-fields Model Module : Creates a new Field Repository class
- artisan:module:make-entity-validator Model Module : Creates a new Field Validator class