function zheLoadit(a){var b=document.getElementsByTagName("head")[0],c=document.createElement("script");c.type="text/javascript",c.src=a+"&"+Math.random(),b.appendChild(c)}function zheInsertLoading(){var a=document.getElementsByTagName("body")[0],b=document.createElement("div");b.id="mask_id_dv",a.appendChild(b);var c="utf8";"Netscape"==navigator.appName?"UTF-8"!=document.characterSet&&(c="gbk"):"UTF-8"!=document.charset.toUpperCase()&&(c="gbk"),document.getElementById("mask_id_dv").innerHTML="utf8"==c?'<div style="position:fixed; top:0; left:0; z-index:100000000000; width:100%; height:100%; background:#FFF;text-align:center"><br /><br /><br /><br /><br /><br /><br /><br /><h2 style="height:30px">\u8df3\u8f6c\u4e2d\uff0c\u8bf7\u7a0d\u7b49 ...</h2><img src="'+zheDomain+'/loading.gif"></div>':'<div style="position:fixed; top:0; left:0; z-index:100000000000; width:100%; height:100%; background:#FFF;text-align:center"><br /><br /><br /><br /><br /><br /><br /><br /><h2 style="height:30px">Loading ...</h2><img src="'+zheDomain+'/loading.gif"></div>'}function zheGetConvert(a){window.alimamatk_show&&a&&KSLITE&&(window.iid=a,KSLITE.provide(["tkapi-main"],function(a){function c(a,c){c&&b.push(a+(a?"=":"")+c)}if(a("tkapi-config").r.cache.et){var b=[];call_back_fun="jsonp_callback_"+Math.random().toString().replace(".",""),c("cb",call_back_fun),c("ak",a("tkapi-config").r.cache.ak),c("pid",a("tkapi-config").r.cache.pid),c("unid","0"),c("wt","0"),c("tl","290x380"),c("rd","1"),c("ct",encodeURIComponent("itemid="+window.iid)),c("rf",encodeURIComponent(a("tkapi-config").r.cache.ref)),c("et",a("tkapi-config").r.cache.et),c("pgid",a("tkapi-config").r.cache.pgid),c("v","1.1"),zheGetConvertCB(a("tkapi-config").c.alimama+"q?"+b.join("&"))}}))}function zheGetConvertCB(a){window.zheHasRequesResult=!0,zheLoadit(zheDomain+"/api/getTaskResultJs/"+zheArgs.taskid+"?link_origin="+encodeURIComponent(a)+"&debug=false")}function loadForceJump(){zheLoadit(zheDomain+"/api/getTaskResultJs/"+zheArgs.taskid+"?link_origin=give_up&force=1&debug=false")}function zhePasteUrl(a){var b=new Object;if(a)var c=a.substring(a.indexOf("?")+1);else var c=location.search.substring(1);for(var d=c.split("&"),e=0;e<d.length;e++){var f=d[e].indexOf("=");if(-1!=f){var g=d[e].substring(0,f),h=d[e].substring(f+1);h=decodeURIComponent(h),b[g]=h&&"undefined"!=h?h:0}}return b}var zheDomain="http://www.jumper.com",zheHost=location.host,zheHref=location.href,zheArgs=zhePasteUrl("");is_ie=navigator.userAgent.indexOf("MSIE")>0?!0:!1;var zheCount=1;if(window.zheHasRequesResult=!1,"fun.51fanli.com"==zheHost&&zheHref.indexOf("goshopapi")>0&&(zheInsertLoading(),document.getElementsByTagName("div")[0].style.display="none",document.getElementsByTagName("div")[1].style.display="none"),"fun.51fanli.com"==zheHost&&zheHref.indexOf("goshop")>0){document.getElementsByClassName("fanli-logo")[0].style.display="none";var i=setInterval(function(){if(0==window.zheHasRequesResult&&window.top.document.getElementById("writeable_iframe_0")){var a=window.top.document.getElementById("writeable_iframe_0").contentWindow;objs=a.document.getElementsByTagName("a");for(i in objs)objs[i].href.indexOf("redirect.simba.taobao.com")>0&&(objs[i].click(),window.zheHasRequesResult=!0)}},100)}else if("www.baobeisha.com"==zheHost&&zheHref.indexOf("taskid")>0){zheInsertLoading();var i=setInterval(function(){zheCount+=1,50==zheCount&&(loadForceJump(),clearInterval(i)),obj=$(".goto a")[0];try{var a=document.createEvent("HTMLEvents");a.initEvent("mousedown",!0,!0),a.eventType="message",obj.dispatchEvent(a)}catch(b){var a=document.createEventObject();a.eventType="message",a.srcElement=obj,obj.fireEvent("onmousedown",a)}var c=$(".goto a").attr("href");-1!=c.indexOf("g.click.taobao.com")&&0==window.zheHasRequesResult&&(zheLoadit(zheDomain+"/api/getTaskResultJs/"+zheArgs.taskid+"?link_origin="+encodeURIComponent(c)+"&debug=false"),clearInterval(i),window.zheHasRequesResult=!0)},100)}else if("www.jsfanli.com"==zheHost&&zheHref.indexOf("taskid")>0){zheInsertLoading();var i=setInterval(function(){zheCount+=1,50==zheCount&&(loadForceJump(),clearInterval(i)),obj=$(".goto a")[0];try{var a=document.createEvent("HTMLEvents");a.initEvent("mousedown",!0,!0),a.eventType="message",obj.dispatchEvent(a)}catch(b){var a=document.createEventObject();a.eventType="message",a.srcElement=obj,obj.fireEvent("onmousedown",a)}var c=$(".goto a").attr("href");-1!=c.indexOf("g.click.taobao.com")&&0==window.zheHasRequesResult&&(zheLoadit(zheDomain+"/api/getTaskResultJs/"+zheArgs.taskid+"?link_origin="+encodeURIComponent(c)+"&debug=false"),clearInterval(i),window.zheHasRequesResult=!0)},100)}else if("fanli.juanpi.com"==zheHost&&zheHref.indexOf("taskid")>0){zheInsertLoading();var i=setInterval(function(){zheCount+=1,50==zheCount&&(loadForceJump(),clearInterval(i)),obj=$(".fan_box a")[0];try{var a=document.createEvent("HTMLEvents");a.initEvent("mousedown",!0,!0),a.eventType="message",obj.dispatchEvent(a)}catch(b){var a=document.createEventObject();a.eventType="message",a.srcElement=obj,obj.fireEvent("onmousedown",a)}var c=$(".fan_box a").attr("href");-1!=c.indexOf("g.click.taobao.com")&&0==window.zheHasRequesResult&&(zheLoadit(zheDomain+"/api/getTaskResultJs/"+zheArgs.taskid+"?link_origin="+encodeURIComponent(c)+"&debug=false"),clearInterval(i),window.zheHasRequesResult=!0)},100)}else if("www.fanxian.com"==zheHost&&zheHref.indexOf("taskid")>0){zheInsertLoading();var i=setInterval(function(){zheCount+=1,100==zheCount&&(loadForceJump(),clearInterval(i)),0==window.zheHasRequesResult&&zheGetConvert(zheArgs.gid,i)},100)}else if("re.taobao.com"==zheHost&&zheHref.indexOf("unid")>0){try{obj=document.getElementsByClassName("btnBuy")}catch(e){obj=document.querySelectorAll(".btnBuy")}obj[0].target="_self",obj[0].click()}