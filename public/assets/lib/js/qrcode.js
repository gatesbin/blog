var JquryQrcode=require("./jqueryQrcode.js"),Qrcode={},init=!1;Qrcode.init=function(o){init||(init=!0,$(document).on("click","[data-page-qrcode]",function(){o.dialogContent('<div style="width:240px;height:240px;padding:20px;box-sizing:border-box;" data-qrcode-pop></div>',{openCallback:function(){$("[data-qrcode-pop]").qrcode({size:200,text:window.location.href,background:"#FFF"})}})}),$(document).on("click","[data-qrcode-content]",function(){var d=$(this).attr("data-qrcode-content");o.dialogContent('<div style="width:240px;height:240px;padding:20px;box-sizing:border-box;" data-qrcode-pop></div>',{openCallback:function(){$("[data-qrcode-pop]").qrcode({size:200,text:d,background:"#FFF"})}})}))},module.exports=Qrcode;