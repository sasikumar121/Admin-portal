(function (lib, img, cjs, ss, an) {

var p; // shortcut to reference prototypes
lib.webFontTxtInst = {}; 
var loadedTypekitCount = 0;
var loadedGoogleCount = 0;
var gFontsUpdateCacheList = [];
var tFontsUpdateCacheList = [];
lib.ssMetadata = [];



lib.updateListCache = function (cacheList) {		
	for(var i = 0; i < cacheList.length; i++) {		
		if(cacheList[i].cacheCanvas)		
			cacheList[i].updateCache();		
	}		
};		

lib.addElementsToCache = function (textInst, cacheList) {		
	var cur = textInst;		
	while(cur != exportRoot) {		
		if(cacheList.indexOf(cur) != -1)		
			break;		
		cur = cur.parent;		
	}		
	if(cur != exportRoot) {		
		var cur2 = textInst;		
		var index = cacheList.indexOf(cur);		
		while(cur2 != cur) {		
			cacheList.splice(index, 0, cur2);		
			cur2 = cur2.parent;		
			index++;		
		}		
	}		
	else {		
		cur = textInst;		
		while(cur != exportRoot) {		
			cacheList.push(cur);		
			cur = cur.parent;		
		}		
	}		
};		

lib.gfontAvailable = function(family, totalGoogleCount) {		
	lib.properties.webfonts[family] = true;		
	var txtInst = lib.webFontTxtInst && lib.webFontTxtInst[family] || [];		
	for(var f = 0; f < txtInst.length; ++f)		
		lib.addElementsToCache(txtInst[f], gFontsUpdateCacheList);		

	loadedGoogleCount++;		
	if(loadedGoogleCount == totalGoogleCount) {		
		lib.updateListCache(gFontsUpdateCacheList);		
	}		
};		

lib.tfontAvailable = function(family, totalTypekitCount) {		
	lib.properties.webfonts[family] = true;		
	var txtInst = lib.webFontTxtInst && lib.webFontTxtInst[family] || [];		
	for(var f = 0; f < txtInst.length; ++f)		
		lib.addElementsToCache(txtInst[f], tFontsUpdateCacheList);		

	loadedTypekitCount++;		
	if(loadedTypekitCount == totalTypekitCount) {		
		lib.updateListCache(tFontsUpdateCacheList);		
	}		
};
// symbols:
// helper functions:

function mc_symbol_clone() {
	var clone = this._cloneProps(new this.constructor(this.mode, this.startPosition, this.loop));
	clone.gotoAndStop(this.currentFrame);
	clone.paused = this.paused;
	clone.framerate = this.framerate;
	return clone;
}

function getMCSymbolPrototype(symbol, nominalBounds, frameBounds) {
	var prototype = cjs.extend(symbol, cjs.MovieClip);
	prototype.clone = mc_symbol_clone;
	prototype.nominalBounds = nominalBounds;
	prototype.frameBounds = frameBounds;
	return prototype;
	}


(lib.Symbol7 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#FFFFFF").s().p("AhSElIAApJICkAAIAAJJg");
	this.shape.setTransform(8.3,29.3);

	this.timeline.addTween(cjs.Tween.get(this.shape).wait(1));

}).prototype = getMCSymbolPrototype(lib.Symbol7, new cjs.Rectangle(0,0,16.5,58.5), null);


(lib.Symbol6 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#FFFFFF").s().p("AwdNJQEJkmHvnHQHvnFG0ldQG1lcB6gmQB5gnkJEnQkIEmnwHGQnuHGm1FdQm0Fch5AmQgOAFgJAAQhFAADqkFg");
	this.shape.setTransform(123.5,110.2);

	this.timeline.addTween(cjs.Tween.get(this.shape).wait(1));

}).prototype = getMCSymbolPrototype(lib.Symbol6, new cjs.Rectangle(0,0,247,220.4), null);


