lzyy = {}
lzyy.br2nl = function(str)
{
	return str.replace(/<br\s*\/?>/mg,"\n");
};
lzyy.nl2br = function(str)
{
	return str.replace(/\n/mg,"<br />");
};
lzyy.post_to_url = function(path, params, method) 
{
    method = method || "post";
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

	for(var key in params) 
	{
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);

        form.appendChild(hiddenField);
    }

    document.body.appendChild(form);    // Not entirely sure if this is necessary
    form.submit();
};
lzyy.object_size = function (obj) 
{
	var len = 0;
	for (var k in obj)
	{
		if (obj.hasOwnProperty(k)) len++;
	}
	return len;
}

var lzyy_form = (function(){
	var _form,_error;
	var _append_error = function()
	{
		var error_template = '<p class="error_message">{}</p>';
		for(var elem in _error)
		{
			var error_message = error_template.replace('{}',_error[elem]);
			$('input[type!="radio"][name="'+elem+'"],textarea[name="'+elem+'"]').after(error_message);
			var $radio = $('input[type="radio"][name="'+elem+'"]');
			if($radio.size())
			{
				$radio.parent().after(error_message);
			}
		}
	};
	var _show_form_value = function()
	{
		for(var elem in _form)
		{
			$('input[name="'+elem+'"],textarea[name="'+elem+'"]').val(_form[elem]);
		}
	};
	return {
		'render':function(data){
			_form = data.form;
			_error = data.error;
			if(lzyy.object_size(_error)) _append_error();
			_show_form_value();
		}
	};
})();

// plugin
jQuery.fn.hint = function (blurClass) {
  if (!blurClass) { 
    blurClass = 'blur';
  }
 
  return this.each(function () {
    // get jQuery version of 'this'
    var $input = jQuery(this),
 
    // capture the rest of the variable to allow for reuse
      title = $input.attr('title'),
      $form = jQuery(this.form),
      $win = jQuery(window);
 
    function remove() {
      if ($input.val() === title && $input.hasClass(blurClass)) {
        $input.val('').removeClass(blurClass);
      }
    }
 
    // only apply logic if the element has the attribute
    if (title) { 
      // on blur, set value to title attr if text is blank
      $input.blur(function () {
        if (this.value === '') {
          $input.val(title).addClass(blurClass);
        }
      }).focus(remove).blur(); // now change all inputs to title
 
      // clear the pre-defined text when form is submitted
      $form.submit(remove);
      $win.unload(remove); // handles Firefox's autocomplete
    }
  });
};

$(function(){
	$('input[title!=""],textarea[title!=""]').hint('grey');

	if(location.hash!='')
	{
		var target = $('a[hashtag="'+location.hash+'"]'); 
		if(target)
		{
			$.scrollTo(target,1000);
			target.next().css('background-color','#efe').animate({backgroundColor:'#fff'},3000);
		}
	};
	$('form').submit(function(){
		$(this).find('input[type="submit"]').attr('disabled','true');
	})
});
