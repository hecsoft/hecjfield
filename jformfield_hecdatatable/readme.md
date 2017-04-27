## JField HEC datatable 
This JField encapsulate JQuery datatables javascript library [JQuery datatables website](https://datatables.net/)

_Folder_ : jformfield_hecdatatable

### Use
To add datatable JField to your component, add a `<field type="datatable">` node to your xml model file.
Define columns by adding `<field>` nodes in `<form>` subnode.


Attributes are:

Attribute | Description
--- | ---
type | = "datatable"
name | Name use to retrieve Post Data
description | Description (use by Tooltip)
required | With of the control
<form> | List of the columns. Each column is in <field> node

Column attributes are (<field> node):

Attribute | Description
--- | ---
type | Field Type (hidden, text...)"
name | Name use to retrieve Post Data
description | Description (use by Tooltip)
label | Column name displayed
readonly | Column is readonly or not
class | CSS Class
default | Default value
columnname | (Optional) Name of the column if different of the label


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
