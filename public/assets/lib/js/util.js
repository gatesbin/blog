var Util={};Util.specialchars=function(e){var t=[];if(!e)return"";if(0==e.length)return"";for(var n=0;n<e.length;n++)switch(e.substr(n,1)){case"<":t.push("&lt;");break;case">":t.push("&gt;");break;case"&":t.push("&amp;");break;case" ":t.push("&nbsp;");break;case'"':t.push("&quot;");break;default:t.push(e.substr(n,1))}return t.join("")},Util.text2html=function(e){return e=Util.specialchars(e),"<p>"+(e=e.replace(/\n/g,"</p><p>"))+"</p>"},Util.text2paragraph=function(e){return"<p>"+(e=e.replace(/\n/g,"</p><p>"))+"</p>"},Util.urlencode=function(e){return e=(e+"").toString(),encodeURIComponent(e).replace(/!/g,"%21").replace(/'/g,"%27").replace(/\(/g,"%28").replace(/\)/g,"%29").replace(/\*/g,"%2A").replace(/%20/g,"+")},Util.randomString=function(e){e=e||16;for(var t="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",n=t.length,u="",r=0;r<e;r++)u+=t.charAt(Math.floor(Math.random()*n));return u},Util.getRootWindow=function(){var e;for(e=window;e.self!==e.parent;)e=e.parent;return e},Util.fixPath=function(e,t){return t=t||"",e?0===e.indexOf("http://")||0===e.indexOf("https://")||0===e.indexOf("//")?e:(0===e.indexOf("/")||(e="/"+e),t&&t.lastIndexOf("/")==t.length-1&&(t=t.substr(0,t.length-1)),t+e):""},Util.set=function(e,t,n){for(var u,r=e,l=t.split("."),s=0;s<l.length-1;s++)u=l[s],u=0===u.indexOf("i")?parseInt(u.substr(1)):u.substr(1),r=r[u];u=l[l.length-1],u=0===u.indexOf("i")?parseInt(u.substr(1)):u.substr(1),r[u]=n},Util.push=function(e,t,n){for(var u,r=e,l=t.split("."),s=0;s<l.length-1;s++)u=l[s],u=0===u.indexOf("i")?parseInt(u.substr(1)):u.substr(1),r=r[u];u=l[l.length-1],u=0===u.indexOf("i")?parseInt(u.substr(1)):u.substr(1),r[u]instanceof Array?r[u].push(n):alert("仅支持数据")},Util.remove=function(e,t){for(var n,u=e,r=t.split("."),l=0;l<r.length-1;l++)n=r[l],n=0===n.indexOf("i")?parseInt(n.substr(1)):n.substr(1),u=u[n];n=r[r.length-1],0===n.indexOf("i")?(n=parseInt(n.substr(1)),u.splice(n,1)):(n=n.substr(1),delete u[n])},Util.fullscreen={enter:function(e){var t=document.documentElement;t.requestFullscreen?(t.requestFullscreen(),setTimeout(function(){e&&e()},1e3)):t.mozRequestFullScreen?(t.mozRequestFullScreen(),setTimeout(function(){e&&e()},1e3)):t.webkitRequestFullScreen?(t.webkitRequestFullScreen(),setTimeout(function(){e&&e()},1e3)):elem.msRequestFullscreen&&(elem.msRequestFullscreen(),setTimeout(function(){e&&e()},1e3))},exit:function(e){document.exitFullscreen?(document.exitFullscreen(),setTimeout(function(){e&&e()},1e3)):document.mozCancelFullScreen?(document.mozCancelFullScreen(),setTimeout(function(){e&&e()},1e3)):document.webkitCancelFullScreen?(document.webkitCancelFullScreen(),setTimeout(function(){e&&e()},1e3)):document.msExitFullscreen&&(document.msExitFullscreen(),setTimeout(function(){e&&e()},1e3))},isFullScreen:function(){return document.exitFullscreen?document.fullscreen:document.mozCancelFullScreen?document.mozFullScreen:document.webkitCancelFullScreen?document.webkitIsFullScreen:!!document.msExitFullscreen&&document.msFullscreenElement},trigger:function(e){Util.fullscreen.isFullScreen()?Util.fullscreen.exit(function(){e("exit")}):Util.fullscreen.enter(function(){e("enter")})}},module.exports=Util;