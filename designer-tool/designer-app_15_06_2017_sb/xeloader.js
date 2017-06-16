 
 RIAXEAPP.productInfo ={};

function xegetParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(parent.location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getLocation(href) {
    var l = document.createElement("a");
    l.href = href;
    return l;
}

function xecheckBrowserCompatibility(){

      try{
          var url = document.URL, 
          shortUrl=url.substring(0,url.lastIndexOf("/")),
          platformName = platform.name.toLowerCase(),
          isBrowserCompatible = 0, 
          brwsrList = RIAXEAPP.localSettings.compatible_browsers;
       
        for (var idx=0; idx<brwsrList.length; idx++) {
              if (platformName.search(brwsrList[idx]) !=-1){
                isBrowserCompatible = 1;
                break;
              }
                  
          }
          if(!isBrowserCompatible)
            document.location.href= shortUrl+'/browsererror.html';


      }
      catch(err) {

      }
}
xecheckBrowserCompatibility();
xeloadProduct();

function xeloadProduct()
{
  var baseUrl = "";
  if(RIAXEAPP.localSettings.enviroment == "dev") {
    baseUrl = getLocation(RIAXEAPP.localSettings.base_url);
  }else {
    var folderName = RIAXEAPP.localSettings.base_url.split("/");
    if(folderName[3])
      baseUrl = window.location.protocol + "//" + window.location.hostname + "/" + folderName[3] + "/";
    else
      baseUrl = window.location.protocol + "//" + window.location.hostname + "/";
	baseUrl = getLocation(baseUrl);
  }
  var assetsPath = baseUrl.hostname.replace(/\./g, '_');
  assetsPath = assetsPath.replace(/www_/g, '');
  RIAXEAPP.localSettings.webfonts_path = "designer-tool/designer-assets/"+assetsPath+"/images/fonts/";
  RIAXEAPP.localSettings.patterns_path = "designer-tool/designer-assets/"+assetsPath+"/images/palettes/";
  RIAXEAPP.localSettings.store_api_path = "designer-tool/designer-assets/"+assetsPath+"/images/";
  
  if(xegetParameterByName('pid')!= "") var config_id = xegetParameterByName('pid');
  else var config_id = xegetParameterByName('id');
  if(xegetParameterByName('simplePdctId')!= "") var product_id = xegetParameterByName('simplePdctId');
  else if(xegetParameterByName('pvid')!= "") var product_id = xegetParameterByName('pvid');
  else var product_id = config_id;
  var productTemplate_id = xegetParameterByName('sid');
   var product_fetch_url =   RIAXEAPP.localSettings.base_url 
                            + RIAXEAPP.localSettings.service_api_url 
                            + '?' 
                            + "reqmethod=getSimpleProductClient&id=" 
                            + product_id
                            + "&apikey="
                            + encodeURIComponent(RIAXEAPP.localSettings.api_key)
                            + "&confId="
                            + config_id;
        if(config_id) {
          $.getJSON(product_fetch_url,
              function(data) {
                var firstside = [];
                RIAXEAPP.productInfo = data;
                firstside[0] = RIAXEAPP.productInfo.sides[0];
                xepreloadImages(firstside);
                xepreloadImages(RIAXEAPP.productInfo.thumbsides);
          });
        }
}

   function embedIframe() 
    {   
            var loc = $(location)[0].href;
            if(loc.indexOf("index.html">0))
            {
                var loc1 = loc.split("/index.html");
                loc = loc1[0];
            }

          RIAXEAPP.embedURL = loc + '/xeeditor/index.html';

            /*$('<iframe>', {
               src:  loc + '/xeeditor/index.html',
               id:  'iframe_svgedit',
               frameborder:'no' ,
               onload:'initIframeEmbed()',
               width:'500',
               height:'500',
               scrolling: 'no'
               }).prependTo('body');*/
 
           
    }


 /*   function initIframeEmbed(){
        initSVGIFrame = setInterval(function(){
               if(typeof  initIframe == 'function'){
                       initIframe();
                      clearInterval(initSVGIFrame);
                  }
          },50);
    }*/

    embedIframe();


 

function xepreloadImages(array) {
    if (!xepreloadImages.list) {
        xepreloadImages.list = [];
    }
    var list = xepreloadImages.list;
    for (var i = 0; i < array.length; i++) {
        var img = new Image();
        img.onload = function() {
            var index = list.indexOf(this);
            if (index !== -1) {
                // remove image from the array once it's loaded
                // for memory consumption reasons
                list.splice(index, 1);
            }
        }
        list.push(img);
        img.src = array[i];
    }
}
