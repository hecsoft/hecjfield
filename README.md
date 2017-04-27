# HEC Joomla JField Collection
Some powerfull JFields for Joomla 3.x in order to add interesting functionalities to your component

## JField datatable
JField encapsulate JQuery datatables javascript library [JQuery datatables website](https://datatables.net/)

### Use
To add datatable JField to your component, add a `<field type="datatable">` node to your xml model file.
Define columns by adding `<field>` nodes in `<form>` subnode.

__Example__
    
    <field name="table" type="datatable"
            label="COM_FIELDTEST_FORM_LBL_TABLE_NAME"
            description="COM_FIELDTEST_FORM_DESC_TABLE_NAME" 
            required="true" >
            <form>
                <field name="id" 
                      type="hidden" 
                      default="0" 
                      label="COM_FIELDTEST_FORM_LBL_TABLE_ID"
                      readonly="true" 
                      class="hidden"
                      description="JGLOBAL_FIELD_ID_DESC" /> 
                 <field name="name" 
                        type="text" 
                        default="0" 
                        label="My Name"
                        description="JGLOBAL_FIELD_ID_DESC" /> 
                 <field name="value" 
                        type="text" 
                        default="0" 
                        label="Valeur" 
                        columnname="My Value"
                        description="JGLOBAL_FIELD_ID_DESC" />
           </form>
      </field>
    
