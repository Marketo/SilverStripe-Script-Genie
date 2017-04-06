# SilverStripe-Script-Genie

## Configuration

```
ObjectType:
  regenerate_scripts: true:
  extensions:
    - GenieExtension

Injector:
  GenieScriptService:
    properties:
      typeConfiguration:
        MyClass:
          target_filename: # just use 'default' if no other needed
            generator: AClassName # Implementing getList
            filter: 
              "arrayKey:GreaterThan": "filter value"
              "CalculatedField": %strtotime|-2 weeks|Y-m-d H:i:s% 
              "OtherProp": "with things"
            order: FieldName DESC, Other ASC
            limit: 10
            template: TemplateName
            fields: ID,LastEdited,Title [optional, otherwise all fields returned]
            rootObject: TopLevelJsObjectName [optional, defaults to Window]
```     

For CalculatedField above, the system will calculate an appropriate value based on the provided arguments, in this
case it will perform a `strototime` on '-2 weeks', followed by converting it to a date in the provided format.

If you're wanting to generate the list completely by hand, create a class that implements the `getList` method
that returns an SS_List (ie `DataList` or `ArrayList`) . 
