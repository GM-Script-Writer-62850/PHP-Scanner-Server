/**
 * Color picker
 * Author: Stefan Petre www.eyecon.ro
 * Patched By: GM-Script-Writer-62850 github.com/GM-Script-Writer-62850
 * 
 * Dual licensed under the MIT and GPL licenses
 */
(function ($) {
	"use strict";
	var ColorPicker = function () {
		var pickerCount=0,
			charMin = 65,
			SAS = 150, // Select Area Size
			HSH = 150, // Hue Selector Height
			char3Input='<input onkeypress="return validateKey(this,event,null);" onchange="this.value=Number(this.value)||0;" type="text" maxlength="3"/>',
			tpl =	'<div class="colorpicker">'+
						'<div class="colorpicker_color">'+
							'<div>'+
								'<div></div>'+
							'</div>'+
						'</div>'+
						'<div class="colorpicker_hue tool">'+
							'<span class="tip">Hue</span>'+
							'<div></div>'+
						'</div>'+
						'<div class="colorpicker_new_color tool">'+
							'<span class="tip">New Color</span>'+
						'</div>'+
						'<div class="colorpicker_current_color tool">'+
							'<span class="tip">Old Color</span>'+
						'</div>'+
						'<div class="colorpicker_hsb_h colorpicker_field tool">H'+
							'<span></span>'+
							char3Input+
							'<span class="tip">Hue</span>'+
						'</div>'+
						'<div class="colorpicker_hsb_s colorpicker_field tool">S'+
							'<span></span>'+
							char3Input+
							'<span class="tip">Saturation</span>'+
						'</div>'+
						'<div class="colorpicker_hsb_b colorpicker_field tool">B'+
							'<span></span>'+
							char3Input+
							'<span class="tip">Brightness</span>'+
						'</div>'+
						'<div class="colorpicker_rgb_r colorpicker_field tool">R'+
							'<span></span>'+
							char3Input+
							'<span class="tip">Red</span>'+
						'</div>'+
						'<div class="colorpicker_rgb_g colorpicker_field tool">G'+
							'<span></span>'+
							char3Input+
							'<span class="tip">Green</span>'+
						'</div>'+
						'<div class="colorpicker_rgb_b colorpicker_field tool">B'+
							'<span></span>'+
							char3Input+
							'<span class="tip">Blue</span>'+
						'</div>'+
						'<div class="colorpicker_hex tool">#'+
							'<span class="tip">Hex Code</span>'+
							'<input type="text" maxlength="6" spellcheck="false"/>'+
						'</div>'+
						'<div class="colorpicker_submit tool">'+
							'<span class="tip">Apply</span>'+
						'</div>'+
					'</div>',
			defaults = {
				eventName: 'click',
				onShow: function () {},
				onBeforeShow: function(){},
				onHide: function () {},
				onChange: function () {},
				onSubmit: function () {},
				color: '408080',
				livePreview: true,
				flat: false
			},
			fillRGBFields = function  (col, cal) {
				if(col.r==null)
					var col = HSBToRGB(col);
				$(cal).data('colorpicker').fields
					.eq(3).val(Math.round(col.r)).end()
					.eq(4).val(Math.round(col.g)).end()
					.eq(5).val(Math.round(col.b)).end();
			},
			fillHSBFields = function  (col, cal) {
				if(col.h==null)
					col=RGBToHSB(col);
				$(cal).data('colorpicker').fields
					.eq(0).val(Math.round(col.h)).end()
					.eq(1).val(Math.round(col.s)).end()
					.eq(2).val(Math.round(col.b)).end();
			},
			fillHexFields = function (col, cal) {
				$(cal).data('colorpicker').fields
					.eq(6).val(col.h==null?RGBToHex(col):HSBToHex(col)).end();
			},
			setSelector = function (hsb, cal) {
				$(cal).data('colorpicker').selector.css('backgroundColor', '#' + HSBToHex({h: hsb.h, s: 100, b: 100}));
				$(cal).data('colorpicker').selectorIndic.css({
					left: Math.round(SAS * hsb.s/100),
					top: Math.round(SAS * (100-hsb.b)/100)
				});
			},
			setHue = function (hsb, cal) {
				$(cal).data('colorpicker').hue.css('top', Math.round(HSH - HSH * hsb.h/360));
			},
			setCurrentColor = function (hsb, cal) {
				$(cal).data('colorpicker').currentColor.css('backgroundColor', '#' + HSBToHex(hsb));
			},
			setNewColor = function (hsb, cal) {
				$(cal).data('colorpicker').newColor.css('backgroundColor', '#' + HSBToHex(hsb));
			},
			keyDown = function (ev) {
				var pressedKey = ev.charCode || ev.keyCode || -1;
				if ((pressedKey > charMin && pressedKey <= 90) || pressedKey == 32) {
					return false;
				}
				var cal = $(this).parent().parent();
				if (cal.data('colorpicker').livePreview === true) {
					change.apply(this);
				}
			},
			change = function (ev) {
				var cal = $(this).parent().parent(),col,c=cal.get(0);
				if (this.parentNode.className.indexOf('_hex') > 0) {
					cal.data('colorpicker').color = col = HexToHSB(this.value);
					if(ev){
						fillRGBFields(col, c);
						fillHSBFields(col, c);
					}
				}
				else{
					var flds=cal.data('colorpicker').fields;
					if (this.parentNode.className.indexOf('_hsb') > 0) {
						cal.data('colorpicker').color = col = fixHSB({
							h: flds.eq(0).val(),
							s: flds.eq(1).val(),
							b: flds.eq(2).val()
						});
						if(ev){
							fillRGBFields(col, c);
							fillHexFields(col, c);
						}
					}
					else {
						cal.data('colorpicker').color = col = fixRGB({
							r: flds.eq(3).val(),
							g: flds.eq(4).val(),
							b: flds.eq(5).val()
						});
						if(ev){
							fillHSBFields(col, c);
							fillHexFields(col, c);
						}
						col=RGBToHSB(col);
					}
				}
				setSelector(col, c);
				setHue(col, c);
				setNewColor(col, c);
				cal.data('colorpicker').onChange.apply(cal, [col, HSBToHex(col), HSBToRGB(col)]);
			},
			blur = function (ev) {
				var cal = $(this).parent().parent();
				cal.data('colorpicker').fields.parent().removeClass('colorpicker_focus');
			},
			focus = function () {
				charMin = this.parentNode.className.indexOf('_hex') > 0 ? 70 : 65;
				$(this).parent().parent().data('colorpicker').fields.parent().removeClass('colorpicker_focus');
				$(this).parent().addClass('colorpicker_focus');
			},
			downIncrement = function (ev) {
				var field = $(this).parent().find('input').focus();
				var current = {
					el: $(this).parent().addClass('colorpicker_slider'),
					max: this.parentNode.className.indexOf('_hsb_h') > 0 ? 360 : (this.parentNode.className.indexOf('_hsb') > 0 ? 100 : 255),
					y: ev.pageY,
					field: field,
					val: Number(field.val()),
					preview: $(this).parent().parent().data('colorpicker').livePreview					
				};
				$(document).bind('mouseup', current, upIncrement);
				$(document).bind('mousemove', current, moveIncrement);
			},
			moveIncrement = function (ev) {
				ev.data.field.val(Math.round(Math.max(0, Math.min(ev.data.max, ev.data.val + -1*(ev.pageY - ev.data.y)))));
				if (ev.data.preview)
					change.apply(ev.data.field.get(0), [true]);
				return false;
			},
			upIncrement = function (ev) {
				change.apply(ev.data.field.get(0), [true]);
				ev.data.el.removeClass('colorpicker_slider').find('input').focus();
				$(document).unbind('mouseup', upIncrement);
				$(document).unbind('mousemove', moveIncrement);
				return false;
			},
			downHue = function (ev) {
				var current = {
					cal: $(this).parent(),
					y: $(this).offset().top
				};
				current.preview = current.cal.data('colorpicker').livePreview;
				$(document).bind('mouseup', current, upHue);
				$(document).bind('mousemove', current, moveHue);
				ev.data=current;
				moveHue(ev);
			},
			moveHue = function (ev) {
				change.apply(
					ev.data.cal.data('colorpicker')
						.fields
						.eq(0)
						.val(Math.round(360*(HSH - Math.max(0,Math.min(HSH,(ev.pageY - ev.data.y))))/HSH))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upHue = function (ev) {
				fillRGBFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				$(document).unbind('mouseup', upHue);
				$(document).unbind('mousemove', moveHue);
				return false;
			},
			downSelector = function (ev) {
				var current = {
					cal: $(this).parent(),
					pos: $(this).offset()
				};
				current.preview = current.cal.data('colorpicker').livePreview;
				$(document).bind('mouseup', current, upSelector);
				$(document).bind('mousemove', current, moveSelector);
				ev.data=current;
				moveSelector(ev);
			},
			moveSelector = function (ev) {
				change.apply(
					ev.data.cal.data('colorpicker')
						.fields
						.eq(2)
						.val(Math.round(100*(SAS - Math.max(0,Math.min(SAS,(ev.pageY - ev.data.pos.top))))/SAS))
						.end()
						.eq(1)
						.val(Math.round(100*(Math.max(0,Math.min(SAS,(ev.pageX - ev.data.pos.left))))/SAS))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upSelector = function (ev) {
				fillRGBFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				$(document).unbind('mouseup', upSelector);
				$(document).unbind('mousemove', moveSelector);
				return false;
			},
			enterSubmit = function (ev) {
				$(this).addClass('colorpicker_focus');
			},
			leaveSubmit = function (ev) {
				$(this).removeClass('colorpicker_focus');
			},
			clickSubmit = function (ev) {
				var cal = $(this).parent();
				var col = cal.data('colorpicker').color;
				cal.data('colorpicker').origColor = col;
				setCurrentColor(col, cal.get(0));
				cal.data('colorpicker').onSubmit(col, HSBToHex(col), HSBToRGB(col), cal.data('colorpicker').el);
			},
			show = function (ev) {
				var cal = $('#' + $(this).data('colorpickerId'));
				cal.data('colorpicker').onBeforeShow.apply(this, [cal.get(0)]);
				var pos = $(this).offset(),
					bso = typeof(document.body.style.boxShadow)=='string'?7:0,// Box Shadow Offset
					viewPort = getViewport(),
					height = this.offsetHeight,
					//width = this.offsetWidth,
					Width = 350 + bso, Height = 170 + bso,// Offset of color picker
					top = pos.top + height + bso,
					left = pos.left + bso;
				if (top + Height > viewPort.t + viewPort.h)
					top -= height + Height + bso;
				if (left + Width > viewPort.l + viewPort.w)
					left -= Width - this.offsetWidth + bso;
				cal.css({left: left + 'px', top: top + 'px'});
				if (cal.data('colorpicker').onShow.apply(this, [cal.get(0)]) != false)
					cal.show();
				$(document).bind('mousedown', {cal: cal}, hide);
				return false;
			},
			hide = function (ev) {
				if (!isChildOf(ev.data.cal.get(0), ev.target, ev.data.cal.get(0))) {
					if (ev.data.cal.data('colorpicker').onHide.apply(this, [ev.data.cal.get(0)]) != false)
						ev.data.cal.hide();
					$(document).unbind('mousedown', hide);
				}
			},
			isChildOf = function(parentEl, el, container) {
				if (parentEl == el)
					return true;
				if (parentEl.contains)
					return parentEl.contains(el);
				if ( parentEl.compareDocumentPosition )
					return !!(parentEl.compareDocumentPosition(el) & 16);
				var prEl = el.parentNode;
				while(prEl && prEl != container) {
					if (prEl == parentEl)
						return true;
					prEl = prEl.parentNode;
				}
				return false;
			},
			getViewport = function () {
				var m = document.compatMode == 'CSS1Compat';
				return {
					l : window.pageXOffset || (m ? document.documentElement.scrollLeft : document.body.scrollLeft),
					t : window.pageYOffset || (m ? document.documentElement.scrollTop : document.body.scrollTop),
					w : window.innerWidth || (m ? document.documentElement.clientWidth : document.body.clientWidth),
					h : window.innerHeight || (m ? document.documentElement.clientHeight : document.body.clientHeight)
				};
			},
			fixHSB = function (hsb) {
				for(var i in hsb)
					hsb[i]=Math.min(i=='h'?360:100, Math.max(0, Number(hsb[i])||0));
				return hsb;
			}, 
			fixRGB = function (rgb) {
				for(var i in rgb)
					rgb[i]=Math.min(255, Math.max(0, Number(rgb[i])||0));
				return rgb;
			},
			HexToRGB = function (hex) {
				hex = hex.slice(0,1)=='#' ? hex.substr(1) : hex;
				var rgb=Array(), x=hex.length==3?1:2;
				for(var i=0,s=x*3;i<s;i+=x)
					rgb.push(hex.substr(i,x))
				for(var i in rgb)
					rgb[i]=parseInt(x==1?rgb[i]+rgb[i]:rgb[i],16);
				return { r:rgb[0], g:rgb[1], b:rgb[2] };
			},
			HexToHSB = function (hex) {
				return RGBToHSB(HexToRGB(hex));
			},
			RGBToHSB = function (rgb) {
				var hsb = {	h:0, s:0, b:0 },
					min = Math.min(rgb.r, rgb.g, rgb.b),
					max = Math.max(rgb.r, rgb.g, rgb.b),
					delta = max - min;
				hsb.b = max;
				hsb.s = max != 0 ? 255 * delta / max : 0;
				if (hsb.s != 0) {
					if (rgb.r == max)
						hsb.h = (rgb.g - rgb.b) / delta;
					else if (rgb.g == max)
						hsb.h = 2 + (rgb.b - rgb.r) / delta;
					else
						hsb.h = 4 + (rgb.r - rgb.g) / delta;
				}
				else
					hsb.h = -1;
				hsb.h *= 60;
				if (hsb.h < 0)
					hsb.h += 360;
				hsb.s *= 100/255;
				hsb.b *= 100/255;
				return hsb;
			},
			HSBToRGB = function (hsb) {
				var rgb = {},
					h = hsb.h,
					s = hsb.s*255/100,
					v = hsb.b*255/100;
				if(s == 0)
					rgb.r = rgb.g = rgb.b = v;
				else {
					var t1 = v;
					var t2 = (255-s)*v/255;
					var t3 = (t1-t2)*(h%60)/60;
					if(h==360) h = 0;
					if(h<60) {rgb.r=t1; rgb.b=t2; rgb.g=t2+t3}
					else if(h<120) {rgb.g=t1; rgb.b=t2; rgb.r=t1-t3}
					else if(h<180) {rgb.g=t1; rgb.r=t2; rgb.b=t2+t3}
					else if(h<240) {rgb.b=t1; rgb.r=t2; rgb.g=t1-t3}
					else if(h<300) {rgb.b=t1; rgb.g=t2; rgb.r=t2+t3}
					else if(h<360) {rgb.r=t1; rgb.g=t2; rgb.b=t1-t3}
					else {rgb.r=0; rgb.g=0; rgb.b=0;}
				}
				return {r:rgb.r, g:rgb.g, b:rgb.b};
			},
			RGBToHex = function (rgb) {
				var hex='',val;
				for(var i in rgb){
					val=Math.round(rgb[i]).toString(16);
					hex+=val.length==1?'0'+val:val;
				}
				return hex;
			},
			HSBToHex = function (hsb) {
				return RGBToHex(HSBToRGB(hsb));
			},
			restoreOriginal = function () {
				var cal = $(this).parent(),
					col = cal.data('colorpicker').origColor;
				cal.data('colorpicker').color = col;
				cal=cal.get(0);
				fillRGBFields(col, cal);
				fillHexFields(col, cal);
				fillHSBFields(col, cal);
				setSelector(col, cal);
				setHue(col, cal);
				setNewColor(col, cal);
			};
		return {
			init: function (opt) {
				opt = $.extend({}, defaults, opt||{});
				if (typeof opt.color == 'string')
					opt.color = HexToHSB(opt.color);
				else if (opt.color.r != undefined)
					opt.color = RGBToHSB(opt.color);
				else if (opt.color.h != undefined)
					opt.color = fixHSB(opt.color);
				else
					return this;
				return this.each(function () {
					if (!$(this).data('colorpickerId')) {
						var options = $.extend({}, opt),c,
							id = 'collorpicker_' + (pickerCount+=1);
						options.origColor = opt.color;
						$(this).data('colorpickerId', id);
						var cal = $(tpl).attr('id', id);
						if (options.flat)
							cal.appendTo(this).show();
						else
							cal.appendTo(document.body);
						options.fields = cal
							.find('input')
								.bind('keyup', keyDown)
								.bind('change', change)
								.bind('blur', blur)
								.bind('focus', focus);
						cal
							.find('span:first-child').bind('mousedown', downIncrement).end()
							.find('>div.colorpicker_current_color').bind('click', restoreOriginal);
						options.selector = cal.find('div.colorpicker_color').bind('mousedown', downSelector);
						options.selectorIndic = options.selector.find('>div');
						options.el = this;
						options.hue = cal.find('div.colorpicker_hue div');
						cal.find('div.colorpicker_hue').bind('mousedown', downHue);
						options.newColor = cal.find('div.colorpicker_new_color');
						options.currentColor = cal.find('div.colorpicker_current_color');
						cal.data('colorpicker', options);
						cal.find('div.colorpicker_submit').bind('click', clickSubmit);
						c=cal.get(0);
						fillRGBFields(options.color, c);
						fillHSBFields(options.color, c);
						fillHexFields(options.color, c);
						setHue(options.color, c);
						setSelector(options.color, c);
						setCurrentColor(options.color, c);
						setNewColor(options.color, c);
						if (options.flat) {
							cal.css({
								position: 'relative',
								display: 'block'
							});
						}
						else
							$(this).bind(options.eventName, show);
					}
				});
			},
			showPicker: function() {
				return this.each( function () {
					if ($(this).data('colorpickerId'))
						show.apply(this);
				});
			},
			hidePicker: function() {
				return this.each( function () {
					if ($(this).data('colorpickerId'))
						$('#' + $(this).data('colorpickerId')).hide();
				});
			},
			setColor: function(col) {
				if (typeof col == 'string')
					col = HexToHSB(col);
				else if (col.r != undefined)
					col = RGBToHSB(col);
				else if (col.h != undefined)
					col = fixHSB(col);
				else
					return this;
				return this.each(function(){
					if ($(this).data('colorpickerId')) {
						var cal = $('#' + $(this).data('colorpickerId'));
						cal.data('colorpicker').color = col;
						cal.data('colorpicker').origColor = col;
						cal=cal.get(0);
						fillRGBFields(col, cal);
						fillHSBFields(col, cal);
						fillHexFields(col, cal);
						setHue(col, cal);
						setSelector(col, cal);
						setCurrentColor(col, cal);
						setNewColor(col, cal);
					}
				});
			}
		};
	}();
	$.fn.extend({
		ColorPicker: ColorPicker.init,
		ColorPickerHide: ColorPicker.hidePicker,
		ColorPickerShow: ColorPicker.showPicker,
		ColorPickerSetColor: ColorPicker.setColor
	});
})(jQuery)
