# SilverStripe-Script-Genie

## Configuration

```
Injector:
  GenieScriptService:
    properties:
      typeConfiguration:
        MyClass:
          filter: 
            "arrayKey:GreaterThan": "filter value"
            "OtherProp": "with things"
          order: FieldName DESC, Other ASC
          template: TemplateName
          target: filename/relative/to/project/root.js
          rootObject: TopLevelObjectName [optional]
```     