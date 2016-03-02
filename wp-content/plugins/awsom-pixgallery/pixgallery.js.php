<?php
define('WP_USE_THEMES', false);
require('../../../wp-blog-header.php');
PixGallery_Options_Load(); // Load PixGallery options
?>

//-------------------------------------------------------------------------------------//
// Javascript Library written by Nathan Moinvaziri
//-------------------------------------------------------------------------------------//

var DOM = (function() {

	if (document.documentElement)
		return document.documentElement;
	else if (document.body)
		return document.body;

	return null;

})();

var Window = {

	Width: function(Frame) {
		if (window.innerWidth)
			return window.innerWidth;
		if (!Frame)
			Frame = DOM;
		return Frame.offsetWidth;
	},
	Height: function(Frame) {
		if (window.innerHeight)
			return window.innerHeight;
		if (!Frame)
			Frame = DOM;
		return Frame.offsetHeight;
	},
	ClientWidth: function(Frame) {
		if (!Frame)
			Frame = DOM;
		return Frame.clientWidth;
	},
	ClientHeight: function(Frame) {
		if (!Frame)
			Frame = DOM;
		return Frame.clientHeight;
	},
	ScrollLeft: function(Frame) {
		if (!Frame)
			Frame = DOM;
		return Frame.scrollLeft;
	},
	ScrollTop: function(Frame) {
		if (window.pageYOffset)
			return window.pageYOffset;
		if (!Frame)
			Frame = DOM;
		return Frame.scrollTop;
	},
	ScrollLeft: function(Frame) {
		if (!Frame)
			Frame = DOM;
		return Frame.scrollWidth;
	},
	ScrollHeight: function(Frame) {
		if (!Frame)
			Frame = DOM;
		return Frame.scrollHeight;
	}
}
var Debug = {
	Print: function(Message) {

		var Object = Element.Get("js-debug");

		if (!Object)
			{
			Object = Element.Create("DIV");

			Object.id				= "js-debug";
			Object.innerHTML		= "";
			Object.style.height		= "100px";
			Object.style.width		= "500px";
			Object.style.overflow	= "scroll";

			Element.Append(Object);
			}

		Object.innerHTML = Message + "<br>" + Object.innerHTML;

		return true;
	}
}

var Element = {

	Get: function(Object) {
		if (!Object)
			return Object;
		var Find = Object;
		if (typeof(Object) == "string")
			Find = document.getElementById(Object);
		return Find;
	},
	Create: function(Name, Id, ClassName) {

		NewElement = document.createElement(Name);

		if (Id)
			NewElement.id = Id;
		if (ClassName)
			NewElement.className = ClassName;

		return NewElement;
	},
	GetCreate: function (Name, Id, ClassName) {
		Object = Element.Get(Id);
		if (!Object)
			return Element.Create(Name, Id, ClassName);
		return Object;
	},
	CreateText: function(Text) {
		return document.createTextNode(Text);
	},
	Append: function(Object, AppendTo) {
		Object = Element.Get(Object);
		if (!AppendTo)
			AppendTo = document.body;
		AppendTo.appendChild(Object);
		return true;
	},
	GetAttribute: function(Object, Name) {
		Object = Element.Get(Object);
		if (!Object || !Object[Name])
			return 0;
		return Object.getAttribute(Name);
	},
	SetAttribute: function(Object, Name, Value) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		Object.setAttribute(Name, Value);
		return true;
	},
	GetStyle: function(Object, Name) {
		Object = Element.Get(Object);
		if (!Object || !Object.style[Name])
			return 0;
		return Object.style[Name];
	},
	GetStyleInt: function(Object, Name) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		return parseInt(Element.GetStyle(Object, Name));
	},
	GetStyleFloat: function(Object, Name) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		return parseFloat(Element.GetStyle(Object, Name));
	},
	SetStyle: function(Object, Name, Value) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		Object.style[Name] = Value;
		return true;
	},
	SetAsMovable: function (Object) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		if (Object.style.position != "relative" && Object.style.position != "absolute")
			Object.style.position = "relative";
		return true;
	},
	Center: function(Object, Layer) {
		Object	= Element.Get(Object);
		Layer	= Element.Get(Layer);

		if (!Object)
			return false;

		Width	= Element.GetStyleInt(Object, "width");
		Height	= Element.GetStyleInt(Object, "height");

		if (Layer)
			ParentWidth = Element.GetStyleInt(Layer, "width");
		else
			ParentWidth = Window.ClientWidth();

		if (Layer)
			ParentHeight = Element.GetStyleInt(Layer, "height");
		else
			ParentHeight = Window.ClientHeight();

		if (!Width)
			Width = Object.width;
		if (!Height)
			Height = Object.height;

		Top	 = ((ParentHeight - Height) / 2);
		Left = ((ParentWidth - Width) / 2);

		if (!Layer)
			Top += Window.ScrollTop();

		Element.SetStyle(Object, "left", Left + "px");
		Element.SetStyle(Object, "top",  Top + "px");

		return true;
	},
	GetVisibility: function(Object) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		if (!Object.style.visibility)
			return false;
		if (Object.style.visibility == "visible")
			return true;
		if (Object.style.visibility != "hidden")
			return true;

		return false;
	},
	SetVisibility: function(Object, Visible) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		if (Visible == true)
			Object.style.visibility = "visible";
		else
			Object.style.visibility = "hidden";

		return true;
	},
	Show: function(Object) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		Object.style.display = "block";
		return true;
	},
	Hide: function(Object) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		Object.style.display = "none";
		return true;
	},
	IsHidden: function(Object) {
		Object = Element.Get(Object);
		if (!Object)
			return false;
		if (Object.style.display != "none")
			return false;
		return true;
	},
	GetOpacity: function(Object) {
		Object = Element.Get(Object);
		if (!Object)
			return 0;

		if (!Object.style.MozOpacity)
			{
			if (Element.GetVisibility(Object) == false)
				return 0;

			return 100;
			}

		return (Object.style.MozOpacity * 100);
	},
	SetOpacity: function(Object, Opacity) {
		Object = Element.Get(Object);
		if (!Object)
			return false;

		Object.style.opacity		= (Opacity / 100);
		Object.style.KhtmlOpacity	= (Opacity / 100);
		Object.style.MozOpacity		= (Opacity / 100);

		if (Opacity == 100)
			Object.style.filter = "";
		else
			Object.style.filter	= "alpha(opacity=" + Opacity + ")";

		return true;
	},
	GetByTagName: function(TagName) {
		return document.getElementsByTagName(TagName);
	}
}

