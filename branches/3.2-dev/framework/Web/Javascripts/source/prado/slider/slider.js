Prado.WebUI.TSlider = Class.extend(Prado.WebUI.PostBackControl,
{	
	onInit : function (options)
	{
		this.options=options;
		this.onChange=options.onChange;
		options.onChange=this.change.bind(this);
		
		this.onSlide=options.onSlide;
		options.onSlide=this.slide.bind(this);
		
		this.hiddenField=$(this.options.ID+'_1');
		new Control.Slider(options.ID+'_handle',options.ID, options);
		
		if(this.options['AutoPostBack']==true)
			Event.observe(this.hiddenField, "change", Prado.PostBack.bindEvent(this,options));
	},
	
	round : function (v)
	{
		// round the value with specified decimals
		var d=Math.pow(10,this.options['decimals']);
		// Round
		return (Math.round(v*d)/d);
	},
	
	change : function (v)
	{
		v=this.round(v);
		this.hiddenField.value=v;
		if (this.onChange)
		{
			this.onChange(v);
		}
		if(this.options['AutoPostBack']==true)
		{
			Event.fireEvent(this.hiddenField, "change");
		}
	},
	
	slide : function (v)
	{
		v=this.round(v);
		if (this.onSlide)
		{
			this.onSlide(v);
		}
	}
});