jQuery(document).ready(function(){
	jQuery('button#edit_button').bind('click', function() {
		var name;
		name = jQuery(this).parent().parent().attr('id');
		
		sfpSettings.bindAll(addMask(sfpSettings.render(name).el));
	});
});

sfpSettings = window.sfpSettings;

sfpSettings.supportedStuff = ['text','date'];

sfpSettings.render = function(where) {
	where = where || 'artists';
	this.el = '<div><ul class="fields">';
	var fields = JSON.parse(sfpSettings.data)[where];
	for(var field in  fields){
		if (fields.hasOwnProperty(field)){
			this.el += '<li>' + this.getInputTemplate(field,fields[field]);
			this.el += this.getEditTemplate();
			this.el += '</li>';
		}
	}
	this.el += "</ul>";
	this.el += this.getEditModel();
	return this;
}

sfpSettings.getEditTemplate = function () {
	return '<button class="remove">Remove</button>';
}

sfpSettings.bindAll = function (obj) {
	obj.find('button.remove').on('click',function(){});
	//obj.find('button.save').on('click',sfpSettings.getInputTemplate);
}

sfpSettings.getEditModel = function () {
	return '<button class="add">Add</button><button class="save">Save</button>';
}


sfpSettings.getInputTemplate = function (name,type) {
	name = name || '';
	type = type || '';
	var result = '<input type="text" value="'+name+'" />';
	// select
	var t, i, selectText = '<select name="' + name + '">';
	for (i = 0; i < this.supportedStuff.length ; i++)
	{
		t = this.supportedStuff[i];
		selectText += '<option value="' + t + '"';
		if (t === type)
		{
			selectText += ' SELECTED ';
		}
		selectText += '>' + t + '</option>';
		
	}
	
	selectText += '</select>';
	
	result += selectText;
	return result;	
}