function ImageEx()
{
	this.InnerImage	= new Image();
}

ImageEx.prototype = {

	GetImage: function() {
		return this.InnerImage;
	},
	GetAlt: function() {
		return this.InnerImage.alt;
	},
	SetAlt: function(Alt) {
		this.InnerImage.alt = Alt;
		return true;
	},
	SetCallback: function(Callback) {
		this.Callback = Callback;
	},
	Load: function(Url) {
		var Self = this;
		var Timer = function() { Self.Loading(); };

		this.TimerId		= window.setInterval(Timer, 1);
		this.InnerImage.src	= Url;
	},
	Loading: function() {

		if (this.InnerImage.complete == true)
			{
			clearInterval(this.TimerId);

			if (typeof(this.Callback) == "function")
				this.Callback(this.InnerImage);
			if (typeof(this.Callback) == "string")
				eval(this.Callback());
			}
	}
}

var Class = {
	Create: function() {
		return function() { this.Initialize.apply(this, arguments); }
	}
}

function Effect(Object, Duration, Interval) {

	this.Object		= Object;
	this.Duration	= Duration;
	this.Interval	= 5;
	this.Complete	= null;
	this.Iteration	= null;

	if (Interval)
		this.Interval = Interval;
	if (typeof(Object) == "string")
		this.Object = document.getElementById(Object);

	this.Reset();
}

