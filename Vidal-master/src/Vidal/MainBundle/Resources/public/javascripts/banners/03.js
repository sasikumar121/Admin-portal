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


(lib.Symbol6 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#FFFFFF").s().p("AjoHbIAAu1IHRAAIAAO1g");
	this.shape.setTransform(23.3,47.5);

	this.timeline.addTween(cjs.Tween.get(this.shape).wait(1));

}).prototype = getMCSymbolPrototype(lib.Symbol6, new cjs.Rectangle(0,0,46.5,95), null);


(lib.Symbol5 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#8BB330").s().p("Aj2D4QhnhnAAiRQAAiQBnhmQBmhnCQAAQCRAABnBnQBmBmAACQQAACRhmBnQhnBmiRAAQiQAAhmhmg");
	this.shape.setTransform(363.8,360.5);

	this.shape_1 = new cjs.Shape();
	this.shape_1.graphics.f("#8BB330").s().p("Aj3D4QhmhnAAiRQAAiQBmhmQBnhnCQAAQCRAABmBnQBnBmAACQQAACRhnBnQhmBmiRAAQiQAAhnhmg");
	this.shape_1.setTransform(225.8,360.5);

	this.shape_2 = new cjs.Shape();
	this.shape_2.graphics.f("#8BB330").s().p("AtyX6MgPUgqBIp6AAIAAlyINCAAIEYMCMA8nAAAIiMFyMg4PAAAIBuE2MA0zAAAIiCFxMgwlAAAIBaEYMAtnAAAIiMFyMgpPAAAIBQDwMAlpAAAIAAFeg");
	this.shape_2.setTransform(249.7,153);

	this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(1));

}).prototype = getMCSymbolPrototype(lib.Symbol5, new cjs.Rectangle(0,0,499.4,395.5), null);


