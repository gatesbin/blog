var InputWithAddMinus=function(a,n){var t=$(a);return n=$.extend({changeCallback:function(a){}},n),t.each(function(a,t){var i=$(t);i.on("click","[data-add]",function(){var a=i.find("[data-value]"),t=parseInt(a.val());if(t++,a.is("[data-max]")){var c=parseInt(a.attr("data-max"));t>c&&(t=c)}return a.val(t),n.changeCallback(a),!1}),i.on("click","[data-minus]",function(){var a=i.find("[data-value]"),t=parseInt(a.val());if(t--,a.is("[data-min]")){var c=parseInt(a.attr("data-min"));t<c&&(t=c)}return a.val(t),n.changeCallback(a),!1}),i.find("[data-value]").on("change",function(){n.changeCallback($(this))})})};module.exports=InputWithAddMinus;