Effect.prototype = {

	/* Effect time management and execution */

	Reset: function() {

		this.TimerId			= null;
		this.TimerStart			= null;
		this.Stop				= false;
		this.Stopping			= false;
		this.PercentMultiplier	= 0;
		this.PercentComplete	= 0;

		return true;
	},
	Start: function() {

		if (typeof(this.Object.Effect) == "undefined")
			this.Object.Effect = null;

		if (this.Object.Effect != null)
			return false;

		this.Object.Effect = this;

		var Self = this;
		var Timer = function() { Self.Elapse(); };

		this.Stop		= false;

		this.TimerStart	= new Date().getTime();
		this.TimerId	= window.setInterval(Timer, this.Interval);

		return true;
	},
	Stop: function() {
		this.Stop = true;
		return true;
	},
	Elapse: function() {

		this.Stopping = false;

		CurrentTime = (new Date().getTime());
		ElapsedTime = (CurrentTime - this.TimerStart);

		if (CurrentTime >= (this.TimerStart + this.Duration) || this.Stop == true)
			this.Stopping = true;

		this.PercentMultiplier = (ElapsedTime / this.Duration);

		if (this.PercentMultiplier > 1)
			this.PercentMultiplier = 1;

		this.PercentComplete = (this.PercentMultiplier * 100);

		for (Property in this)
			{
			if (Property.indexOf("Elapse") != -1 && Property != "Elapse")
				this[Property]();
			}

		if (this.Update)
			this.Update();

		if (this.Stopping == true)
			{
			window.clearInterval(this.TimerId);

			this.Object.Effect = null;

			if (this.Complete)
				{
				if (typeof(this.Complete) == "function")
					this.Complete();
				if (typeof(this.Complete) == "string")
					eval(this.Complete);
				}

			this.Reset();
			}

		return true;
	},

	/* Effects */

	Fade: function(Opacity) {
		this.PerformFade		= true;

		this.FadeOpacity		= Opacity;
		this.FadeOpacityStart	= Element.GetOpacity(this.Object);

		return true;
	},
	ElapseFade: function() {

		if (!this.PerformFade || this.PerformFade == false)
			return false;

		CurrentOpacity  = Element.GetOpacity(this.Object);

		OpacityChange	= (this.FadeOpacity - this.FadeOpacityStart);
		Opacity			= (this.FadeOpacityStart + (this.PercentMultiplier * OpacityChange));

		Element.SetOpacity(this.Object, Opacity);

		if (this.Stopping == true)
			this.PerformFade = false;

		return true;
	},
	MoveBy: function(X, Y) {

		this.PerformMoveBy	= true;

		this.MoveByXStart	= Element.GetStyleInt(this.Object, "left");
		this.MoveByYStart	= Element.GetStyleInt(this.Object, "top");

		this.MoveByXEnd		= this.MoveByXStart + X;
		this.MoveByYEnd		= this.MoveByYStart + Y;

		this.MoveByXDelta	= (this.MoveByXEnd - this.MoveByXStart);
		this.MoveByYDelta	= (this.MoveByYEnd - this.MoveByYStart);

		return true;
	},
	ElapseMoveBy: function() {

		if (!this.PerformMoveBy || this.PerformMoveBy == false)
			return false;

		CurrentX	= Element.GetStyleInt(this.Object, "left");
		CurrentY	= Element.GetStyleInt(this.Object, "top");

		X	= (this.MoveByXStart + (this.PercentMultiplier * this.MoveByXDelta));
		Y	= (this.MoveByXStart + (this.PercentMultiplier * this.MoveByYDelta));

		Element.SetAsMovable(this.Object);

		if (this.MoveByXDelta != 0)
			Element.SetStyle(this.Object, "left", X + "px");
		if (this.MoveByYDelta != 0)
			Element.SetStyle(this.Object, "top", Y + "px");

		if (this.Stopping == true)
			this.PerformMoveBy = false;

		return true;
	},
	ResizeTo: function(Width, Height) {

		this.PerformResizeTo		= true;

		this.ResizeToWidthEnd		= Width;
		this.ResizeToHeightEnd		= Height;

		this.ResizeToWidthStart		= Element.GetStyleInt(this.Object, "width");
		this.ResizeToHeightStart	= Element.GetStyleInt(this.Object, "height");

		this.ResizeToLeftStart		= Element.GetStyleInt(this.Object, "left");
		this.ResizeToTopStart		= Element.GetStyleInt(this.Object, "top");

		this.ResizeToWidthDelta		= (this.ResizeToWidthEnd - this.ResizeToWidthStart);
		this.ResizeToHeightDelta	= (this.ResizeToHeightEnd - this.ResizeToHeightStart);

		return true;
	},
	ElapseResizeTo: function() {

		if (!this.PerformResizeTo || this.PerformResizeTo == false)
			return false;

		CurrentWidth	= Element.GetStyleInt(this.Object, "width");
		CurrentHeight	= Element.GetStyleInt(this.Object, "height");

		Width	= (this.ResizeToWidthStart + (this.PercentMultiplier * this.ResizeToWidthDelta));
		Height	= (this.ResizeToHeightStart + (this.PercentMultiplier * this.ResizeToHeightDelta));

		WidthDelta = ((this.PercentMultiplier * this.ResizeToWidthDelta) / 2);

		if (this.ResizeToWidthDelta != 0)
			{
			Left = Element.GetStyleInt(this.Object, "left");
			Element.SetStyle(this.Object, "left", (this.ResizeToLeftStart - WidthDelta) + "px");
			Element.SetStyle(this.Object, "width", Width + "px");
			}

		HeightDelta = ((this.PercentMultiplier * this.ResizeToHeightDelta) / 2);

		if (this.ResizeToHeightDelta != 0)
			{
			Top = Element.GetStyleInt(this.Object, "top");
			Element.SetStyle(this.Object, "top", (this.ResizeToTopStart - HeightDelta) + "px");
			Element.SetStyle(this.Object, "height", Height + "px");
			}

		if (this.Stopping == true)
			this.PerformResizeTo = false;

		return true;
	},
	ScaleBy: function(Change, Scale) {

		this.PerformScaleBy = true;

		this.ScaleBy		= Change;
		this.ScaleByScale	= Scale;
		this.ScaleByStart	= Element.GetStyleInt(this.Object, "zoom");

		return true;
	},
	ElapseScaleBy: function() {

		if (!this.PerformScaleBy || this.PerformScaleBy == false)
			return false;

		CurrentSize = Element.GetStyleInt(this.Object, "zoom");

		SizeChange	= (this.ScaleBy - this.ScaleByStart);
		Size		= (this.ScaleByStart + (this.PercentMultiplier * SizeChange));

		Element.SetStyle(this.Object, "zoom", Size + this.ScaleByScale);

		return true;
	}
}