(lib.LOGVETOQUINOLCMYKTAGLINEai = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Calque 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#39893E").s().p("AAYBBIgag3IgYAAIAAA3IgUAAIAAiBIAxAAQASAAALALQALALAAAQQAAANgHAKQgHAIgLACIAdA6gAgagFIAcAAQAJAAAHgGQAFgFAAgKQAAgJgFgFQgHgGgJAAIgcAAg");
	this.shape.setTransform(343.1,133.1);

	this.shape_1 = new cjs.Shape();
	this.shape_1.graphics.f("#39893E").s().p("AgoBBIAAiBIBRAAIAAASIg+AAIAAAmIA1AAIAAAQIg1AAIAAAnIA+AAIAAASg");
	this.shape_1.setTransform(331.8,133.1);

	this.shape_2 = new cjs.Shape();
	this.shape_2.graphics.f("#39893E").s().p("AAaBBIAAg5IgzAAIAAA5IgUAAIAAiBIAUAAIAAA3IAzAAIAAg3IAUAAIAACBg");
	this.shape_2.setTransform(319.9,133.1);

	this.shape_3 = new cjs.Shape();
	this.shape_3.graphics.f("#39893E").s().p("AgJBBIAAhvIgjAAIAAgSIBZAAIAAASIgjAAIAABvg");
	this.shape_3.setTransform(308.7,133.1);

	this.shape_4 = new cjs.Shape();
	this.shape_4.graphics.f("#39893E").s().p("AgoBBIAAiBIBRAAIAAASIg+AAIAAAmIA1AAIAAAQIg1AAIAAAnIA+AAIAAASg");
	this.shape_4.setTransform(298.7,133.1);

	this.shape_5 = new cjs.Shape();
	this.shape_5.graphics.f("#39893E").s().p("AghA0QgIgIgDgMQgCgJAAgXQAAgWACgIQADgNAIgIQANgNAUgBQAUABAMALQAMALADASIgUAAQgFgXgWAAQgLAAgHAIQgFAFgBAIQgCAHAAATQAAATACAIQABAJAFAFQAIAHAKABQANAAAIgKQAGgHAAgOIAAgGIgbAAIAAgQIAvAAIAAATQAAAXgMANQgOAPgVAAQgUAAgNgOg");
	this.shape_5.setTransform(287.2,133.1);

	this.shape_6 = new cjs.Shape();
	this.shape_6.graphics.f("#39893E").s().p("AggA0QgJgIgDgMQgBgJAAgXQAAgXABgHQADgNAJgIQANgNATgBQAVABAMANQAJAIADANQABAHAAAXQAAAXgBAJQgDAMgJAIQgMAOgVAAQgTAAgNgOgAgSgnQgFAFgBAIQgCAHAAATQAAATACAIQABAIAFAFQAIAJAKAAQALAAAIgJQAFgEABgJQACgIAAgTQAAgTgCgHQgBgIgFgFQgHgIgMAAQgLAAgHAIg");
	this.shape_6.setTransform(275.6,133.1);

	this.shape_7 = new cjs.Shape();
	this.shape_7.graphics.f("#39893E").s().p("AgJBBIAAhvIgjAAIAAgSIBZAAIAAASIgjAAIAABvg");
	this.shape_7.setTransform(265.2,133.1);

	this.shape_8 = new cjs.Shape();
	this.shape_8.graphics.f("#5B5B5A").s().p("AgoBBIAAiBIBRAAIAAASIg+AAIAAAmIA1AAIAAAQIg1AAIAAAnIA+AAIAAASg");
	this.shape_8.setTransform(251.1,133.1);

	this.shape_9 = new cjs.Shape();
	this.shape_9.graphics.f("#5B5B5A").s().p("AAYBBIgag3IgYAAIAAA3IgUAAIAAiBIAxAAQASAAALALQALALAAAQQAAANgHAKQgIAIgLACIAeA6gAgagFIAcAAQAKAAAGgGQAFgFAAgKQAAgJgFgFQgGgGgKAAIgcAAg");
	this.shape_9.setTransform(240,133.1);

	this.shape_10 = new cjs.Shape();
	this.shape_10.graphics.f("#5B5B5A").s().p("AggA0QgJgIgDgMQgBgJAAgXQAAgXABgHQADgNAJgIQAMgNAUgBQAVABAMANQAJAIADANQABAHAAAXQAAAXgBAJQgDAMgJAIQgMAOgVAAQgUAAgMgOgAgSgnQgFAFgBAIQgCAHAAATQAAATACAIQABAIAFAFQAIAJAKAAQALAAAIgJQAFgEABgJQACgIAAgTQAAgTgCgHQgBgIgFgFQgHgIgMAAQgLAAgHAIg");
	this.shape_10.setTransform(227.9,133.1);

	this.shape_11 = new cjs.Shape();
	this.shape_11.graphics.f("#5B5B5A").s().p("AAlBBIAAhWIgdA+IgOAAIgeg+IAABWIgUAAIAAiBIAUAAIAlBQIAkhQIAUAAIAACBg");
	this.shape_11.setTransform(214.9,133.1);

	this.shape_12 = new cjs.Shape();
	this.shape_12.graphics.f("#5B5B5A").s().p("AgoBBIAAiBIBRAAIAAASIg+AAIAAAmIA1AAIAAAQIg1AAIAAAnIA+AAIAAASg");
	this.shape_12.setTransform(198.4,133.1);

	this.shape_13 = new cjs.Shape();
	this.shape_13.graphics.f("#5B5B5A").s().p("AgHBBIgqiBIAUAAIAdBeIAdheIAVAAIgrCBg");
	this.shape_13.setTransform(187.6,133.1);

	this.shape_14 = new cjs.Shape();
	this.shape_14.graphics.f("#5B5B5A").s().p("AgoBBIAAiBIBRAAIAAASIg+AAIAAAmIA1AAIAAAQIg1AAIAAAnIA+AAIAAASg");
	this.shape_14.setTransform(177.6,133.1);

	this.shape_15 = new cjs.Shape();
	this.shape_15.graphics.f("#5B5B5A").s().p("AgJBBIAAiBIATAAIAACBg");
	this.shape_15.setTransform(169.3,133.1);

	this.shape_16 = new cjs.Shape();
	this.shape_16.graphics.f("#5B5B5A").s().p("AAaBBIAAg5IgzAAIAAA5IgUAAIAAiBIAUAAIAAA3IAzAAIAAg3IAUAAIAACBg");
	this.shape_16.setTransform(160.5,133.1);

	this.shape_17 = new cjs.Shape();
	this.shape_17.graphics.f("#5B5B5A").s().p("AggA0QgJgIgCgMQgCgJAAgXQAAgWACgIQACgNAJgIQANgNATgBQASAAANAKQAMALADATIgUAAQgFgWgVAAQgLAAgHAIQgFAFgBAIQgCAIAAASQAAATACAIQABAJAFAFQAHAHALABQAWgBAFgWIATAAQgDATgNALQgMAJgSABQgTAAgNgOg");
	this.shape_17.setTransform(148.9,133.1);

	this.shape_18 = new cjs.Shape();
	this.shape_18.graphics.f("#5B5B5A").s().p("AAjBBIgJgaIgyAAIgJAaIgVAAIAviBIAPAAIAvCBgAgSAWIAmAAIgTg5g");
	this.shape_18.setTransform(137.7,133.1);

	this.shape_19 = new cjs.Shape();
	this.shape_19.graphics.f("#39893E").s().p("AgoApQgRgRAAgYQAAgXARgRQARgRAXAAQAYAAARARQARARAAAXQAAAYgRARQgRARgYAAQgXAAgRgRg");
	this.shape_19.setTransform(258.6,61.8);

	this.shape_20 = new cjs.Shape();
	this.shape_20.graphics.f("#39893E").s().p("AhFCwIAykXIg4AAIANhJICKAAIg/Fgg");
	this.shape_20.setTransform(254,92.6);

	this.shape_21 = new cjs.Shape();
	this.shape_21.graphics.f("#39893E").s().p("Ah0CwIA+lgIBSAAIgwEYICJAAIgNBIg");
	this.shape_21.setTransform(344.7,92.6);

	this.shape_22 = new cjs.Shape();
	this.shape_22.graphics.f("#39893E").s().p("AhZCuQgggKgWgVQgXgVgLgcQgMgdAAgkQAAgsAQgmQAQgoAbgdQAbgcAngSQAngRAsAAQApABAfAKQAgAMAVAUQAWAUAMAdQAMAeAAAjQAAArgPAoQgRAogaAcQgaAcgoASQgmAQgtAAQgpAAgfgLgAgihjQgWALgPATQgPATgIAYQgIAXAAAZQAAATAGAPQAGASALALQALALARAIQAQAGAXAAQAaABAWgLQAWgMAPgSQAPgTAIgYQAIgaAAgXQAAgSgGgRQgGgQgMgMQgLgMgRgHQgQgGgWgBQgbAAgVAMg");
	this.shape_22.setTransform(314.8,92.6);

	this.shape_23 = new cjs.Shape();
	this.shape_23.graphics.f("#39893E").s().p("AAvC1IAmjVIABgKIAAgKQAAgLgDgKQgEgLgHgHQgGgHgMgFQgLgFgQABQgWAAgMAGQgNAHgKAKQgIALgEANQgGAPgBAMIgnDWIhSAAIAnjfQAGgfAMgZQANgaAUgSQAUgSAcgKQAcgKAkAAQAfAAAaAIQAaAIARANQASAOAKAWQAKAVAAAYIgCAeIgnDdg");
	this.shape_23.setTransform(277.1,92.1);

	this.shape_24 = new cjs.Shape();
	this.shape_24.graphics.f("#39893E").s().p("AhYCtQgbgIgRgNQgTgPgJgUQgKgUAAgaIADgfIAnjcIBSAAIgmDVIgBAVQAAALADAJQADAKAHAIQAGAHAMAFQAMAEAQABQAVgBAMgGQANgHAKgLQAIgJAFgOQAFgLACgQIAmjWIBSAAIgmDfQgGAfgMAaQgOAagTARQgVATgbAJQgdAKgiAAQghAAgZgIg");
	this.shape_24.setTransform(227.9,93);

	this.shape_25 = new cjs.Shape();
	this.shape_25.graphics.f("#39893E").s().p("AhZCuQgggLgWgUQgXgUgLgeQgMgcAAgkQAAgqAQgoQAQgoAbgdQAbgcAngSQAngRAsAAQApABAfAKQAgALAVAVQAWAUAMAdQAMAeAAAjQAAArgPAoQgPAmgcAeQgbAdgnARQglAQguAAQgpAAgfgLgAgihjQgWAMgPASQgPATgIAYQgIAXAAAZQAAATAGAPQAGARALALQAMANAQAGQAQAIAXgBQAbABAVgLQAWgMAPgSQAOgTAJgYQAIgaAAgXQAAgSgGgRQgGgPgMgNQgLgMgRgHQgSgHgUAAQgaAAgWAMg");
	this.shape_25.setTransform(151.7,92.6);

	this.shape_26 = new cjs.Shape();
	this.shape_26.graphics.f("#39893E").s().p("ABFDkIARhvIgUAKQglAQguAAQgpAAgfgLQgggKgWgVQgWgUgMgdQgMgdAAgjQAAgrAQgoQAQgoAbgdQAbgcAngSQAngRAsAAQApABAfAKQAfAMAXAUQAVAUAMAdQAMAeAAAjQAAAhgIAdIgmDsgAgiiNQgWALgPATQgPATgIAYQgIAXAAAaQAAATAGAOQAGASALALQALALARAIQAQAGAXAAQAbABAVgLQAWgMAPgSQAPgTAIgXIAIgyQAAgSgGgRQgGgQgMgMQgMgMgQgHQgQgGgWgBQgbAAgVAMg");
	this.shape_26.setTransform(190.5,96.8);

	this.shape_27 = new cjs.Shape();
	this.shape_27.graphics.f("#39893E").s().p("AhPDHQgSgcAJhBIAXigIhJAAIALhJIBIAAIAOhmIBXAAIgPBmIBsAAIAABJIh2AAIgVCZQgDAcAHAOQAJAPAcAAQALAAAagJQAYgKALgGIghBYQgaALguAAQhCAAgVgfg");
	this.shape_27.setTransform(119.6,88);

	this.shape_28 = new cjs.Shape();
	this.shape_28.graphics.f("#39893E").s().p("AiMCFQgwg0AMhRQALhQA/g0QA9gzBPAAQBLAAAlAyQAnAzgNBZIgDAYIkKAAQgBAmAXAXQAXAWAlAAQA3AAAtgwIA4AwQhDBGhaAAQhRAAgvgzgAgvheQgbAWgLAmICzAAQAFgmgUgXQgVgWgpAAQglAAgbAXg");
	this.shape_28.setTransform(87,92.6);

	this.shape_29 = new cjs.Shape();
	this.shape_29.graphics.f("#39893E").s().p("AheCyIhfljIBhAAIA8D4IACAAICBj4IBbAAIi+Fjg");
	this.shape_29.setTransform(55,92.7);

	this.shape_30 = new cjs.Shape();
	this.shape_30.graphics.f("#8BB330").s().p("AqRFuQg2iUALieQAKiTBCiDQA/h/BqhdQBrheCEguQCKgwCRALQDgAQCvCNQCrCKBCDTQhQivibhuQighyjEgNQiOgKiHAuQiBAthoBcQhoBcg+B7QhBCBgKCPQgJCEAmB9QAkB4BLBmQhphug1iOg");
	this.shape_30.setTransform(70.3,61.8);

	this.shape_31 = new cjs.Shape();
	this.shape_31.graphics.f("#39893E").s().p("AAzJVQj/gSioi/QipjAASj8QANi/B5iUQB1iSCzg4QiUBEheCEQhgCIgMCmQgSD3ClC8QClC7D6ARQBwAIBrggQBmgfBWhAQhdBah5AtQhpAmhwAAIgsgBg");
	this.shape_31.setTransform(63.1,84.5);

	this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_31},{t:this.shape_30},{t:this.shape_29},{t:this.shape_28},{t:this.shape_27},{t:this.shape_26},{t:this.shape_25},{t:this.shape_24},{t:this.shape_23},{t:this.shape_22},{t:this.shape_21},{t:this.shape_20},{t:this.shape_19},{t:this.shape_18},{t:this.shape_17},{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(1));

}).prototype = p = new cjs.MovieClip();
p.nominalBounds = new cjs.Rectangle(0,0,356.4,144.4);


