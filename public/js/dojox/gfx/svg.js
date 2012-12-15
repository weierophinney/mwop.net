//>>built
define("dojox/gfx/svg",["dojo/_base/lang","dojo/_base/window","dojo/dom","dojo/_base/declare","dojo/_base/array","dojo/dom-geometry","dojo/dom-attr","dojo/_base/Color","./_base","./shape","./path"],function(_1,_2,_3,_4,_5,_6,_7,_8,g,gs,_9){
var _a=g.svg={};
_a.useSvgWeb=(typeof window.svgweb!="undefined");
var _b=navigator.userAgent.toLowerCase(),_c=_b.search("iphone")>-1||_b.search("ipad")>-1||_b.search("ipod")>-1,_d=parseFloat(_b.split("android ")[1]),_e=(!_d||_d<4)?"optimizeLegibility":"auto";
function _f(ns,_10){
if(_2.doc.createElementNS){
return _2.doc.createElementNS(ns,_10);
}else{
return _2.doc.createElement(_10);
}
};
function _11(_12,ns,_13,_14){
if(_12.setAttributeNS){
return _12.setAttributeNS(ns,_13,_14);
}else{
return _12.setAttribute(_13,_14);
}
};
function _15(_16){
if(_a.useSvgWeb){
return _2.doc.createTextNode(_16,true);
}else{
return _2.doc.createTextNode(_16);
}
};
function _17(){
if(_a.useSvgWeb){
return _2.doc.createDocumentFragment(true);
}else{
return _2.doc.createDocumentFragment();
}
};
_a.xmlns={xlink:"http://www.w3.org/1999/xlink",svg:"http://www.w3.org/2000/svg"};
_a.getRef=function(_18){
if(!_18||_18=="none"){
return null;
}
if(_18.match(/^url\(#.+\)$/)){
return _3.byId(_18.slice(5,-1));
}
if(_18.match(/^#dojoUnique\d+$/)){
return _3.byId(_18.slice(1));
}
return null;
};
_a.dasharray={solid:"none",shortdash:[4,1],shortdot:[1,1],shortdashdot:[4,1,1,1],shortdashdotdot:[4,1,1,1,1,1],dot:[1,3],dash:[4,3],longdash:[8,3],dashdot:[4,3,1,3],longdashdot:[8,3,1,3],longdashdotdot:[8,3,1,3,1,3]};
var _19=0;
_a.Shape=_4("dojox.gfx.svg.Shape",gs.Shape,{destroy:function(){
if(this.fillStyle&&"type" in this.fillStyle){
var _1a=this.rawNode.getAttribute("fill"),ref=_a.getRef(_1a);
if(ref){
ref.parentNode.removeChild(ref);
}
}
if(this.clip){
var _1b=this.rawNode.getAttribute("clip-path");
if(_1b){
var _1c=_3.byId(_1b.match(/gfx_clip[\d]+/)[0]);
_1c&&_1c.parentNode.removeChild(_1c);
}
}
this.rawNode=null;
gs.Shape.prototype.destroy.apply(this,arguments);
},setFill:function(_1d){
if(!_1d){
this.fillStyle=null;
this.rawNode.setAttribute("fill","none");
this.rawNode.setAttribute("fill-opacity",0);
return this;
}
var f;
var _1e=function(x){
this.setAttribute(x,f[x].toFixed(8));
};
if(typeof (_1d)=="object"&&"type" in _1d){
switch(_1d.type){
case "linear":
f=g.makeParameters(g.defaultLinearGradient,_1d);
var _1f=this._setFillObject(f,"linearGradient");
_5.forEach(["x1","y1","x2","y2"],_1e,_1f);
break;
case "radial":
f=g.makeParameters(g.defaultRadialGradient,_1d);
var _20=this._setFillObject(f,"radialGradient");
_5.forEach(["cx","cy","r"],_1e,_20);
break;
case "pattern":
f=g.makeParameters(g.defaultPattern,_1d);
var _21=this._setFillObject(f,"pattern");
_5.forEach(["x","y","width","height"],_1e,_21);
break;
}
this.fillStyle=f;
return this;
}
f=g.normalizeColor(_1d);
this.fillStyle=f;
this.rawNode.setAttribute("fill",f.toCss());
this.rawNode.setAttribute("fill-opacity",f.a);
this.rawNode.setAttribute("fill-rule","evenodd");
return this;
},setStroke:function(_22){
var rn=this.rawNode;
if(!_22){
this.strokeStyle=null;
rn.setAttribute("stroke","none");
rn.setAttribute("stroke-opacity",0);
return this;
}
if(typeof _22=="string"||_1.isArray(_22)||_22 instanceof _8){
_22={color:_22};
}
var s=this.strokeStyle=g.makeParameters(g.defaultStroke,_22);
s.color=g.normalizeColor(s.color);
if(s){
rn.setAttribute("stroke",s.color.toCss());
rn.setAttribute("stroke-opacity",s.color.a);
rn.setAttribute("stroke-width",s.width);
rn.setAttribute("stroke-linecap",s.cap);
if(typeof s.join=="number"){
rn.setAttribute("stroke-linejoin","miter");
rn.setAttribute("stroke-miterlimit",s.join);
}else{
rn.setAttribute("stroke-linejoin",s.join);
}
var da=s.style.toLowerCase();
if(da in _a.dasharray){
da=_a.dasharray[da];
}
if(da instanceof Array){
da=_1._toArray(da);
for(var i=0;i<da.length;++i){
da[i]*=s.width;
}
if(s.cap!="butt"){
for(var i=0;i<da.length;i+=2){
da[i]-=s.width;
if(da[i]<1){
da[i]=1;
}
}
for(var i=1;i<da.length;i+=2){
da[i]+=s.width;
}
}
da=da.join(",");
}
rn.setAttribute("stroke-dasharray",da);
rn.setAttribute("dojoGfxStrokeStyle",s.style);
}
return this;
},_getParentSurface:function(){
var _23=this.parent;
for(;_23&&!(_23 instanceof g.Surface);_23=_23.parent){
}
return _23;
},_setFillObject:function(f,_24){
var _25=_a.xmlns.svg;
this.fillStyle=f;
var _26=this._getParentSurface(),_27=_26.defNode,_28=this.rawNode.getAttribute("fill"),ref=_a.getRef(_28);
if(ref){
_28=ref;
if(_28.tagName.toLowerCase()!=_24.toLowerCase()){
var id=_28.id;
_28.parentNode.removeChild(_28);
_28=_f(_25,_24);
_28.setAttribute("id",id);
_27.appendChild(_28);
}else{
while(_28.childNodes.length){
_28.removeChild(_28.lastChild);
}
}
}else{
_28=_f(_25,_24);
_28.setAttribute("id",g._base._getUniqueId());
_27.appendChild(_28);
}
if(_24=="pattern"){
_28.setAttribute("patternUnits","userSpaceOnUse");
var img=_f(_25,"image");
img.setAttribute("x",0);
img.setAttribute("y",0);
img.setAttribute("width",f.width.toFixed(8));
img.setAttribute("height",f.height.toFixed(8));
_11(img,_a.xmlns.xlink,"xlink:href",f.src);
_28.appendChild(img);
}else{
_28.setAttribute("gradientUnits","userSpaceOnUse");
for(var i=0;i<f.colors.length;++i){
var c=f.colors[i],t=_f(_25,"stop"),cc=c.color=g.normalizeColor(c.color);
t.setAttribute("offset",c.offset.toFixed(8));
t.setAttribute("stop-color",cc.toCss());
t.setAttribute("stop-opacity",cc.a);
_28.appendChild(t);
}
}
this.rawNode.setAttribute("fill","url(#"+_28.getAttribute("id")+")");
this.rawNode.removeAttribute("fill-opacity");
this.rawNode.setAttribute("fill-rule","evenodd");
return _28;
},_applyTransform:function(){
var _29=this.matrix;
if(_29){
var tm=this.matrix;
this.rawNode.setAttribute("transform","matrix("+tm.xx.toFixed(8)+","+tm.yx.toFixed(8)+","+tm.xy.toFixed(8)+","+tm.yy.toFixed(8)+","+tm.dx.toFixed(8)+","+tm.dy.toFixed(8)+")");
}else{
this.rawNode.removeAttribute("transform");
}
return this;
},setRawNode:function(_2a){
var r=this.rawNode=_2a;
if(this.shape.type!="image"){
r.setAttribute("fill","none");
}
r.setAttribute("fill-opacity",0);
r.setAttribute("stroke","none");
r.setAttribute("stroke-opacity",0);
r.setAttribute("stroke-width",1);
r.setAttribute("stroke-linecap","butt");
r.setAttribute("stroke-linejoin","miter");
r.setAttribute("stroke-miterlimit",4);
r.__gfxObject__=this.getUID();
},setShape:function(_2b){
this.shape=g.makeParameters(this.shape,_2b);
for(var i in this.shape){
if(i!="type"){
this.rawNode.setAttribute(i,this.shape[i]);
}
}
this.bbox=null;
return this;
},_moveToFront:function(){
this.rawNode.parentNode.appendChild(this.rawNode);
return this;
},_moveToBack:function(){
this.rawNode.parentNode.insertBefore(this.rawNode,this.rawNode.parentNode.firstChild);
return this;
},setClip:function(_2c){
this.inherited(arguments);
var _2d=_2c?"width" in _2c?"rect":"cx" in _2c?"ellipse":"points" in _2c?"polyline":"d" in _2c?"path":null:null;
if(_2c&&!_2d){
return this;
}
if(_2d==="polyline"){
_2c=_1.clone(_2c);
_2c.points=_2c.points.join(",");
}
var _2e,_2f,_30=_7.get(this.rawNode,"clip-path");
if(_30){
_2e=_3.byId(_30.match(/gfx_clip[\d]+/)[0]);
if(_2e){
_2e.removeChild(_2e.childNodes[0]);
}
}
if(_2c){
if(_2e){
_2f=_f(_a.xmlns.svg,_2d);
_2e.appendChild(_2f);
}else{
var _31=++_19;
var _32="gfx_clip"+_31;
var _33="url(#"+_32+")";
this.rawNode.setAttribute("clip-path",_33);
_2e=_f(_a.xmlns.svg,"clipPath");
_2f=_f(_a.xmlns.svg,_2d);
_2e.appendChild(_2f);
this.rawNode.parentNode.appendChild(_2e);
_7.set(_2e,"id",_32);
}
_7.set(_2f,_2c);
}else{
this.rawNode.removeAttribute("clip-path");
if(_2e){
_2e.parentNode.removeChild(_2e);
}
}
return this;
},_removeClipNode:function(){
var _34,_35=_7.get(this.rawNode,"clip-path");
if(_35){
_34=_3.byId(_35.match(/gfx_clip[\d]+/)[0]);
if(_34){
_34.parentNode.removeChild(_34);
}
}
return _34;
}});
_a.Group=_4("dojox.gfx.svg.Group",_a.Shape,{constructor:function(){
gs.Container._init.call(this);
},setRawNode:function(_36){
this.rawNode=_36;
this.rawNode.__gfxObject__=this.getUID();
},destroy:function(){
this.clear(true);
_a.Shape.prototype.destroy.apply(this,arguments);
}});
_a.Group.nodeType="g";
_a.Rect=_4("dojox.gfx.svg.Rect",[_a.Shape,gs.Rect],{setShape:function(_37){
this.shape=g.makeParameters(this.shape,_37);
this.bbox=null;
for(var i in this.shape){
if(i!="type"&&i!="r"){
this.rawNode.setAttribute(i,this.shape[i]);
}
}
if(this.shape.r!=null){
this.rawNode.setAttribute("ry",this.shape.r);
this.rawNode.setAttribute("rx",this.shape.r);
}
return this;
}});
_a.Rect.nodeType="rect";
_a.Ellipse=_4("dojox.gfx.svg.Ellipse",[_a.Shape,gs.Ellipse],{});
_a.Ellipse.nodeType="ellipse";
_a.Circle=_4("dojox.gfx.svg.Circle",[_a.Shape,gs.Circle],{});
_a.Circle.nodeType="circle";
_a.Line=_4("dojox.gfx.svg.Line",[_a.Shape,gs.Line],{});
_a.Line.nodeType="line";
_a.Polyline=_4("dojox.gfx.svg.Polyline",[_a.Shape,gs.Polyline],{setShape:function(_38,_39){
if(_38&&_38 instanceof Array){
this.shape=g.makeParameters(this.shape,{points:_38});
if(_39&&this.shape.points.length){
this.shape.points.push(this.shape.points[0]);
}
}else{
this.shape=g.makeParameters(this.shape,_38);
}
this.bbox=null;
this._normalizePoints();
var _3a=[],p=this.shape.points;
for(var i=0;i<p.length;++i){
_3a.push(p[i].x.toFixed(8),p[i].y.toFixed(8));
}
this.rawNode.setAttribute("points",_3a.join(" "));
return this;
}});
_a.Polyline.nodeType="polyline";
_a.Image=_4("dojox.gfx.svg.Image",[_a.Shape,gs.Image],{setShape:function(_3b){
this.shape=g.makeParameters(this.shape,_3b);
this.bbox=null;
var _3c=this.rawNode;
for(var i in this.shape){
if(i!="type"&&i!="src"){
_3c.setAttribute(i,this.shape[i]);
}
}
_3c.setAttribute("preserveAspectRatio","none");
_11(_3c,_a.xmlns.xlink,"xlink:href",this.shape.src);
_3c.__gfxObject__=this.getUID();
return this;
}});
_a.Image.nodeType="image";
_a.Text=_4("dojox.gfx.svg.Text",[_a.Shape,gs.Text],{setShape:function(_3d){
this.shape=g.makeParameters(this.shape,_3d);
this.bbox=null;
var r=this.rawNode,s=this.shape;
r.setAttribute("x",s.x);
r.setAttribute("y",s.y);
r.setAttribute("text-anchor",s.align);
r.setAttribute("text-decoration",s.decoration);
r.setAttribute("rotate",s.rotated?90:0);
r.setAttribute("kerning",s.kerning?"auto":0);
r.setAttribute("text-rendering",_e);
if(r.firstChild){
r.firstChild.nodeValue=s.text;
}else{
r.appendChild(_15(s.text));
}
return this;
},getTextWidth:function(){
var _3e=this.rawNode,_3f=_3e.parentNode,_40=_3e.cloneNode(true);
_40.style.visibility="hidden";
var _41=0,_42=_40.firstChild.nodeValue;
_3f.appendChild(_40);
if(_42!=""){
while(!_41){
if(_40.getBBox){
_41=parseInt(_40.getBBox().width);
}else{
_41=68;
}
}
}
_3f.removeChild(_40);
return _41;
}});
_a.Text.nodeType="text";
_a.Path=_4("dojox.gfx.svg.Path",[_a.Shape,_9.Path],{_updateWithSegment:function(_43){
this.inherited(arguments);
if(typeof (this.shape.path)=="string"){
this.rawNode.setAttribute("d",this.shape.path);
}
},setShape:function(_44){
this.inherited(arguments);
if(this.shape.path){
this.rawNode.setAttribute("d",this.shape.path);
}else{
this.rawNode.removeAttribute("d");
}
return this;
}});
_a.Path.nodeType="path";
_a.TextPath=_4("dojox.gfx.svg.TextPath",[_a.Shape,_9.TextPath],{_updateWithSegment:function(_45){
this.inherited(arguments);
this._setTextPath();
},setShape:function(_46){
this.inherited(arguments);
this._setTextPath();
return this;
},_setTextPath:function(){
if(typeof this.shape.path!="string"){
return;
}
var r=this.rawNode;
if(!r.firstChild){
var tp=_f(_a.xmlns.svg,"textPath"),tx=_15("");
tp.appendChild(tx);
r.appendChild(tp);
}
var ref=r.firstChild.getAttributeNS(_a.xmlns.xlink,"href"),_47=ref&&_a.getRef(ref);
if(!_47){
var _48=this._getParentSurface();
if(_48){
var _49=_48.defNode;
_47=_f(_a.xmlns.svg,"path");
var id=g._base._getUniqueId();
_47.setAttribute("id",id);
_49.appendChild(_47);
_11(r.firstChild,_a.xmlns.xlink,"xlink:href","#"+id);
}
}
if(_47){
_47.setAttribute("d",this.shape.path);
}
},_setText:function(){
var r=this.rawNode;
if(!r.firstChild){
var tp=_f(_a.xmlns.svg,"textPath"),tx=_15("");
tp.appendChild(tx);
r.appendChild(tp);
}
r=r.firstChild;
var t=this.text;
r.setAttribute("alignment-baseline","middle");
switch(t.align){
case "middle":
r.setAttribute("text-anchor","middle");
r.setAttribute("startOffset","50%");
break;
case "end":
r.setAttribute("text-anchor","end");
r.setAttribute("startOffset","100%");
break;
default:
r.setAttribute("text-anchor","start");
r.setAttribute("startOffset","0%");
break;
}
r.setAttribute("baseline-shift","0.5ex");
r.setAttribute("text-decoration",t.decoration);
r.setAttribute("rotate",t.rotated?90:0);
r.setAttribute("kerning",t.kerning?"auto":0);
r.firstChild.data=t.text;
}});
_a.TextPath.nodeType="text";
var _4a=(function(){
var _4b=/WebKit\/(\d*)/.exec(_b);
return _4b?_4b[1]:0;
})()>534;
_a.Surface=_4("dojox.gfx.svg.Surface",gs.Surface,{constructor:function(){
gs.Container._init.call(this);
},destroy:function(){
gs.Container.clear.call(this,true);
this.defNode=null;
this.inherited(arguments);
},setDimensions:function(_4c,_4d){
if(!this.rawNode){
return this;
}
this.rawNode.setAttribute("width",_4c);
this.rawNode.setAttribute("height",_4d);
if(_4a){
this.rawNode.style.width=_4c;
this.rawNode.style.height=_4d;
}
return this;
},getDimensions:function(){
var t=this.rawNode?{width:g.normalizedLength(this.rawNode.getAttribute("width")),height:g.normalizedLength(this.rawNode.getAttribute("height"))}:null;
return t;
}});
_a.createSurface=function(_4e,_4f,_50){
var s=new _a.Surface();
s.rawNode=_f(_a.xmlns.svg,"svg");
s.rawNode.setAttribute("overflow","hidden");
if(_4f){
s.rawNode.setAttribute("width",_4f);
}
if(_50){
s.rawNode.setAttribute("height",_50);
}
var _51=_f(_a.xmlns.svg,"defs");
s.rawNode.appendChild(_51);
s.defNode=_51;
s._parent=_3.byId(_4e);
s._parent.appendChild(s.rawNode);
return s;
};
var _52={_setFont:function(){
var f=this.fontStyle;
this.rawNode.setAttribute("font-style",f.style);
this.rawNode.setAttribute("font-variant",f.variant);
this.rawNode.setAttribute("font-weight",f.weight);
this.rawNode.setAttribute("font-size",f.size);
this.rawNode.setAttribute("font-family",f.family);
}};
var C=gs.Container,_53={openBatch:function(){
this.fragment=_17();
},closeBatch:function(){
if(this.fragment){
this.rawNode.appendChild(this.fragment);
delete this.fragment;
}
},add:function(_54){
if(this!=_54.getParent()){
if(this.fragment){
this.fragment.appendChild(_54.rawNode);
}else{
this.rawNode.appendChild(_54.rawNode);
}
C.add.apply(this,arguments);
_54.setClip(_54.clip);
}
return this;
},remove:function(_55,_56){
if(this==_55.getParent()){
if(this.rawNode==_55.rawNode.parentNode){
this.rawNode.removeChild(_55.rawNode);
}
if(this.fragment&&this.fragment==_55.rawNode.parentNode){
this.fragment.removeChild(_55.rawNode);
}
_55._removeClipNode();
C.remove.apply(this,arguments);
}
return this;
},clear:function(){
var r=this.rawNode;
while(r.lastChild){
r.removeChild(r.lastChild);
}
var _57=this.defNode;
if(_57){
while(_57.lastChild){
_57.removeChild(_57.lastChild);
}
r.appendChild(_57);
}
return C.clear.apply(this,arguments);
},getBoundingBox:C.getBoundingBox,_moveChildToFront:C._moveChildToFront,_moveChildToBack:C._moveChildToBack};
var _58={createObject:function(_59,_5a){
if(!this.rawNode){
return null;
}
var _5b=new _59(),_5c=_f(_a.xmlns.svg,_59.nodeType);
_5b.setRawNode(_5c);
_5b.setShape(_5a);
this.add(_5b);
return _5b;
}};
_1.extend(_a.Text,_52);
_1.extend(_a.TextPath,_52);
_1.extend(_a.Group,_53);
_1.extend(_a.Group,gs.Creator);
_1.extend(_a.Group,_58);
_1.extend(_a.Surface,_53);
_1.extend(_a.Surface,gs.Creator);
_1.extend(_a.Surface,_58);
_a.fixTarget=function(_5d,_5e){
if(!_5d.gfxTarget){
if(_c&&_5d.target.wholeText){
_5d.gfxTarget=gs.byId(_5d.target.parentElement.__gfxObject__);
}else{
_5d.gfxTarget=gs.byId(_5d.target.__gfxObject__);
}
}
return true;
};
if(_a.useSvgWeb){
_a.createSurface=function(_5f,_60,_61){
var s=new _a.Surface();
if(!_60||!_61){
var pos=_6.position(_5f);
_60=_60||pos.w;
_61=_61||pos.h;
}
_5f=_3.byId(_5f);
var id=_5f.id?_5f.id+"_svgweb":g._base._getUniqueId();
var _62=_f(_a.xmlns.svg,"svg");
_62.id=id;
_62.setAttribute("width",_60);
_62.setAttribute("height",_61);
svgweb.appendChild(_62,_5f);
_62.addEventListener("SVGLoad",function(){
s.rawNode=this;
s.isLoaded=true;
var _63=_f(_a.xmlns.svg,"defs");
s.rawNode.appendChild(_63);
s.defNode=_63;
if(s.onLoad){
s.onLoad(s);
}
},false);
s.isLoaded=false;
return s;
};
_a.Surface.extend({destroy:function(){
var _64=this.rawNode;
svgweb.removeChild(_64,_64.parentNode);
}});
var _65={connect:function(_66,_67,_68){
if(_66.substring(0,2)==="on"){
_66=_66.substring(2);
}
if(arguments.length==2){
_68=_67;
}else{
_68=_1.hitch(_67,_68);
}
this.getEventSource().addEventListener(_66,_68,false);
return [this,_66,_68];
},disconnect:function(_69){
this.getEventSource().removeEventListener(_69[1],_69[2],false);
delete _69[0];
}};
_1.extend(_a.Shape,_65);
_1.extend(_a.Surface,_65);
}
return _a;
});