function Pulse(Object, Duration) {

	this.Object		= Object;
	this.Duration	= 500;

	if (Duration)
		this.Duration = Duration;

	this.On();
};

Pulse.prototype = {

	On: function() {

		var Self = this;
		var PulseOff = function() { Self.Off(); };

		EffectObj = new Effect(this.Object, (this.Duration / 2));

		EffectObj.Fade(40);
		EffectObj.Complete = PulseOff;

		EffectObj.Start();

		return true;
	},
	Off: function() {

		EffectObj = new Effect(this.Object, (this.Duration / 2));

		EffectObj.Fade(100);
		EffectObj.Start();

		return true;
	}
};

function Extender(Object, StartHeight, EndHeight)
{
	this.Object = Element.Get(Object);

	CurrentHeight = Element.GetStyleInt(this.Object, "height");

	if (CurrentHeight == EndHeight)
		HeightChange = (StartHeight - EndHeight);
	else
		HeightChange = (EndHeight - StartHeight);

	if (!CurrentHeight)
		Element.SetStyle(this.Object, "height", StartHeight);

	Element.SetStyle(this.Object, "overflow", "hidden");
	Element.SetStyle(this.Object, "display", "block");

	var Self = this;
	var Done = function() { Self.Complete(); };

	EffectObj = new Effect(this.Object, 1000);

	EffectObj.ResizeTo(0, HeightChange);
	EffectObj.Complete = Done;
	EffectObj.Start();
}

Extender.prototype = {

	Complete: function() {

		if (Element.GetStyleInt(this.Object, "height") == 0)
			Element.SetStyle(this.Object, "display", "none");

		return;
	}
};

function BackgroundShadow(Color, Opacity)
{
	Shadow = Element.Get("EffectBkgrd");

	if (!Shadow)
		{
		Shadow = Element.Create("DIV");

		Shadow.id					= "EffectBkgrd";
		Shadow.innerHTML			= "&nbsp;";
		Shadow.style.zIndex			= 0;
		Shadow.style.position		= "absolute";
		Shadow.style.left			= "0px";
		Shadow.style.top			= "0px";

		Element.SetOpacity(Shadow, 0);
		Element.Hide(Shadow);

		document.body.appendChild(Shadow);
		}

	Shadow.style.background			= Color;
	Shadow.style.backgroundColor	= Color;
	Shadow.style.width				= "100%";
	Shadow.style.height				= Window.ScrollHeight() + 'px';


	Element.Show(Shadow);

	EffectObj = new Effect(Shadow, 250, 1);
	EffectObj.Fade(Opacity);
	EffectObj.Complete = function() {
		if (Element.GetOpacity("EffectBkgrd") == 0)
			Element.Hide("EffectBkgrd");
		}
	EffectObj.Start();

	return;
}

//-------------------------------------------------------------------------------------//
// Javascript PixGallery Library written by Nathan Moinvaziri
//-------------------------------------------------------------------------------------//

