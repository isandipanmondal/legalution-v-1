'use strict'
let baseUrl='';
let basePath='backend_v1.php?func=';
$(document).ready(function(){
    getSiteBasePath();
    console.log("SiteBasePath :: ",baseUrl);
});

//basepath retrive
function getSiteBasePath(){
    let htef = location.href;
    baseUrl= htef.substring(0,htef.lastIndexOf('/')+1);
}
//validator function

function doAjaxCall(path,data,callback){
    let url = baseUrl+basePath+path;
    let error=undefined;
    let full_res=undefined;
    $.ajax({
        url:url,
        type:'post',
        dataType:'json',
        //contentType:'application/json;',
        //data:JSON.stringify(data),
        data:data,
        start:function(){
            console.log("start");
        },
        success:function(response){
            error=undefined;
            full_res=response;
        },
        error:function(response){
            console.log("error :: ",response);
            full_res=undefined;
            error = response.status+" : "+response.statusText;
        },
        complete:function(){
            console.log("End");
            callback(error,full_res);
        }
    });
}