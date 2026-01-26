<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<!------ Include the above in your HEAD tag ---------->
	<script src='https://www.marcoguglie.it/Codepen/AnimatedHeaderBg/demo-1/js/EasePack.min.js'></script>
	<script src='https://www.marcoguglie.it/Codepen/AnimatedHeaderBg/demo-1/js/rAF.js'></script>
	<script src='https://www.marcoguglie.it/Codepen/AnimatedHeaderBg/demo-1/js/TweenLite.min.js'></script>
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	
	<title>Deutsche Post</title>
</head>
<body>

<?php $this->renderBody(); ?>


<script type="text/javascript">
	(function() {
		var width, height, largeHeader, canvas, ctx, points, target, animateHeader = true;
// Main
initHeader();
initAnimation();
addListeners();
function initHeader() {
	width = window.innerWidth;
	height = window.innerHeight;
	target = {x: width/2, y: height/2};
	largeHeader = document.getElementById('large-header');
	largeHeader.style.height = height+'px';
	canvas = document.getElementById('demo-canvas');
	canvas.width = width;
	canvas.height = height;
	ctx = canvas.getContext('2d');
// create points
points = [];
for(var x = 0; x < width; x = x + width/20) {
	for(var y = 0; y < height; y = y + height/20) {
		var px = x + Math.random()*width/20;
		var py = y + Math.random()*height/20;
		var p = {x: px, originX: px, y: py, originY: py };
		points.push(p);
	}
}
// for each point find the 5 closest points
for(var i = 0; i < points.length; i++) {
	var closest = [];
	var p1 = points[i];
	for(var j = 0; j < points.length; j++) {
		var p2 = points[j]
		if(!(p1 == p2)) {
			var placed = false;
			for(var k = 0; k < 5; k++) {
				if(!placed) {
					if(closest[k] == undefined) {
						closest[k] = p2;
						placed = true;
					}
				}
			}
			for(var k = 0; k < 5; k++) {
				if(!placed) {
					if(getDistance(p1, p2) < getDistance(p1, closest[k])) {
						closest[k] = p2;
						placed = true;
					}
				}
			}
		}
	}
	p1.closest = closest;
}
// assign a circle to each point
for(var i in points) {
	var c = new Circle(points[i], 2+Math.random()*2, 'rgba(255,255,255,0.3)');
	points[i].circle = c;
}
}
// Event handling
function addListeners() {
	if(!('ontouchstart' in window)) {
		window.addEventListener('mousemove', mouseMove);
	}
	window.addEventListener('scroll', scrollCheck);
	window.addEventListener('resize', resize);
}
function mouseMove(e) {
	var posx = posy = 0;
	if (e.pageX || e.pageY) {
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY)    {
		posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	}
	target.x = posx;
	target.y = posy;
}
function scrollCheck() {
	if(document.body.scrollTop > height) animateHeader = false;
	else animateHeader = true;
}
function resize() {
	width = window.innerWidth;
	height = window.innerHeight;
	largeHeader.style.height = height+'px';
	canvas.width = width;
	canvas.height = height;
}
// animation
function initAnimation() {
	animate();
	for(var i in points) {
		shiftPoint(points[i]);
	}
}
function animate() {
	if(animateHeader) {
		ctx.clearRect(0,0,width,height);
		for(var i in points) {
// detect points in range
if(Math.abs(getDistance(target, points[i])) < 4000) {
	points[i].active = 0.3;
	points[i].circle.active = 0.6;
} else if(Math.abs(getDistance(target, points[i])) < 20000) {
	points[i].active = 0.1;
	points[i].circle.active = 0.3;
} else if(Math.abs(getDistance(target, points[i])) < 40000) {
	points[i].active = 0.02;
	points[i].circle.active = 0.1;
} else {
	points[i].active = 0;
	points[i].circle.active = 0;
}
drawLines(points[i]);
points[i].circle.draw();
}
}
requestAnimationFrame(animate);
}
function shiftPoint(p) {
	TweenLite.to(p, 1+1*Math.random(), {x:p.originX-50+Math.random()*100,
		y: p.originY-50+Math.random()*100, ease:Circ.easeInOut,
		onComplete: function() {
			shiftPoint(p);
		}});
}
// Canvas manipulation
function drawLines(p) {
	if(!p.active) return;
	for(var i in p.closest) {
		ctx.beginPath();
		ctx.moveTo(p.x, p.y);
		ctx.lineTo(p.closest[i].x, p.closest[i].y);
		ctx.strokeStyle = 'rgba(203,3,34,'+ p.active+')';
		ctx.stroke();
	}
}
function Circle(pos,rad,color) {
	var _this = this;
// constructor
(function() {
	_this.pos = pos || null;
	_this.radius = rad || null;
	_this.color = color || null;
})();
this.draw = function() {
	if(!_this.active) return;
	ctx.beginPath();
	ctx.arc(_this.pos.x, _this.pos.y, _this.radius, 0, 2 * Math.PI, false);
	ctx.fillStyle = 'rgba(203,3,34,'+ _this.active+')';
	ctx.fill();
};
}
// Util
function getDistance(p1, p2) {
	return Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2);
}
})();
</script>


<style>
	

.mt-100 {
    margin-top: 100px; 
}
.mb-100 {
    margin-bottom: 100px;
}

.icon {
    width: 32px;
    height: 32px;
    text-align: center;
    padding: 7px 8px;
    border: 2px solid;
    border-radius: 50%;
}

