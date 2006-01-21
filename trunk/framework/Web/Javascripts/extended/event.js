Object.extend(Event, {
	OnLoad : function (fn) {
		// opera onload is in document, not window
		var w = document.addEventListener && !window.addEventListener ? document : window;
		Event.__observe(w,'load',fn);
	},
	observe: function(elements, name, observer, useCapture) {
    if(!isList(elements))
		return this.__observe(elements, name, observer, useCapture);
	for(var i=0; i<elements.length; i++)
		this.__observe(elements[i], name, observer, useCapture);
  },
  __observe: function(element, name, observer, useCapture) {
    var element = $(element);
    useCapture = useCapture || false;
    
    if (name == 'keypress' &&
        ((navigator.appVersion.indexOf('AppleWebKit') > 0) 
        || element.attachEvent))
      name = 'keydown';
    
    this._observeAndCache(element, name, observer, useCapture);
  },
   keyCode : function(e)
	{
	   return e.keyCode != null ? e.keyCode : e.charCode
	},

	isHTMLEvent : function(type)
	{
		var events = ['abort', 'blur', 'change', 'error', 'focus', 'load', 'reset', 'resize', 'scroll', 'select', 'submit', 'unload'];
		return events.include(type);
	},

	isMouseEvent : function(type)
	{
		var events = ['click', 'mousedown', 'mousemove', 'mouseout', 'mouseover', 'mouseup'];
		return events.include(type);
	},

	fireEvent : function(element,type)
	{
		if(document.createEvent)
        {            
			if(Event.isHTMLEvent(type))
			{
				var event = document.createEvent('HTMLEvents');
	            event.initEvent(type, true, true);
			}
			else if(Event.isMouseEvent(type))
			{
				var event = document.createEvent('MouseEvents');
				event.initMouseEvent(type,true,true,
					document.defaultView, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
			}
			else
			{
				if(Logger) 
					Logger.error("undefined event", type);
				return;
			}
            element.dispatchEvent(event);
        }
        else if(element.fireEvent)
        {
            element.fireEvent('on'+type);
            element[type]();
        }
        else
            element[type]();
	}
});