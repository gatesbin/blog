var table=function(t,e){var d=$(t);d.html('<div class="advanced-table"></div>'),d=d.find(".advanced-table"),d.append('<div class="advanced-table-data"></div>'),d.append('<div class="advanced-table-head"></div>'),d.append('<div class="advanced-table-column"></div>'),d.append('<div class="advanced-table-column-fixed"></div>'),d.append('<div class="advanced-table-loading"><i class="uk-icon-spinner uk-icon-spin"></i> 正在加载...</div>');var i={loading:d.find(".advanced-table-loading"),head:d.find(".advanced-table-head"),data:d.find(".advanced-table-data"),column:d.find(".advanced-table-column"),columnFixed:d.find(".advanced-table-column-fixed")},a=$.extend({entityCheckbox:!1},e),n=function(){var t=function(){var t=[],e=[];i.data.find("table > thead > tr > th").each(function(e,d){t.push(d.getBoundingClientRect())}),i.data.find("table > tbody > tr").each(function(t,d){e.push(d.getBoundingClientRect())}),i.head.find("table > thead > tr > th > .item").each(function(e,d){$(d).css({width:t[e].width-1+"px",height:t[e].height-1+"px"})}),i.column.find("table > thead > tr > th > .item").each(function(e,d){$(d).css({width:t[e].width-1+"px",height:t[e].height-1+"px"})}),i.columnFixed.find("table > thead > tr > th > .item").each(function(e,d){$(d).css({width:t[e].width-1+"px",height:t[e].height-1+"px"})}),i.column.find("table > tbody > tr").each(function(t,d){$(d).find("td:first > .item").css({height:e[t].height-1+"px"})})};t(),setTimeout(function(){t()},0),$(window).on("resize",function(){t()})};$(window).on("resize",function(){n()});var h=d.width(),l=d.height();setInterval(function(){var t=d.width(),e=d.height();t==h&&e==l||(n(),h=t,l=e)},50),d.on("scroll",function(){var t=d.scrollTop();i.head.css({top:t+"px"});var e=d.scrollLeft();return i.column.css({left:e+"px"}),i.columnFixed.css({top:t+"px",left:e+"px"}),!1}),d.on("change","input[name=__id_all]",function(){var t=$(this).is(":checked");return d.find("input[name=__id_all]").prop("checked",t),$('input[name="__id[]"]').prop("checked",t),!1}),d.on("change",'input[name="__id[]"]',function(){var t=$(this).is(":checked"),e=$(this).attr("value");d.find('input[name="__id[]"][value='+e+"]").prop("checked",t);var a=i.data.find('input[name="__id[]"]').length==i.data.find('input[name="__id[]"]:checked').length;return d.find("input[name=__id_all]").prop("checked",a),!1}),this.loading=function(t){if(t=t||!1){var e=d.scrollTop(),a=d.scrollLeft();i.loading.css({left:a+"px",top:e+"px",width:d.outerWidth()+"px",height:d.outerHeight()+"px"}),i.loading.show(),d.addClass("loading")}else i.loading.hide(),d.removeClass("loading")},this.render=function(t){var e=t.list,d=t.head,h=[],l=0,s=!1,c=0,o=[],u=[];u.push("<table><thead><tr>");var r=[];r.push("<table><thead><tr>");var p=[];p.push("<table><thead><tr>");var v=[];v.push("<table><thead><tr>"),a.entityCheckbox&&(c++,u.push('<th><div class="item"><input type="checkbox" name="__id_all" /></div></th>'),r.push('<th><div class="item"><input type="checkbox" name="__id_all" /></div></th>'),p.push('<th><div class="item"><input type="checkbox" name="__id_all" /></div></th>'),v.push('<th><div class="item"><input type="checkbox" name="__id_all" /></div></th>'),l++,s=!0);for(var f=0;f<d.length;f++)"fixed"in d[f]&&(c++,u.push('<th data-item="'+d[f].field+'" '),r.push('<th data-item="'+d[f].field+'" '),s=!0,o.push(d[f].field)),p.push('<th data-item="'+d[f].field+'" '),v.push('<th data-item="'+d[f].field+'" '),"order"in d[f]&&("fixed"in d[f]&&(u.push(' data-order="'+d[f].order+'" '),r.push(' data-order="'+d[f].order+'" ')),p.push(' data-order="'+d[f].order+'" '),v.push(' data-order="'+d[f].order+'" ')),"attr"in d[f]&&("fixed"in d[f]&&(u.push(d[f].attr+" "),r.push(d[f].attr+" ")),p.push(d[f].attr+" "),v.push(d[f].attr+" ")),"orderAble"in d[f]&&("fixed"in d[f]&&(u.push(" data-orderAble "),r.push(" data-orderAble ")),p.push(" data-orderAble "),v.push(" data-orderAble ")),"fixed"in d[f]&&(u.push('><div class="item">'+d[f].title+"</div></th>"),r.push('><div class="item">'+d[f].title+"</div></th>")),p.push('><div class="item">'+d[f].title+"</div></th>"),v.push('><div class="item">'+d[f].title+"</div></th>"),h.push(d[f].field),l++;if(u.push("</tr></thead><tbody>"),r.push("</tr></thead><tbody>"),p.push("</tr></thead><table>"),v.push("</tr></thead><tbody>"),0==e.length)v.push('<tr><td colspan="'+l+'"><div class="none">暂无数据</div></td></tr>');else for(var f=0;f<e.length;f++){var m=e[f];v.push('<tr data-item-id="'+m._id+'">'),a.entityCheckbox&&(r.push('<td><div class="item"><input type="checkbox" name="__id[]" value="'+m._id+'" /></div></td>'),v.push('<td><div class="item"><input type="checkbox" name="__id[]" value="'+m._id+'" /></div></td>'));for(var b=0;b<h.length;b++)o.indexOf(h[b])>=0&&r.push('<td data-item="'+h[b]+'"><div class="item">'+(m[h[b]]?m[h[b]]:"")+"</div></td>"),v.push('<td data-item="'+h[b]+'"><div class="item">'+(m[h[b]]?m[h[b]]:"")+"</div></td>");r.push("</tr>"),v.push("</tr>")}u.push('<tr><td colspan="'+c+'" style="border:none;"></td></tr></tbody></table>'),r.push("</tbody></table>"),v.push("</tbody></table>"),s?(i.columnFixed.html(u.join("")),i.column.html(r.join(""))):(i.columnFixed.html(""),i.column.html("")),i.head.html(p.join("")),i.data.html(v.join("")),i.data.find("tbody > tr").each(function(t,d){$(d).data("data",e[t])}),n()},this.getSelectedIds=function(){var t={};d.find('input[name="__id[]"]:checked').each(function(e,d){t["id-"+$(d).val()]=!0});var e=[];for(var i in t)e.push(i.substring(3));return e}};module.exports=table;