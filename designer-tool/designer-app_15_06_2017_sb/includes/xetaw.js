var minX = 9999, minY = 9999, maxY = -1, arch = 0, dyt, dyb;
var bounds, angleValue, curveValue, path_str, source, destination,
isTriangle, isBridge, isTop, isBottom, isMiddle;
var path_arr, subpath_type="", is_num, xy_counter, xy,
    path_arr2 = [], subpath_type_upper, point;

self.addEventListener('message', function(e) {
  switch (e.data.method) {
    case 'distort_path':
      distort_path(e.data);
      break;
  };
}, false);


function distort_path(vData)
{
    data = JSON.parse(vData.msg);
    path_str = data.path_str;
    
    if(!path_str) return;

    source = data.source;
    destination = data.destination;
    bounds = data.bounds;
    angleValue = data.angleValue;
    curveValue = data.curveValue;
    isTriangle = data.isTriangle;
    isBridge = data.isBridge;
    isTop = data.isTop;
    isMiddle = data.isMiddle;
    isBottom = data.isBottom;


    path_arr=path_string_to_array(path_str);
    subpath_type = "";
    path_arr2 = [];

    for(var i=0;i<path_arr.length;i++)
    {
      patt1=/[mzlhvcsqta]/i;
      curr=path_arr[i];
      if (curr.toString().match(patt1))
      {
          xy_counter=-1;
          subpath_type=curr;
          subpath_type_upper=subpath_type.toUpperCase();
          is_num=false;
          path_arr2.push(curr);
      }
      else
      {
          is_num=true;
          curr=parseFloat(curr);
      }
      if (xy_counter%2 == 0) xy="x";
      else xy="y";
      if (is_num) // && subpath_type=="q")
      {            
        if(xy=="y")
        {
          if(parseFloat(path_arr[i-1]) < minX) minX = parseFloat(path_arr[i-1]);
          if(curr < minY) minY = curr;
          if(curr > maxY) maxY = curr;
          minY = minY;
          maxY = maxY;
          point=transferPoint(parseFloat(path_arr[i-1]), curr);
          //point={x:parseFloat(path_arr[i-1]),y:curr};
          path_arr2.push(point.x);
          path_arr2.push(point.y);
        }
      }
        xy_counter++;
    }

    //console.log('minY, maxY', minY, maxY);
    path_str=path_array_to_string(path_arr2);

    self.postMessage(path_str);
}

function path_string_to_array(path_str)
{
    var patt1=/[mzlhvcsqta]|-?[0-9.]+/gi;
    var path_arr=path_str.match(patt1);
    patt1=/[mzlhvcsqta]/i;
    for(var i=0;i<path_arr.length;i++)
    {
      if (!path_arr[i].match(patt1))
      {
          path_arr[i]=parseFloat(path_arr[i]);
      }
    }
    return path_arr;
}

function path_array_to_string(path_arr)
{
    var path_str=path_arr.toString();
    path_str=path_str.replace(/([0-9]),([-0-9])/g, "$1 $2");
    path_str=path_str.replace(/([0-9]),([-0-9])/g, "$1 $2"); // for some reason have to do twice
    path_str=path_str.replace(/,/g, "");
    return path_str;
}

function transferPoint(xI, yI)
{
    var anglesteps = (parseInt(angleValue, 10)/bounds.width);  
    var curve = parseInt(curveValue, 10);
    if(isTriangle)
    {
      if(xI > bounds.width/2) 
      {
        dyt = anglesteps*(yI - minY)*(xI - bounds.width)/minY;
        dyb = anglesteps*(yI - maxY)*(xI - bounds.width)/maxY;
        
        if(isTop) dyb = 0;
        if(isBottom) dyt = 0;
        arch = yI - dyt - dyb;  
      }
      else
      {
        dyt = anglesteps*(yI - minY)*(xI)/minY;
        dyb = anglesteps*(yI - maxY)*(xI)/maxY;
        
        if(isTop) dyb = 0;
        if(isBottom) dyt = 0;
        arch = yI + dyt + dyb;  
      }
    }
    else if(isBridge)
    {
      if(angleValue < 0)
      {
        dyt = (yI - minY)*(bounds.bottom/2 - curve * Math.sin(xI * anglesteps * Math.PI/180))/minY;
        dyb = (yI - maxY)*(bounds.bottom/2 - curve * Math.sin(xI * anglesteps * Math.PI/180))/maxY;
      }
      else
      {
        dyt = (yI - minY)*(bounds.bottom - curve * Math.sin(xI * anglesteps * Math.PI/180))/minY;
        dyb = (yI - maxY)*(bounds.bottom - curve * Math.sin(xI * anglesteps * Math.PI/180))/maxY;
      }
      
      if(isTop) dyb = 0;
      if(isBottom) dyt = 0;
      
      arch = yI + dyt + dyb;
      
      if(isMiddle) arch = yI + dyt/2 + dyb/2;
    }
    else
    {
      dyt = (yI - minY)*(bounds.bottom - (bounds.height*2) * Math.sin(xI * anglesteps * Math.PI/180))/minY;
      dyb = (yI - maxY)*(bounds.bottom + (bounds.height*2) * Math.sin(xI * anglesteps * Math.PI/180))/maxY;
      /*var yRatio = yI/minY;
      if(xI > bounds.width/2)
        yRatio = minY/yI;
      var r = yRatio+bounds.width*0.8;
      dyt = yI + Math.sqrt( r*r - (r - xI)*(r - xI) );*/
      
      
      if(isTop) dyb = 0;
      if(isBottom) dyt = 0;
      arch = yI + dyt/2 + dyb/2;
    }
    var b = {x:xI, y:arch/2}; 
    //b.x=Math.round(b.x);
    //b.y=Math.round(b.y);
    return b;
}