var $=require("jquery"),Dialog=require("./dialogPC.js"),ImageSelectDialog=require("./imageSelectDialog.js"),Widget={};Widget.ImagesUploader=function(container){var $container=$(container),$valueHolder=$container.find("[data-value]"),$listHolder=$container.find(".list"),$me=this,server=$container.attr("data-upload-server");this.value=[];var init=function(){var initValue;try{try{initValue=JSON.parse($valueHolder.val())}catch(e){eval("initValue = "+$valueHolder.val())}}catch(e){initValue=[]}$me.val(initValue),$container.on("click",".item .close",function(){var e=$(this).closest(".item"),a=parseInt(e.attr("data-item-index"));return $me.value.splice(a,1),$me.val($me.value),!1}),$container.on("click",".uploader",function(){new ImageSelectDialog({server:server,limitMax:100,callback:function(e){for(var a=0;a<e.length;a++)$me.value.push(e[a]);$me.val($me.value)}},Dialog).show()})};this.val=function(e){if(void 0===e)return $me.value;$me.value=e,$listHolder.find(".item").remove();for(var a,i=$me.value.length-1;i>=0;i--){a=$(['<div class="item" data-image-preview>','   <a href="javascript:;" class="close">x</a>','   <div class="cover"></div>',"</div>"].join(""));var t=$me.value[i];0===t.indexOf("http://")||0===t.indexOf("https://")||0===t.indexOf("/")||(t="/"+t),a.attr("data-item-index",i),a.data("data",$me.value[i]),a.attr("href",t),a.find(".cover").css({backgroundImage:"url("+t+")"}),$listHolder.prepend(a)}$valueHolder.val(JSON.stringify($me.value)),$container.trigger("widget.images-uploader.change",[$me])},init()},Widget.init=function(){$(".widget-images-uploader").each(function(e,a){$(a).data("widget")||$(a).data("widget",new Widget.ImagesUploader(a))})},module.exports=Widget;