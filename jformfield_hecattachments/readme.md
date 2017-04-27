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
    
