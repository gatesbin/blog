!function(e,t,n,a){function l(t,n){this.element=t,this.settings=e.extend({},m,n),this._defaults=m,this._name=c,this.init()}function o(e){return(e.filename?e.filename:"table2excel")+".xlsx"}function i(e){return e.replace(/<img[^>]*>/gi,"")}function s(e){return e.replace(/<A[^>]*>|<\/A>/g,"")}function r(e){return e.replace(/<input[^>]*>|<\/input>/gi,"")}var c="table2excel",m={exclude:".noExl",name:"Table2Excel"};l.prototype={init:function(){var t=this;t.template={head:'<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8">\x3c!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>',sheet:{head:"<x:ExcelWorksheet><x:Name>",tail:"</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>"},mid:"</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--\x3e</head><body>",table:{head:"<table>",tail:"</table>"},foot:"</body></html>"},t.tableRows=[],e(t.element).each(function(n,a){var l="";e(a).find("tr").not(t.settings.exclude).each(function(t,n){l+="<tr>"+e(n).html()+"</tr>"}),t.tableRows.push(l)}),t.settings.exclude_img&&(t.tableRows[0]=i(t.tableRows[0])),t.settings.exclude_links&&(t.tableRows[0]=s(t.tableRows[0])),t.settings.exclude_inputs&&(t.tableRows[0]=r(t.tableRows[0])),t.tableToExcel(t.tableRows,t.settings.name)},tableToExcel:function(a,l){var i,s,r,c=this,m="";if(c.uri="data:application/vnd.ms-excel;base64,",c.base64=function(e){return t.btoa(unescape(encodeURIComponent(e)))},c.format=function(e,t){return e.replace(/{(\w+)}/g,function(e,n){return t[n]})},c.ctx={worksheet:l||"Worksheet",table:a},m=c.template.head,e.isArray(a))for(i in a)m+=c.template.sheet.head+"Table"+i+c.template.sheet.tail;if(m+=c.template.mid,e.isArray(a))for(i in a)m+=c.template.table.head+"{table"+i+"}"+c.template.table.tail;m+=c.template.foot;for(i in a)c.ctx["table"+i]=a[i];if(delete c.ctx.table,"undefined"!=typeof msie&&msie>0||navigator.userAgent.match(/Trident.*rv\:11\./))if("undefined"!=typeof Blob){m=[m];var x=new Blob(m,{type:"text/html"});t.navigator.msSaveBlob(x,o(c.settings))}else txtArea1.document.open("text/html","replace"),txtArea1.document.write(m),txtArea1.document.close(),txtArea1.focus(),sa=txtArea1.document.execCommand("SaveAs",!0,o(c.settings));else s=c.uri+c.base64(c.format(m,c.ctx)),r=n.createElement("a"),r.download=o(c.settings),r.href=s,r.click();return!0}},e.fn[c]=function(t){var n=this;return n.each(function(){e.data(n,"plugin_"+c)||e.data(n,"plugin_"+c,new l(this,t))}),n}}(jQuery,window,document);