var Cursor				= {X:0, Y:0};
var DragDropPosition	= {X:0, Y:0, StartX: 0, StartY: 0};
var DragDropObject		= null;
var DragDropActive		= false;
var PreloadedImage		= new Array();

function PixGallery_Initialize()
{
	if ((!window.event) && (typeof Event != "undefined"))
		document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP);

	document.onmousemove	= PixGallery_EventMouseMove;
	document.onmousedown	= PixGallery_EventMouseDown;
	document.onmouseup		= PixGallery_EventMouseUp;

	PreloadedImage[0] = new Image();
	PreloadedImage[0].src = "<?=get_option('siteurl')?>/wp-content/plugins/awsom-pixgallery/images/loading.gif";
	PreloadedImage[1] = new Image();
	PreloadedImage[1].src = "<?=get_option('siteurl')?>/wp-content/plugins/awsom-pixgallery/images/background.gif";

	PixGallery_AttachLinks();

	return;
}

function PixGallery_AttachLinks()
{
	var Links = Element.GetByTagName("a");

	for(var i = 0; i != Links.length; i += 1)
		{
		Object = Links[i];

		if (Object.rel.toLowerCase() != "pixgallery")
			continue;

		<?php if ($GLOBALS["PixGallery_PopupMethod"] == "Layer") { ?>
			Object.onclick = function() { return PixGallery_PopupLayer(this); };
		<?php } else if ($GLOBALS["PixGallery_PopupMethod"] == "Window") { ?>
			Object.onclick = function() { return PixGallery_PopupWindow(this); };
		<?php } else { ?>
			Object.onclick = function() { return; };
		<?php } ?>
		}

	return;
}

function PixGallery_EventMouseDown(e)
{
	if (window.event)
		e = window.event;

	if (document.all)
		Object = e.srcElement;
	else
		Object = e.target;

	while ((Object.tagName != "HTML" && Object.tagName != "BODY") && (Object.className != "PxgPopupDrag"))
		{
		if (document.all)
			Object = Object.parentElement;
		else
			Object = Object.parentNode;
		}

	if (Object.className != "PxgPopupDrag")
		return true;

	DragDropObject	= Object;

	DragDropPosition.X	= e.clientX;
	DragDropPosition.Y	= e.clientY;

	DragDropPosition.StartX = parseInt(DragDropObject.style.left);
	DragDropPosition.StartY = parseInt(DragDropObject.style.top);

	return true;
}

function PixGallery_EventMouseUp() {
	DragDropObject = null;
}

function PixGallery_EventScroll() {
	PixGallery_PopupLayerClose();
}

function PixGallery_EventMouseMove(e)
{
	if (window.event)
		e = window.event;

	if (e.pageX || e.pageY)
		{
		Cursor.X = e.pageX;
		Cursor.Y = e.pageY;
		}
	else
		{
		Cursor.X = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) -
						document.documentElement.clientLeft;
		Cursor.Y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) -
						document.documentElement.clientTop;
		}

	if (DragDropObject)
		{
		DragDropObject.style.left	= DragDropPosition.StartX + e.clientX - DragDropPosition.X + "px";
		DragDropObject.style.top	= DragDropPosition.StartY + e.clientY - DragDropPosition.Y + "px";
		}
}

function PixGallery_PopupWindow(LinkObject)
{
	NewImage = new ImageEx();

	NewImage.SetAlt(LinkObject.title);
	NewImage.SetCallback(PixGallery_PopupWindowComplete);
	NewImage.Load(LinkObject.href);

	return false;
}

function PixGallery_PopupWindowComplete(ImageObject)
{
	var PopupWindowOptions	= "height=" + ImageObject.height + ", width=" + ImageObject.width + "";
	var PopupWindow			= window.open(ImageObject.src, '', PopupWindowOptions);
	var PopupCode			= "";

	if (ImageObject.alt)
		PopupWindow.document.write("<title>" + ImageObject.alt + "</title>");

	PopupCode += "<style>body { margin: 0px; padding: 0px;}</style><body>";
	PopupCode += "<img id='Picture' src=\"" + ImageObject.src + "\"></body>";

	PopupWindow.document.write(PopupCode);

	var Picture = PopupWindow.document.getElementById("Picture");

	Picture.src = ImageObject.src;
}

