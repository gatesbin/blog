var Pager=function(a,t,s){var p=this,e=$.extend({jump:!0,total:!0},s);t=t||function(a){alert(a)};var r=$(a);r.on("click","a.page",function(){var a=$(this).attr("data-page");return t(a),!1}),r.on("click","button",function(){var a=parseInt($(this).prev().val());return a?(t(a),!1):($.tipError("请输入正确页码"),!1)}),this.total=0,this.render=function(a,t,s,n){n=n||0,p.total=a;var u=Math.ceil(a/t);n>0&&a>n&&(u=Math.ceil((n-1)/t)),s=s||1,s=parseInt(s);var i=function(a,t){for(var p=a;p<=t;p++)p==s?l.push('<span class="current">'+p+"</span>"):l.push('<a class="page" href="javascript:;" title="'+p+'" data-page="'+p+'">'+p+"</a>")},l=[];if(l.push('<div class="pages">'),l.push("<span>"),e.total&&(l.push("共"+a+"条"),n>0&&a>n&&l.push("(显示前"+n+"条)")),l.push("</span>"),u<4)u>1&&i(1,u);else{i(1,1);var c=s-1,h=s+1;c<2&&(c=2),h>u-1&&(h=u-1),c>2&&l.push('<span class="more">...</span>'),i(c,h),h<u-1&&l.push('<span class="more">...</span>'),i(u,u)}s>1&&l.push('<a class="page" href="javascript:;" title="'+(s-1)+'" data-page="'+(s-1)+'">上页</a>'),s<u&&l.push('<a class="page" href="javascript:;" title="'+(s+1)+'" data-page="'+(s+1)+'">下页</a>'),u>1&&e.jump&&l.push('<span class="jumpBox"><input type="text" value="'+s+'" class="page" /> <button>跳转</button></span>'),l.push("</div>"),r.html(l.join(""))}};module.exports=Pager;