(lib.Calque1 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#39893E").s().p("AgoApQgRgRAAgYQAAgXARgRQARgRAXAAQAYAAARARQARARAAAXQAAAYgRARQgRARgYAAQgXAAgRgRg");
	this.shape.setTransform(258.6,61.8);

	this.shape_1 = new cjs.Shape();
	this.shape_1.graphics.f("#39893E").s().p("AhFCwIAykXIg4AAIANhJICKAAIg/Fgg");
	this.shape_1.setTransform(254,92.6);

	this.shape_2 = new cjs.Shape();
	this.shape_2.graphics.f("#39893E").s().p("Ah0CwIA+lgIBSAAIgwEYICJAAIgNBIg");
	this.shape_2.setTransform(344.7,92.6);

	this.shape_3 = new cjs.Shape();
	this.shape_3.graphics.f("#39893E").s().p("AhZCuQgggKgWgVQgXgVgLgcQgMgdAAgkQAAgsAQgmQAQgoAbgdQAbgcAngSQAngRAsAAQApABAfAKQAgAMAVAUQAWAUAMAdQAMAeAAAjQAAArgPAoQgRAogaAcQgaAcgoASQgmAQgtAAQgpAAgfgLgAgihjQgWALgPATQgPATgIAYQgIAXAAAZQAAATAGAPQAGASALALQALALARAIQAQAGAXAAQAaABAWgLQAWgMAPgSQAPgTAIgYQAIgaAAgXQAAgSgGgRQgGgQgMgMQgLgMgRgHQgQgGgWgBQgbAAgVAMg");
	this.shape_3.setTransform(314.8,92.6);

	this.shape_4 = new cjs.Shape();
	this.shape_4.graphics.f("#39893E").s().p("AAvC1IAmjVIABgKIAAgKQAAgLgDgKQgEgLgHgHQgGgHgMgFQgLgFgQABQgWAAgMAGQgNAHgKAKQgIALgEANQgGAPgBAMIgnDWIhSAAIAnjfQAGgfAMgZQANgaAUgSQAUgSAcgKQAcgKAkAAQAfAAAaAIQAaAIARANQASAOAKAWQAKAVAAAYIgCAeIgnDdg");
	this.shape_4.setTransform(277.1,92.1);

	this.shape_5 = new cjs.Shape();
	this.shape_5.graphics.f("#39893E").s().p("AhYCtQgbgIgRgNQgTgPgJgUQgKgUAAgaIADgfIAnjcIBSAAIgmDVIgBAVQAAALADAJQADAKAHAIQAGAHAMAFQAMAEAQABQAVgBAMgGQANgHAKgLQAIgJAFgOQAFgLACgQIAmjWIBSAAIgmDfQgGAfgMAaQgOAagTARQgVATgbAJQgdAKgiAAQghAAgZgIg");
	this.shape_5.setTransform(227.9,93);

	this.shape_6 = new cjs.Shape();
	this.shape_6.graphics.f("#39893E").s().p("AhZCuQgggLgWgUQgXgUgLgeQgMgcAAgkQAAgqAQgoQAQgoAbgdQAbgcAngSQAngRAsAAQApABAfAKQAgALAVAVQAWAUAMAdQAMAeAAAjQAAArgPAoQgPAmgcAeQgbAdgnARQglAQguAAQgpAAgfgLgAgihjQgWAMgPASQgPATgIAYQgIAXAAAZQAAATAGAPQAGARALALQAMANAQAGQAQAIAXgBQAbABAVgLQAWgMAPgSQAOgTAJgYQAIgaAAgXQAAgSgGgRQgGgPgMgNQgLgMgRgHQgSgHgUAAQgaAAgWAMg");
	this.shape_6.setTransform(151.7,92.6);

	this.shape_7 = new cjs.Shape();
	this.shape_7.graphics.f("#39893E").s().p("ABFDkIARhvIgUAKQglAQguAAQgpAAgfgLQgggKgWgVQgWgUgMgdQgMgdAAgjQAAgrAQgoQAQgoAbgdQAbgcAngSQAngRAsAAQApABAfAKQAfAMAXAUQAVAUAMAdQAMAeAAAjQAAAhgIAdIgmDsgAgiiNQgWALgPATQgPATgIAYQgIAXAAAaQAAATAGAOQAGASALALQALALARAIQAQAGAXAAQAbABAVgLQAWgMAPgSQAPgTAIgXIAIgyQAAgSgGgRQgGgQgMgMQgMgMgQgHQgQgGgWgBQgbAAgVAMg");
	this.shape_7.setTransform(190.5,96.8);

	this.shape_8 = new cjs.Shape();
	this.shape_8.graphics.f("#39893E").s().p("AhPDHQgSgcAJhBIAXigIhJAAIALhJIBIAAIAOhmIBXAAIgPBmIBsAAIAABJIh2AAIgVCZQgDAcAHAOQAJAPAcAAQALAAAagJQAYgKALgGIghBYQgaALguAAQhCAAgVgfg");
	this.shape_8.setTransform(119.6,88);

	this.shape_9 = new cjs.Shape();
	this.shape_9.graphics.f("#39893E").s().p("AiMCFQgwg0AMhRQALhQA/g0QA9gzBPAAQBLAAAlAyQAnAzgNBZIgDAYIkKAAQgBAmAXAXQAXAWAlAAQA3AAAtgwIA4AwQhDBGhaAAQhRAAgvgzgAgvheQgbAWgLAmICzAAQAFgmgUgXQgVgWgpAAQglAAgbAXg");
	this.shape_9.setTransform(87,92.6);

	this.shape_10 = new cjs.Shape();
	this.shape_10.graphics.f("#39893E").s().p("AheCyIhfljIBhAAIA8D4IACAAICBj4IBbAAIi+Fjg");
	this.shape_10.setTransform(55,92.7);

	this.shape_11 = new cjs.Shape();
	this.shape_11.graphics.f("#8BB330").s().p("AqRFuQg2iUALieQAKiTBCiDQA/h/BqhdQBrheCEguQCKgwCRALQDgAQCvCNQCrCKBCDTQhQivibhuQighyjEgNQiOgKiHAuQiBAthoBcQhoBcg+B7QhBCBgKCPQgJCEAmB9QAkB4BLBmQhphug1iOg");
	this.shape_11.setTransform(70.3,61.8);

	this.shape_12 = new cjs.Shape();
	this.shape_12.graphics.f("#39893E").s().p("AAzJVQj/gSioi/QipjAASj8QANi/B5iUQB1iSCzg4QiUBEheCEQhgCIgMCmQgSD3ClC8QClC7D6ARQBwAIBrggQBmgfBWhAQhdBah5AtQhpAmhwAAIgsgBg");
	this.shape_12.setTransform(63.1,84.5);

	this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(1));

}).prototype = getMCSymbolPrototype(lib.Calque1, new cjs.Rectangle(0,0,356.4,144.4), null);


