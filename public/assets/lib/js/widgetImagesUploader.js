var $=require("jquery"),UploadButton=require("./uploadButton.js"),Widget={},WidgetDialog=null;Widget.ImagesUploader=function(container){var $container=$(container),$valueHolder=$container.find("[data-value]"),$listHolder=$container.find(".list"),$me=this;this.value=[];var init=function(){var initValue;try{try{initValue=JSON.parse($valueHolder.val())}catch(e){eval("initValue = "+$valueHolder.val())}}catch(e){initValue=[]}return $me.val(initValue),"undefined"==typeof __uploadButton?void alert("需要在使用页面上进行动态配置 __uploadButton"):"swf"in __uploadButton?"server"in __uploadButton?"chunkSize"in __uploadButton?($container.on("click",".item .close",function(){var e=$(this).closest(".item"),t=parseInt(e.attr("data-item-index"));return $me.value.splice(t,1),$me.val($me.value),!1}),void UploadButton($container.find(".list .uploader"),{text:'<div style="line-height:50px;height:50px;width:50px;font-size:20px;padding:0;text-align:center;color:#999;"><span class="uk-icon-plus am-icon-plus"></span></div>',swf:__uploadButton.swf,server:__uploadButton.server,extensions:"jpg,png,gif,jpeg",sizeLimit:5242880,chunkSize:__uploadButton.chunkSize,callback:function(e,t){$(t).attr("data-name");$me.value.push(e.path),$me.val($me.value)}})):void alert("需要在使用页面上进行动态配置 __uploadButton.chunkSize"):void alert("需要在使用页面上进行动态配置 __uploadButton.server"):void alert("需要在使用页面上进行动态配置 __uploadButton.swf")};this.val=function(e){if(void 0===e)return $me.value;$me.value=e,$listHolder.find(".item").remove();for(var t,a=$me.value.length-1;a>=0;a--){t=$(['<div class="item" data-image-preview>','   <a href="javascript:;" class="close">x</a>','   <div class="cover"></div>',"</div>"].join(""));var i=$me.value[a];0===i.indexOf("http://")||0===i.indexOf("https://")||0===i.indexOf("/")||(i="/"+i),t.attr("data-item-index",a),t.data("data",$me.value[a]),t.attr("href",i),t.find(".cover").css({backgroundImage:"url("+i+")"}),$listHolder.prepend(t)}$valueHolder.val(JSON.stringify($me.value)),$container.trigger("widget.images-uploader.change",[$me])},init()},Widget.init=function(e){WidgetDialog=e,$(".widget-images-uploader").each(function(e,t){$(t).data("widget")||$(t).data("widget",new Widget.ImagesUploader(t))})},module.exports=Widget;