.btn-circle {
    border-radius: 20px;
}

.input-group input {
    border: 0;
    box-shadow: none;
    padding-right: 30px;
}
.input-group input:focus,
.input-group input:active {
    outline: 0;
    box-shadow: none;
}
.input-group-btn:last-child>.btn {
    z-index: 2;
    margin-left: -18px;   
    border-radius: 20px;
}


/* Header */
.large-header {
  position: relative;
  width: 100%;
  background: #fff;
  overflow: hidden;
  
  z-index: 1;
  background: url("deutchepost-login.png");
  background-repeat: no-repeat;
  background-position: top right;




}

body {


background: -moz-linear-gradient(top, #ffe57e 0%, #ffcc00 100%); 
background: -webkit-linear-gradient(top, #ffe57e 0%,#ffcc00 100%);
background: linear-gradient(to bottom, #ffe57e 0%,#ffcc00 100%); 
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffe57e', endColorstr='#ffcc00',GradientType=0 );

    font-family: "FrutigerLTW01-55Roman",sans-serif;
}



.main-title {
  position: absolute;
  margin: 0;
  padding: 0;
  color: #4C4B53;
  top: 0%;
  left: 50%;
  -webkit-transform: translate3d(-50%, 5%, 0);
  transform: translate3d(-50%, 5%, 0);
}

.btn-success {
    color: #fff;
    background-color: #B9004D;
    border-color: #B9004D;
}

.btn-success:hover {
    color: #fff;
    background-color:#4C4B53;
    border-color: #4C4B53;
}

.demo-1 .main-title {
  text-transform: uppercase;
  font-size: 4.2em;
  letter-spacing: 0.1em;
}
.main-title .thin {
  font-weight: 200;
}
@media only screen and (max-width: 768px) {
  .demo-1 .main-title {
    font-size: 3em;
  }
}



.login-container{
    margin-top: 5%;
    margin-bottom: 5%;
}
.login-form-1{
    padding: 5%;
    box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.2), 0 9px 26px 0 rgba(0, 0, 0, 0.19);
}
.login-form-1 h3{
    text-align: center;
    color: #333;
}
.login-form-2{
    padding: 5%;
    background: #0062cc;
    box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.2), 0 9px 26px 0 rgba(0, 0, 0, 0.19);
}
.login-form-2 h3{
    text-align: center;
    color: #fff;
}
.login-container form{
    padding: 10%;
}
.btnSubmit
{
    width: 50%;
    border-radius: 1rem;
    padding: 1.5%;
    border: none;
    cursor: pointer;
}
.login-form-1 .btnSubmit{
    font-weight: 600;
    color: #fff;
    background-color: #0062cc;
}
.login-form-2 .btnSubmit{
    font-weight: 600;
    color: #0062cc;
    background-color: #fff;
}
.login-form-2 .ForgetPwd{
    color: #fff;
    font-weight: 600;
    text-decoration: none;
}
.login-form-1 .ForgetPwd{
    color: #0062cc;
    font-weight: 600;
    text-decoration: none;
}

.login .content .form-actions {
    clear: both;
    border: 0px;
    border-bottom: 1px solid #eee;
    padding: 0px 30px 25px 30px;
    margin-left: -30px;
    margin-right: -30px;
}

.login .content .rememberme {
    margin-left: 8px;
    margin-top: 10px;
}

.login .content .check {
    color: #8290a3;
}



.form-heading { font-size:23px;}
.panell h2{ color:#333; font-size:18px; margin:10px 0 8px 0;}
.panell p { color:#333; font-size:14px; margin-bottom:30px; line-height:12px;}
.login-form .form-control {
  background: #f7f7f7 none repeat scroll 0 0;
  border: 1px solid #d4d4d4;
  border-radius: 4px;
  font-size: 14px;
  height: 50px;
  line-height: 50px;
}
.main-div {
  background: #ffffff none repeat scroll 0 0;
  border-radius: 2px;
  padding: 50px 70px 70px 71px;
  border:1px solid #e5cb60;
    box-shadow: inset 0px 0px 10px rgba(0,0,0,0.5);

}

.login-form .form-group {
  margin-bottom:10px;
}
.login-form{ text-align:center;}
.forgot a {
  color: #777777;
  font-size: 14px;
  text-decoration: underline;
}

.forgot {
  text-align: left; margin-bottom:10px;
}
.botto-text {
  color: #ffffff;
  font-size: 14px;
  margin: auto;
}
.login-form .btn.btn-primary.reset {
  background: #ff9900 none repeat scroll 0 0;
}
.back { text-align: left; margin-top:10px;}
.back a {color: #444444; font-size: 13px;text-decoration: none;}


.login-form  .btn.btn-primary {
   color: #ffffff;
  font-size: 14px;
  width: 100%;
  height: 50px;
  line-height: 50px;
  padding: 0;
  background: #d40511!important;
  border-color: #d40511!important;

}

.login-form  .btn.btn-primary:hover {
  background: orange!important;
  border-color: orange!important;

}


.login-form  .btn.btn-info {
  background: orange!important;
  border-color: orange!important;
  color: #ffffff;
  font-size: 14px;
  width: 100%;
  height: 50px;
  line-height: 50px;
  padding: 0;
}

.login-form  .btn.btn-info:hover {
  background: #d40511!important;
  border-color: #d40511!important;
}

.text-white { color: #000}

</style>


</body>
</html>