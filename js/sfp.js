var models =  JSON.parse(window.sfpSettings.data);

var fieldTypes = ['date','text','textarea'];

var modelsNR;
var handel_model = 'admin-ajax.php';

function GET_modelView(model, idModel)  {
    var name    = model['name'];
    var title   = model['title'];
    var content = model['content'];
    var fields  = model['fields'];

    var view = "<div class='modelType' id='mod_"+idModel+"'>" +
        "       <div>" +
        "           <span class='modelName'>"+name+"</span>" +
        "           <span class='tools_editModel'>" +
        "               <input type='button' value='edit' onclick='EDITmodel(\""+idModel+"\")'/>" +
        "               <input type='button' value='delete' onclick='DELETEmodel(\""+idModel+"\")'/>" +
        "           </span>" +
        "       </div>" +
        "       <div>"+title+"</div>"+
        "       <div>"+content+"</div>";
    for(var field in fields)
      view += "<div>" +
          "         <span class='field_name'>"+fields[field]['name']+"</span>" +
          "         <span>"+fields[field]['type']+"</span>" +
          "     </div>";

    view +="</div>";

    return view;
}

function EDITmodel(idModel){
    var model = models[idModel];
    var name    = model['name'];
    var title   = model['title'];
    var content = model['content'];
    var fields  = model['fields'];

    var view = "<form action='' method='post' id='editMOD'>"+
        "       <div>" +
        "           <input type='text' name='model_Name' class='modelName' value='"+name+"' />" +
        "           <span class='tools_editModel'>" +
        "               <input type='button' value='save' onclick='SAVEmodel(\"editmodel\",\""+idModel+"\")' />" +
        "               <input type='button' value='close' onclick='closeEDITmodel()'/>" +
        "           </span>" +
        "       </div>" +
        "       <div><input type='text' name='title_Label' value='"+title+"' /></div>"+
        "       <div><input type='text' name='content_Label' value='"+content+"' /></div>";

    //=========================================[ LIST FIELDS ]==========================================================
    for(var field in fields)
        view += "<div class='modelField' id='field_"+field+"'>" +
            "         <input type='text' name='field_Name"+field+"' class='field_name' value='"+fields[field]['name']+"' />" +
                      SET_fieldTypes(fields[field]['type'])+
            "         <input type='button' value='remove' onclick='removeField(\""+field+"\")'>"+
            "     </div>";

    //=========================================[ ADD FIELD ]============================================================
    field++;

        view +=
            "   <div class='modelField' id='field_"+field+"' style='margin-top: 20px;'>" +
            "         <input type='text' name='field_Name"+field+"' class='field_name' placeholder='new field' />" +
                      SET_fieldTypes('')+
            "         <input type='button' value='add' onclick='addField(\""+field+"\")'>"+
            "     </div>";

    view += "</form>";



    jQuery('body').append("<div class='popUP_EDITmodel'>"+view+"</div>");
}

function ADDmodel()             {


    idModel = modelsNR;

    var view = "<form action='' method='post' id='editMOD'>"+
        "       <div>" +
        "           <input type='text' name='model_Name' class='modelName' placeholder='model name' />" +
        "           <span class='tools_editModel'>" +
        "               <input type='button' value='save' onclick='SAVEmodel(\"addmodel\",\""+idModel+"\")' />" +
        "               <input type='button' value='close' onclick='closeEDITmodel()'/>" +
        "           </span>" +
        "       </div>" +
        "       <div><input type='text' name='title_Label' placeholder='title label' /></div>"+
        "       <div><input type='text' name='content_Label' placeholder='content label' /></div>";


    //=========================================[ ADD FIELD ]============================================================
    field = 0;

    view +=
        "   <div class='modelField' id='field_"+field+"' style='margin-top: 20px;'>" +
            "         <input type='text' name='field_Name[]' class='field_name' placeholder='new field' />" +
            SET_fieldTypes('')+
            "         <input type='button' value='add' onclick='addField(\""+field+"\")'>"+
            "     </div>";

    view += "</form>";



    jQuery('body').append("<div class='popUP_EDITmodel'>"+view+"</div>");
}

function addField(idField)      {

    lastField = jQuery("form #field_"+idField);

    lastField.attr('style','')
             .find('input[value=add]')
                .attr('value','remove')
                .attr('onclick','removeField(\"'+idField+'\")');

    idField++;
    lastField.after(
       "   <div class='modelField' id='field_"+idField+"' style='margin-top: 20px;' >" +
            "         <input type='text' name='field_Name' class='field_name' placeholder='new field' />" +
                      SET_fieldTypes('')+
            "         <input type='button' value='add' onclick='addField(\""+idField+"\")'>"+
            "     </div>"
    );
}

function removeField(idField)   {

    jQuery("form #field_"+idField).remove();
}


function closeEDITmodel()       {

    jQuery('.popUP_EDITmodel').remove();
}

function SAVEmodel(action, idModel){

    //action = add or edit



    var name     = jQuery("#editMOD input[name='model_Name']").val();
    var title    = jQuery("#editMOD input[name='title_Label']").val();
    var content  = jQuery("#editMOD input[name='content_Label']").val();

    var fields = new Array();
    var field_name;
    var field_type='';


    jQuery("#editMOD .modelField").map(function(){

        field_name = jQuery(this).children("input[name^='field_Name']").val();

        field_type = jQuery(this).find("select option:selected").val();

        fields.push({name:field_name, type:field_type});


    });

    fields.pop();
//=========================================================================================================

    var model =  {
                  name:name,
                  title:title,
                  content:content,
                  fields:fields
                };

    //jQuery.post(handel_model, {model:model, action:action, idModel:idModel});
    console.log(idModel);
    var nonce = {addmodel: window.sfpSettings.addNonce, editmodel:window.sfpSettings.editNonce};

    var a = jQuery.post('admin-ajax.php',{'id':idModel,'action':action,nonce:nonce[action],data:JSON.stringify(model)});

    a.done(function () { window.location="http://localhost/selfpublishing/wp-admin/admin.php?page=omc/selfpublisher.php"; });
    a.fail(function () { alert('ERROR!'); });
}

function SET_fieldTypes(selectedType)    {

    var selected = '';

    var HTMLoptions = "<select name='field_type[]'>"  ;
    for(var type in fieldTypes)
        {
            if(selectedType == fieldTypes[type]) selected = 'selected';

            HTMLoptions +="<option "+selected+" value='"+fieldTypes[type]+"' >"+fieldTypes[type]+"</option>";

            selected = '';
        }
        HTMLoptions +="</select>";

        return HTMLoptions;
}


jQuery(document).ready(function() {
    modelsNR = models.length;
    for(var modelID in models )
        {
            HTMLview = GET_modelView(models[modelID],modelID);
            jQuery("#modelList").prepend(HTMLview);
        }
});