function PixGallery_PopupLayer(LinkObject)
{
	BackgroundShadow("black", 40);

	PopupLayer		= Element.GetCreate("DIV", "PxgPopupLayer", "PxgPopupDrag");
	PopupHeader		= Element.GetCreate("DIV", "PxgPopupHeader");
	PopupClose		= Element.GetCreate("DIV", "PxgPopupClose");
	PopupTitle		= Element.GetCreate("DIV", "PxgPopupTitle");
	PopupImage		= Element.GetCreate("IMG", "PxgPopupImage");
	PopupLoading	= Element.GetCreate("IMG", "PxgPopupImageLoading");

	if (Element.Get("PxgPopupLayer") == null)
		{
		PopupClose.appendChild(Element.CreateText("Close"));

		PopupClose.onclick = PixGallery_PopupLayerClose;

		PopupHeader.appendChild(PopupTitle);
		PopupHeader.appendChild(PopupClose);

		PopupLayer.appendChild(PopupHeader);
		PopupLayer.appendChild(PopupImage);
		PopupLayer.appendChild(PopupLoading);

		PopupLayer.style.width	= "220px";
		PopupLayer.style.height	= "20px";
		PopupLayer.style.zIndex	= 200;
		PopupLayer.style.backgroundImage = "url('" + PreloadedImage[1].src + "')";

		document.body.appendChild(PopupLayer);
		}

	Element.Hide(PopupHeader);
	Element.Hide(PopupImage);
	Element.Hide(PopupLoading);

	Element.Show(PopupLayer);
	Element.Center(PopupLayer);

	PopupTitle.innerHTML	= "";
	PopupLoading.src		= PreloadedImage[0].src;

	Element.SetStyle(PopupLoading, "height", PreloadedImage[0].height + "px");
	Element.SetStyle(PopupLoading, "width", PreloadedImage[0].width + "px");

	Element.Center(PopupLoading, PopupLayer);
	Element.Show(PopupLoading);

	NewImage = new ImageEx();
	NewImage.SetAlt(LinkObject.title);
	NewImage.SetCallback(PixGallery_PopupLayerResize);
	NewImage.Load(LinkObject.href);

	return false;
}

function PixGallery_PopupLayerResize(ImageObject)
{
	PopupLayer		= Element.Get("PxgPopupLayer");
	PopupImage		= Element.Get("PxgPopupImage");
	PopupLoading	= Element.Get("PxgPopupImageLoading");

	if (!PopupImage || !PopupLayer || !PopupLoading)
		return true;

	Height = (ImageObject.height + 30);

	EffectObj = new Effect(PopupLayer, 1500, 1);
	EffectObj.ResizeTo(ImageObject.width, Height);
	EffectObj.Update = function() { Element.Center("PxgPopupImageLoading", "PxgPopupLayer"); };
	EffectObj.Complete = function() { PixGallery_PopupLayerComplete(ImageObject); };
	EffectObj.Start();

	return true;
}

function PixGallery_PopupLayerComplete(ImageObject)
{
	PopupLayer		= Element.Get("PxgPopupLayer");
	PopupClose		= Element.Get("PxgPopupClose");
	PopupTitle		= Element.Get("PxgPopupTitle");
	PopupHeader		= Element.Get("PxgPopupHeader");
	PopupImage		= Element.Get("PxgPopupImage");
	PopupLoading	= Element.Get("PxgPopupImageLoading");

	Element.SetOpacity(PopupImage, 0);
	Element.Show(PopupHeader);
	Element.Hide(PopupLoading);

	PopupImage.alt	= ImageObject.alt;
	PopupImage.src	= ImageObject.src;

	Element.Center(PopupImage, PopupLayer);
	Element.Show(PopupImage);

	ImageAlt	= ImageObject.alt;
	Title		= ImageAlt;

	PopupTitle.innerHTML = Title;

	Element.SetOpacity(PopupImage, 0);

	EffectObj = new Effect(PopupImage, 1500, 1);
	EffectObj.Fade(100);
	EffectObj.Start();

	return true;
}

function PixGallery_PopupLayerClose()
{
	PopupLayer	= Element.Get("PxgPopupImage");
	PopupImage	= Element.Get("PxgPopupLayer");

	if (!PopupImage || !PopupLayer)
		return true;

	if ((Element.IsHidden(PopupImage) == true) &&
		(Element.IsHidden(PopupLayer) == true))
		return;

	Element.Hide(PopupImage);
	Element.Hide(PopupLayer);

	BackgroundShadow("black", 0);

	return true;
}

window.onload	= PixGallery_Initialize;
window.onscroll	= PixGallery_EventScroll;

//-------------------------------------------------------------------------------------//