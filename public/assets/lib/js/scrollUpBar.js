!function(t){"use strict";t.fn.scrollupbar=function(){var s,o=t(window),i=t(document),e=this,n=e.outerHeight(),c=0,p=!1;return o.scroll(function(){var t=o.scrollTop();s&&clearTimeout(s),t<0||t>i.height()-o.height()||(t<c?(p||(p=!0,t>n&&e.css("top",t-n)),parseInt(e.css("top"))>t&&e.css({position:"fixed",top:0}),s=setTimeout(function(){t<parseInt(e.css("top"))+n&&(e.css({position:"fixed",top:parseInt(e.css("top"))-t}),e.animate({top:0},100))},400)):(p=!1,"fixed"==e.css("position")&&e.css({position:"absolute",top:t}),s=setTimeout(function(){t<parseInt(e.css("top"))+n&&t>n&&e.animate({top:t-n},100)},400)),c=t)}),this}}(jQuery);