// stage content:
(lib._03 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// Layer 8
	this.shape = new cjs.Shape();
	this.shape.graphics.f().s("#3F8B46").ss(1,1,1).p("AVQWJMgqfAAAQgYAAgQgQQgRgRAAgYMAAAgqgQAAgXARgRQAQgQAYAAMAqfAAAQAYAAARAQQAQARAAAXMAAAAqgQAAAYgQARQgRAQgYAAg");
	this.shape.setTransform(100,100,0.706,0.706);

	this.timeline.addTween(cjs.Tween.get(this.shape).wait(50));

	// Layer 3 copy 2
	this.instance = new lib.Symbol5();
	this.instance.parent = this;
	this.instance.setTransform(270,65,0.249,0.228,0,-10.1,180,249.1,198.6);

	this.timeline.addTween(cjs.Tween.get(this.instance).to({x:96.5},8).to({regX:248.8,scaleY:0.22,skewX:-0.6,x:102.8},4).wait(38));

	// Layer 7
	this.instance_1 = new lib.Symbol6();
	this.instance_1.parent = this;
	this.instance_1.setTransform(-9.7,163.1,0.376,1,0,0,0,23.3,47.5);
	this.instance_1.alpha = 0.398;

	this.timeline.addTween(cjs.Tween.get(this.instance_1).to({x:211.8},21).wait(29));

	// Layer 1
	this.instance_2 = new lib.LOGVETOQUINOLCMYKTAGLINEai("synched",0);
	this.instance_2.parent = this;
	this.instance_2.setTransform(101,157.5,0.477,0.477,0,0,0,178.2,72.4);

	this.timeline.addTween(cjs.Tween.get(this.instance_2).wait(50));

}).prototype = p = new cjs.MovieClip();
p.nominalBounds = new cjs.Rectangle(81.6,99,354.7,211.6);
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