(lib.Cimaljeks3aicopy = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 2
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#2D3E86").s().p("AggAjIgBgBQgJgDADgMIAPg3IASAAIgOA1IgBADIAAACIACAEQADABAEAAIAKgBIAJgFIAPg5IASAAIgTBIIgPAAIABgHIgBAAIgFADIgFADIgHACIgIAAQgIAAgFgCg");
	this.shape.setTransform(4,8.2);

	this.shape_1 = new cjs.Shape();
	this.shape_1.graphics.f("#2D3E86").s().p("AgGAGIAAgIIADgIIAKAAIgGAOIgBAHg");
	this.shape_1.setTransform(6.8,12.1);

	this.shape_2 = new cjs.Shape();
	this.shape_2.graphics.f("#2D3E86").s().p("AggASQgHgHADgLQADgKALgHQANgIAPAAQAKABAHAFIABgEIAOAAIgNAvIgPAAIgBgDQgHAEgIAAQgSAAgIgHgAgTAAQgBAHAEAEQAEAFAIAAQAJAAAHgGIAGgTQgFgGgJAAQgSAAgFAPg");
	this.shape_2.setTransform(28.5,9.6);

	this.shape_3 = new cjs.Shape();
	this.shape_3.graphics.f("#2D3E86").s().p("AgPAYQgGgBgFgDQgFgDgBgFQgCgEACgHQABgGAEgEQAEgFAGgEQAGgDAHgCQAFgCAHAAQAHABAFABQAGACADADQAEADABAEQABAEgCAHIgBADIgvAAIABAGIAEAEIAGADIAIAAIALgBIAMgCIgCAKIgMACIgOABQgHgBgHgBgAAAgPIgFADIgEAEIgDADIAcAAIAAgDIgBgEIgEgDIgGAAg");
	this.shape_3.setTransform(55.3,9.5);

	this.shape_4 = new cjs.Shape();
	this.shape_4.graphics.f("#2D3E86").s().p("AglAkIgEgBIADgJIAKABIALABQAMAAAGgDQAIgEABgGIABgBIAAgDIABgCIgEACIgFABIgMACIgLgCQgGgCgCgBQgEgDgBgFQgCgEACgGQACgIAEgEQAEgGAFgCQAEgDAHgCQAGgCAGAAQAEAAAHACQAFADACACIABAAIACgFIAQAAIgNAvIgDAIIgFAGIgHAEIgKAEIgKACIgTAAgAgBgZIgEACQgBAAAAAAQgBABAAAAQgBAAAAABQAAAAgBABIgDAEIgDAGIAAAGIACAFQABACADAAIAHABIAJgBIAEgBIADgCIAGgVQgCgCgEgCQgEgBgFAAIgDAAg");
	this.shape_4.setTransform(41.3,10.7);

	this.shape_5 = new cjs.Shape();
	this.shape_5.graphics.f("#2D3E86").s().p("AgWAUQgIgFAAgJQAAgMALgJQALgJASAAQAMAAAJAEIgCAHQgIgDgJAAQgMAAgGAHQgGAFAAAIQAAANARAAQANAAAGgDIgBAIQgPAEgKAAQgMAAgIgGg");
	this.shape_5.setTransform(67.7,9.7);

	this.shape_6 = new cjs.Shape();
	this.shape_6.graphics.f("#2D3E86").s().p("AAEAYIgPgXIgHAXIgSAAIAPgvIARAAIgHAXIAfgXIARAAIghAWIAVAZg");
	this.shape_6.setTransform(62.1,9.5);

	this.shape_7 = new cjs.Shape();
	this.shape_7.graphics.f("#2D3E86").s().p("AgMAYIANgvIAMAAIgOAvg");
	this.shape_7.setTransform(49,9.5);

	this.shape_8 = new cjs.Shape();
	this.shape_8.graphics.f("#2D3E86").s().p("AgYAkIgRgSIgTASIgRAAIAcgbIgVgVIAUAAIARATQAfgdArgKQAWgEAPABQgOAAgUAGQgpANgcAfIAUAVg");
	this.shape_8.setTransform(52.7,8.3);

	this.shape_9 = new cjs.Shape();
	this.shape_9.graphics.f("#2D3E86").s().p("AgjANIABgFIAKggIARAAIgIAcIgBAFQAAAGAHAAQAIAAAJgEIAKgjIASAAIgOAvIgSAAIACgFQgOAHgLAAQgQAAAAgMg");
	this.shape_9.setTransform(11.7,9.6);

	this.shape_10 = new cjs.Shape();
	this.shape_10.graphics.f("#2D3E86").s().p("AAMAYIgEghIgXAhIgOAAIAlgvIAOAAIAIAvg");
	this.shape_10.setTransform(34.5,9.5);

	this.shape_11 = new cjs.Shape();
	this.shape_11.graphics.f("#2D3E86").s().p("AAgAYIgEghIgYAhIgNAAIgEghIgYAhIgMAAIAjgvIAOAAIAFAdIAWgdIAPAAIAIAvg");
	this.shape_11.setTransform(19.4,9.5);

	this.shape_12 = new cjs.Shape();
	this.shape_12.graphics.f("#FFEC00").s().p("AgbAvQAAgmAQgbQAQgbAXgBQgOABgKAbQgJAbAAAmg");
	this.shape_12.setTransform(54.1,4.7);

	this.shape_13 = new cjs.Shape();
	this.shape_13.graphics.f("#FABA0C").s().p("AgLAuQAAgmAEgaQADgbAEAAQAFAAAEAbQADAZAAAng");
	this.shape_13.setTransform(56.9,4.6);

	this.shape_14 = new cjs.Shape();
	this.shape_14.graphics.f("#C3DB9C").s().p("AgOASQgYgaAAglIAVAAQAAAlARAaQAQAbAXABQgggBgVgbg");
	this.shape_14.setTransform(52.9,14);

	this.shape_15 = new cjs.Shape();
	this.shape_15.graphics.f("#FFEC00").s().p("AASAvQAAgmgRgbQgQgagXgCQAgACAWAaQAXAbAAAmg");
	this.shape_15.setTransform(61,4.7);

	this.shape_16 = new cjs.Shape();
	this.shape_16.graphics.f("#E42F25").s().p("AhBAPQgYgXgDgiQAEAdAXAUQAbAXAmAAQAmAAAbgXQAYgUAEgdQgDAigYAXQgbAcgnAAQglAAgcgcg");
	this.shape_16.setTransform(56.9,14.3);

	this.shape_17 = new cjs.Shape();
	this.shape_17.graphics.f("#2FB6E9").s().p("ABBgGQgbgXgmAAQgmAAgbAXQgXAUgEAcQADghAYgXQAcgbAlAAQAmAAAcAbQAYAXADAhQgEgcgYgUg");
	this.shape_17.setTransform(56.9,4.2);

	this.shape_18 = new cjs.Shape();
	this.shape_18.graphics.f("#78B738").s().p("AhAAMQgbgPgBgYQABAPAbAKQAbAIAlABQAmgBAbgIQAbgKABgPQgBAYgbAPQgbAQgmAAQglAAgbgQg");
	this.shape_18.setTransform(56.9,12.1);

	this.shape_19 = new cjs.Shape();
	this.shape_19.graphics.f("#2FB6E9").s().p("ABBADQgbgJgmABQglgBgbAJQgbALgBAPQABgYAbgQQAbgQAlgBQAmABAbAQQAbAQABAYQgBgPgbgLg");
	this.shape_19.setTransform(56.9,6.4);

	this.shape_20 = new cjs.Shape();
	this.shape_20.graphics.f("#FABA0C").s().p("AgHBBQgEgaAAgnQAAgmAEgbQADgbAEAAQAFAAAEAbQADAbAAAmQAAAngDAaQgEAcgEAAQgFAAgDgcg");
	this.shape_20.setTransform(56.9,9.3);

	this.shape_21 = new cjs.Shape();
	this.shape_21.graphics.f("#C3DB9C").s().p("AgOBBQgYgbAAglQAAglAXgbQAWgbAggCQgXACgQAbQgRAbAAAlQAAAlARAbQAQAbAXABQgggBgVgbg");
	this.shape_21.setTransform(52.9,9.3);

	this.shape_22 = new cjs.Shape();
	this.shape_22.graphics.f("#FFEC00").s().p("AABBBQARgbAAglQAAgmgRgbQgQgagXgCQAgACAWAaQAXAbAAAmQAAAlgXAbQgWAbggABQAXgBAQgbg");
	this.shape_22.setTransform(61,9.3);

	this.shape_23 = new cjs.Shape();
	this.shape_23.graphics.f("#FFEC00").s().p("AgLBBQgQgbAAglQAAgmAQgbQAQgbAXgBQgOABgKAbQgJAbAAAmQAAAmAJAaQAKAbAOABQgXgBgQgbg");
	this.shape_23.setTransform(54.1,9.3);

	this.shape_24 = new cjs.Shape();
	this.shape_24.graphics.f("#F29A7D").s().p("AgCBBQAJgbAAglQAAgmgJgbQgLgbgPgBQAYABAQAbQARAbAAAmQAAAlgRAbQgQAbgXABQAOgBALgbg");
	this.shape_24.setTransform(59.8,9.3);

	this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_24},{t:this.shape_23},{t:this.shape_22},{t:this.shape_21},{t:this.shape_20},{t:this.shape_19},{t:this.shape_18},{t:this.shape_17},{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(1));

}).prototype = p = new cjs.MovieClip();
p.nominalBounds = new cjs.Rectangle(0,0,70.9,18.6);


