var init=!1,ChooseWidget={getOption:function($container){var initValue;try{try{initValue=JSON.parse($container.attr("data-choose-widget"))}catch(e){eval("initValue = "+$container.attr("data-choose-widget"))}}catch(t){initValue={}}return $.extend({url:""},initValue)},init:function(t){if(!t)return void alert("Dialog empty");if(init)return void alert("ChooseWidget already init");init=!0,$(document).on("click","[data-choose-widget] [data-label]",function(){var e=$(this).closest("[data-choose-widget]"),i=ChooseWidget.getOption(e);return window.__chooseWidgetItem=null,t.dialog(i.url,{closeCallback:function(){null!==window.__chooseWidgetItem&&e.chooseWidget().val(window.__chooseWidgetItem)}}),!1});var e=function(t){var e=$(t);this.val=function(t){if(void 0==t)return{value:e.find("[data-value]").val(),title:e.find("[data-title]").val()};t||(t={value:"",title:""}),e.find("[data-value]").val(t.value),e.find("[data-title]").val(t.title),e.find("[data-label]").html(t.title||"[选择]"),e.trigger("choose-widget.change",[e,t])}};$.fn.extend({chooseWidget:function(){return new e(this)}})}};module.exports=ChooseWidget;