# HEC Joomla JField Collection
Some powerfull JFields for Joomla 3.x in order to add interesting functionalities to your component

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
    
## JField HEC Attachments
This JField allow you to add an attachment feature :
..*Browse files on server
..*Upload file
..*Add files to a list

_Folder_ : jformfield_hecattachments

### Use
To add attachment JField to your component, add a `<field type="attachments">` node to your xml model file.

Attributes are:

Attribute | Description
--- | ---
type | = "attachments"
name | Name use to retrieve Post Data
hint | Hint description
description | Description (use by Tooltip)
label | JField Label
label_attachment | Label of Attachment button
label_browse | Label of Browse button
can_create_folder | 0 Can't create new folder, 1 Can Create new folder
controller | Controller to use for webservice (list content of directory, upload, create,...)
width | With of the control



__Example__

    <field 
        name="attachment" 
        __type="attachments"__ 
        hint="COM_HECMAILING_ATTACHMENT_DESC" 
        description="COM_HECMAILING_ATTACHMENT_DESC"
        label="COM_HECMAILING_ATTACHMENT" 
        label_attachment="COM_HECMAILING_ADD_ATTACHMENT" 
        label_browse="COM_HECMAILING_BROWSE_SERVER"
        can_create_folder="0" 
        controller="send" 
        width="800"
    />
    
    
 ## JField HEC fileexplorer 
This JField is a file explorer like. It display content of you website and allow you to navigate.
Features are :
..*Display content of directory like windows explorer
..*Navigate in directory structure
..*Download files
..*Upload files (using button or drag&drop)
..*Create directory
..*Delete file or directory
..*Manage access rights

_Folder_ : jformfield_hecfileexplorer

### Use
To add file explorer JField to your component, add a `<field type="datatable">` node to your xml model file.
Define columns by adding `<field>` nodes in `<form>` subnode.


Attributes are:

Attribute | Description
--- | ---
type | = "datatable"
name | Name use to retrieve Post Data
label | JField Label
description | Description (use by Tooltip)
default | Default directory
mode | Display mode (file : display only files, folder : display only folders, file&folder : display file and folders (default)
filter | Extension filter, only file with one of define extension will be shown (colon list extension  pdf,xls,xlsx) 
exclude | File/Folder exclude , don't show files or folder corresponding (colon list - xxx,yyy).



__Example__
    
    <field  type="filexplorer" 
            name="myfileexplorer" 
			default="/images/"  
			label="My documents"
			description="Browse my documents" 
			mode="file|folder|file&folder" 
			filter="xls|xlsx|jpg|pdf|png|doc|docx" 
			exclude="powered_by.png" /> 
    