(lib.LOGVETOQUINOLCMYKTAGLINEai = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Calque 1
	this.instance = new lib.Calque1();
	this.instance.parent = this;
	this.instance.setTransform(178.2,72.2,1,1,0,0,0,178.2,72.2);

	this.timeline.addTween(cjs.Tween.get(this.instance).wait(1));

}).prototype = p = new cjs.MovieClip();
p.nominalBounds = new cjs.Rectangle(0,0,356.4,144.4);


// stage content:
(lib._02 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 15
	this.instance = new lib.LOGVETOQUINOLCMYKTAGLINEai("synched",0);
	this.instance.parent = this;
	this.instance.setTransform(157,184.1,0.196,0.196,0,0,0,179,72.5);

	this.timeline.addTween(cjs.Tween.get(this.instance).wait(60));

	// Layer 13
	this.instance_1 = new lib.Symbol7();
	this.instance_1.parent = this;
	this.instance_1.setTransform(-12.5,148.7,1,1,0,0,0,8.3,29.3);
	this.instance_1.alpha = 0.5;
	this.instance_1._off = true;

	this.timeline.addTween(cjs.Tween.get(this.instance_1).wait(34).to({_off:false},0).to({x:211.1,y:148.2},14).wait(12));

	// Layer 2
	this.instance_2 = new lib.Cimaljeks3aicopy("synched",0);
	this.instance_2.parent = this;
	this.instance_2.setTransform(96.1,148.7,0.846,0.844,0,0,0,35.5,9.3);
	this.instance_2.alpha = 0;

	this.timeline.addTween(cjs.Tween.get(this.instance_2).to({scaleX:3.24,scaleY:3.24,x:100.8,y:151.5,alpha:1},11).to({scaleX:2.68,scaleY:2.68,x:100.9,y:148.3},8).wait(41));

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#CC6600").s().p("AggAqQgKgKAAgRQAAgPAGgNQAGgOAIgJQANgKAUgBIAggCIgCAJIggACQgRABgKAKQgIAHgDANQAKgQAUAAQAPAAAJAJQAHAJABANQAAAVgOALQgMAKgPAAQgPAAgJgIgAgVgFQgKAIAAAPQAAALAHAGQAGAGALAAQANAAAJgKQAIgIAAgOQAAgKgGgHQgHgGgKAAQgMAAgJAJg");
	this.shape.setTransform(135.6,101.5,0.911,0.911);

	this.shape_1 = new cjs.Shape();
	this.shape_1.graphics.f("#CC6600").s().p("AgdAlIAPhJIAJAAIgFAdIASAAQALgBAGAHQAFAFAAAIQAAAMgKAHQgHAGgMAAgAgRAcIAUAAQAHAAAEgEQAGgEAAgHQAAgGgEgDQgDgDgHAAIgSAAg");
	this.shape_1.setTransform(156.4,102.5,0.911,0.911);

	this.shape_2 = new cjs.Shape();
	this.shape_2.graphics.f("#CC6600").s().p("AAZAlIgNg8IgkA8IgLAAIAshJIAKAAIARBJg");
	this.shape_2.setTransform(149.5,102.5,0.911,0.911);

	this.shape_3 = new cjs.Shape();
	this.shape_3.graphics.f("#CC6600").s().p("AgbAeQgJgKAAgPQAAgTANgNQALgKARAAQAPAAAIAIQAJAJAAAQQAAASgMANQgMALgRAAQgOAAgJgIgAgRgUQgJAKgBANQAAANAHAHQAGAGALAAQAMAAAJgIQAKgKgBgNQAAgNgGgHQgGgGgLAAQgNAAgIAIg");
	this.shape_3.setTransform(143,102.6,0.911,0.911);

	this.shape_4 = new cjs.Shape();
	this.shape_4.graphics.f("#CC6600").s().p("AgMAeQgLgLACgQIgPAAIgHAhIgJAAIAOhIIAKAAIgGAgIAOAAQADgPAJgIQAKgKASAAQAPAAAJAIQAJAJAAAQQAAATgNAMQgLALgSAAQgOAAgJgIgAgCgUQgKAKAAAOQAAAMAHAHQAFAGALAAQAOAAAJgIQAJgKAAgOQAAgMgGgHQgGgGgLAAQgOAAgIAIg");
	this.shape_4.setTransform(164.6,102.6,0.911,0.911);

	this.shape_5 = new cjs.Shape();
	this.shape_5.graphics.f("#CC6600").s().p("AgdAfQgMgJAAgSQAAgTAPgNQAKgJASAAQAVAAAHAQIADgPIAJAAIgOBIIgJAAIABgIQgKAKgQAAQgOAAgJgHgAgWgTQgJAJAAANQAAAMAHAHQAGAHANAAQANAAAJgJQAIgJABgNQgBgNgGgHQgIgGgLAAQgNAAgJAJg");
	this.shape_5.setTransform(64.7,102.6,0.911,0.911);

	this.shape_6 = new cjs.Shape();
	this.shape_6.graphics.f("#CC6600").s().p("AANAlIAHghIgoAAIgGAhIgLAAIAPhJIAKAAIgGAgIAnAAIAHggIAKAAIgPBJg");
	this.shape_6.setTransform(56.9,102.5,0.911,0.911);

	this.shape_7 = new cjs.Shape();
	this.shape_7.graphics.f("#CC6600").s().p("AAdAuIADgTIg+AAIgDATIgKAAIAFgcIAIAAIAng/IAMAAIAPA/IAIAAIgFAcgAAaASIgNg1IggA1IAtAAg");
	this.shape_7.setTransform(71.9,103.4,0.911,0.911);

	this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(60));

	// Layer 1
	this.instance_3 = new lib.Symbol6();
	this.instance_3.parent = this;
	this.instance_3.setTransform(12.6,23.7,1,1,0,0,0,123.5,110.2);
	this.instance_3.alpha = 0.398;

	this.timeline.addTween(cjs.Tween.get(this.instance_3).to({x:188.5,y:110.2},19).wait(15).to({x:11.7,y:24.7},0).wait(26));

	// Layer 12
	this.shape_8 = new cjs.Shape();
	this.shape_8.graphics.f("#CC6600").s().p("AjwC+QgJgKAHgWQAFgNAMgPQAMgRARgQQAOgPAVgSIAlgdQAUgPAMgGQAPgHAJAAQAAAAABAAQAAAAABAAQAAAAABABQAAAAAAABIADAFIABAFQAAABAAAAQABAAAAABQAAAAABAAQAAAAABAAQAGAAAJgCQAMgDAHAAQAKAAAGAHQADgIAEgNIAGgpIABgPQAEghAOgaQAOgYATgQQATgPAVgKQAagKASgEQASgEAXgCIAigBQAOAAAKAGQAJAHgDAHQgBACgIAJIgkAfIgKAGQgIAAADgIIAPgQQACgGgKgDQgJgEgQAAQgNAAgWACQgVAEgNAFQgaAHgPANQgOANgIAQQgIAQgDATIgGAoQgEAYgFARQgEAVgMAXIgDAIIAAAKQgBAEABACQADAGgIAAIgGABIgJABQgPARgXAUQgUATgdATQgaASgYALQgYAMgUAAQgUAAgKgKgAg8ApQgPAGgWAMQgUAMgTAOIglAdQgQAOgMAPQgLANgEAMQgHASASAAQAIAAAPgHQAPgHARgKQAVgNAOgKIAigaIAZgWQAKgJACgGIgCgEQgCgCAEgJQAEgKAIgHIAJgIQABgCgGAAQgOAAgSAHg");
	this.shape_8.setTransform(112.2,69.4,0.793,0.793);

	this.shape_9 = new cjs.Shape();
	this.shape_9.graphics.f("#CC6600").s().p("AhyBWQgKgKAIgWQAEgMAMgQQALgOASgSQAPgOAVgTIAjgdQASgNAPgHQAOgIAJAAQAAAAABAAQABAAAAABQABAAAAAAQABABAAAAIAEALQAAAAAAABQAAAAAAAAQABAAAAABQABAAAAAAQAGAAAKgDQALgCAIAAQALAAAGAJQAGAJgHAWIgCADIAAAKQAAAFABACQACAFgIAAIgGABQgDACgFAAQgQARgXATQgXAUgZASQgcATgVAKQgaAMgTAAQgTAAgKgLgABBg+QgQAGgWANQgTALgTAOQgUAPgQAOQgQAOgMAPQgMAMgEAMQgGASARAAQAIAAAPgHQAQgHAQgKIAjgXIA7guQAKgKACgFIgCgEQgCgDADgIQAFgLAIgHQAIgGAAgBQACgDgHAAQgOAAgRAHg");
	this.shape_9.setTransform(86.3,77.6,0.793,0.793);

	this.shape_10 = new cjs.Shape();
	this.shape_10.graphics.f("#CC6600").s().p("AlRE4QgFgFADgIQAHgSAYgcQAXgZAmghQAggdAxglQAsghA0gjQA8gnAogXQAzgeAwgYQAegaAZgaQAcgcATgWQATgWAQgYQAOgUAHgUQAJgZgOgRQgOgSgvAAQgWAAgeAHQgdAHggALQggAKgeAMIg1AYQgWAJgRAKIgQAJQAAABAAABQAAAAAAABQAAAAAAABQABAAAAAAQABABAAAAQABABAAAAQAAABAAAAQAAABgBAAQAAABgMAIIhJAoIgrAWIgkARQgQAGgHAAIgJgBQgGgCADgHQABgFAagRQAZgQAqgXQAqgYAygYQAsgVA8gZQAygTA2gQQAzgNAnAAQAyAAAWAVQAXAWgSAvQgJAZgbAkQgcAjgpArQAggNAZgGQAZgHAUAAQAfAAAIASQAHASgKAcQgMAggUAdQgUAdgbAhQgZAfgbAcIhWBVQgPAPgBADQgCAHAJAAQACAAARgKIBog5QAHABgCAGIgRALIggAWIhUAzQgYANgNAGQgQAIgHAAQgIAAgIgHQgHgHAFgNQAEgMAWgYIAygxIBAg8QAggdAigiQAdgfAaggQAZggAKgdQAEgLgCgJQgCgKgPAAQgbAAgmAMQgkALgtAVQgtAtgtAnQgtApgzArQg1AsgrAhQg0AngkAYQgpAcgeAPQgeAPgQAAQgHAAgEgEgAg3BKQhBAsgsAgQgwAjgfAcQgfAbgDAJQgBABAAAAQAAABgBAAQAAABAAAAQAAAAgBAAIADABQADAAAOgKICqh4IBOg7QAsgkAhgcQg7Ahg9Apg");
	this.shape_10.setTransform(60,62.9,0.793,0.793);

	this.shape_11 = new cjs.Shape();
	this.shape_11.graphics.f("#CC6600").s().p("AlzEcQgIgJAGgSQAJgYAWgYQAVgYAggaQAjgbAjgXQAsgcAngUQAggRA0gZQArgTApgQQAtgRAlgLQAqgMAhgHIBYhlQAMgOANgSIhpBXQgYASgXANQgUAMgKAAQgGAAgCgIQgBgHADgKQAGgQAQgWQAMgRASgVIhPA2QgQALgWALQgSAKgRAFQgQAFgMAAQgXAAgJgOQgJgOAJgaQAKgcAagfQAXgdAegYQAbgXAbgPQAagPAMAAQAJAAADAHQAEAGAEADIALAJQAHAEgEALQgFALgSANQgUANgVALQgaANgWAIQgXAIgRAEQgMAOgIANQgJANgFANQgEANADAHQADAHAKAAQAJAAAOgEQAQgFAOgIQARgIAQgKQAQgJATgNQAjgYAagUQAegXAPgPQABAAABAAQABAAAAABQABAAAAAAQAAABABAAQAVgRASgMQAYgRAXgLQAWgKASAAQANAAAGAFQAHAGgEAMIgEAHQgDgMgNAAQgKAAgNAHQgPAHgQALQgQAKgUAQQgQAMgWATIghAdIgcAcIgUAYQgIAKgDAHQgDAHADAAQAHAAAQgJIAkgZQAPgKAbgXQAUgQAYgVIAlgjQAOgNAMgPQADgDACgBIAGAAIAGABIAEABQAIAAgFAPQgGAPgMAVQgNAVgRAXQgQAUgYAbQgZAcgVAVIAVgBQAIAAgDAJQgCAFgKAAIgdABQgtArgxAnQgyAog1AnQg7ArgxAeQg1AhgyAaQgyAZgnAOQgpAOgcAAQgOAAgJgKgABrgdQgkAMgpAQQgoAQgqATQgoATgqAXQgnAVgmAYQggAUgiAaQgeAZgTAVQgUAVgHAUQgDAKADAIQADAHALAAQAVAAArgSQAlgRA2ggQAugbA7gnQBBgtAmgdQA7gsAegYQAjgaAagZQgYAFgrANgAB1j9IgWANIgZATQgPANgKAKQAMgCARgHQAOgFASgJIAagPQAKgHACgDQACgFgCgDQgBgDgLAAQgFAAgKAEg");
	this.shape_11.setTransform(113.1,91.9,0.793,0.793);

	this.shape_12 = new cjs.Shape();
	this.shape_12.graphics.f("#CC6600").s().p("AhCBSQgGgFADgOQACgMANgSQALgRAPgQIAZgcIAMgOQAJgIAAgHIAFgIQAFgGAFgEQAEgEAHgEQAGgDAGAAQAHAAAFAEQAEAEgBAFIgGAPQgDAKgFAFIg9A4IgZAaQgMAOgBAHQgBADABADQABADAEAAQAIAAAOgGIA5gfIAVgOQAFAAgCAIIg6AnIgcAOQgQAIgPAAQgKAAgFgFg");
	this.shape_12.setTransform(157.4,79.2,0.793,0.793);

	this.shape_13 = new cjs.Shape();
	this.shape_13.graphics.f("#CC6600").s().p("AhrBnQgIgPARggQARgZARgTIA+g/IAgghQACgEAEgEIAMgLQAHgFAHgEQAHgEAGAAIAEgBQAHAAABAEQADAGgDAEQgBAEgIAKIgNAOIgKAKIhkBhIgYAdQgLANgEAJQgKAUAOACQAOABAMgFQAPgHAMgJQANgJAMgOQALgMAJgNQAJgMAGgLQAFgJAAgGIgGgBQgEAAAAgJQAAgKAHgJQAGgGANgGQANgGAFADQAFAEAAAIIgBAFIAGAEIAZAJQAHABgJAGIgjAIIgJACQABANgNATQgOAUgUASQgTASgZAOQgaANgUABQgYAAgIgPg");
	this.shape_13.setTransform(147.8,77.8,0.793,0.793);

	this.shape_14 = new cjs.Shape();
	this.shape_14.graphics.f("#CC6600").s().p("AgHA1QgCgDACgEIAGgLIAUgZIALgKIgMAEIgLAJQgIAJgNAKIgbAPQgOAHgQAAQgIAAgCgFQgBgGACgGQADgHAGgGQAGgGAIAAQAAAAABAAQAAAAABAAQAAAAABAAQAAAAABAAQACABgBAFIgBABQgHALAAAEQAAABAAAAQABABAAAAQABAAAAAAQABAAABAAQAIAAALgFQAIgDAOgKQANgIAIgIIADgDQgIAAgEgCQgFgCgDgEQgDgDABgIQADgOAKgIQAIgJAKgFQAKgEAIAAIACAAQAIABAEAFQAEAHgFAKQgEAKgSASIAIgEQAKgEAFgBQAFgBgEAGIgYAWIgKAOQgEAGABAFQAEgBAKgGIA0gjIACAAQAAAAAAABQAAAAAAAAQAAABAAAAQgBAAAAABIg9AsQgLAHgIABIgEACQgBAAAAAAQgBgBAAAAQgBAAAAAAQgBAAAAgBgAATgsQgHAFgEAHQgFAHgBAFIAAAIQACAEADADQADADAHABIAIgJQAJgKAEgIQAFgIgBgGQgDgIgHAAQgHABgGAFg");
	this.shape_14.setTransform(141.3,27.7,0.911,0.911);

	this.shape_15 = new cjs.Shape();
	this.shape_15.graphics.f("#CC6600").s().p("Ai0CLQgEgEADgKQAFgMAKgKQAMgOANgLIAjgZQAagPAOgIQAWgMATgIIApgRIAogNQARgGATgEIA3hAIgZAVIgaAUQgMALgLAEQgJAGgFABQgEAAAAgFQgBgCACgGQADgHAHgLIASgVQALgMALgKQAKgKAOgJQAMgIALgFQAMgGAHAAQAHAAADAEQADACgCAGIgCADQgBgFgHgBIgLAEIgPAJQgLAHgHAFIgwAsIgKALIgFAIIAAAEQADAAAIgFIASgLIBJhCIACgCIADAAIADABIACAAQAEAAgDAIQgCAFgHAMIgiAtIgWAYIAKgBQAFAAgDAEQAAADgFAAIgLAAIgDAAIguAoIgzAmQgWARgeATQgTANgfAQQgVAKgXAJQgTAGgOAAQgHABgEgFgAA0gNIh1AyIglAWIggAXQgOAKgKAMQgKAKgDAKQgCAFABAEQACADAFAAQALAAAUgJQAUgIAZgPIAzghIAygkIArghQAUgOAKgKQgPADgSAGg");
	this.shape_15.setTransform(108.5,35.9,0.911,0.911);

	this.shape_16 = new cjs.Shape();
	this.shape_16.graphics.f("#CC6600").s().p("AgMAyQgCgCADgIQADgJAGgKIANgTIAOgPIAIgIIg7A2QgJAIgKAEQgLAFgIABQgIAAgCgFQgCgEABgFQACgGAEgEQAFgFAGAAQABgBAAAAQABAAAAAAQABAAAAABQABAAAAAAQABAAAAABQAAAAABABQAAAAAAABQAAAAAAABIAAACQgGAIACADQABACAEAAQAHAAAKgHQAJgFANgNIA8g7IAKgGIAHgBIAAAAIAIABQAAAAABAAQAAAAABAAQAAAAABABQAAAAABAAQAAACgDAEQgFAHgJAIIgVATQgJAIgMANQgKAKgFAMQgDAEAFAAIALgFIApgdIACAAQAAAAAAABQAAAAAAAAQAAABAAAAQAAABgBAAIgwAkQgKAFgEABIgFABIgDgBg");
	this.shape_16.setTransform(129.6,28.1,0.911,0.911);

	this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8}]}).wait(60));

}).prototype = p = new cjs.MovieClip();
p.nominalBounds = new cjs.Rectangle(-11,13.5,302.8,284.6);
// library properties:
lib.properties = {
	width: 200,
	height: 200,
	fps: 24,
	color: "#FFFFFF",
	opacity: 1.00,
	webfonts: {},
	manifest: [],
	preloads: []
};




})(lib = lib||{}, images = images||{}, createjs = createjs||{}, ss = ss||{}, AdobeAn = AdobeAn||{});
var lib, images, createjs, ss, AdobeAn;