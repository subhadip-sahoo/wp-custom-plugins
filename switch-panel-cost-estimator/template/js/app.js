"use strict";var App;App=angular.module("app",["ngCookies","ngResource","app.controllers","app.directives","app.filters","app.services","ui"]),App.config(["$routeProvider","$locationProvider",function(e,t,n){return t.html5Mode(!1)}]),"use strict",angular.module("app.controllers",[]).controller("AppCtrl",["$scope","$location","$resource","$rootScope","$http",function(e,t,n,r,i){var s,o,u,a,f,l,c;return e.data={},e.data.defaults={requestType:"",howManyPanels:"1",wouldLikeContact:"Yes",approximatePanelSize:'up to 6" x 6"',rockerSwitches:"0",toggleSwitches:"0",circuitBreakers:"0",dcPlugs:"0",voltmeters:"0",ledLights:"0",textLabels:"0",emptyHoles:"0",graphic:"None",backlightGraphic:0},e.components=[{id:"rockerSwitches",name:"Rocker Switches"},{id:"toggleSwitches",name:"Toggle Switches"},{id:"circuitBreakers",name:"Circuit Breakers"},{id:"dcPlugs",name:"DC Plugs"},{id:"voltmeters",name:"Voltmeters"},{id:"ledLights",name:"LED Lights"},{id:"textLabels",name:"Text Labels"},{id:"emptyHoles",name:"Additional Empty Holes"}],e.costTable=f={},i.get("cost_table.txt").success(function(t){return e.costTable=f=t,e.invalidCostTable=!1}).error(function(t){return e.invalidCostTable=!0}),s=function(t){return e.data[t]*f[t]},o=function(e){return f[e]},a=function(t,n){var r;return(r=f[n])!=null?r[e.data[t]]:void 0},u=function(e){return a(e,e)},e.costForComponent=function(e){return s(e)/100},e.costForOneComponent=function(e){return o(e)/100},e.costForOptionBasedOn=function(e,t){return a(e,t)/100},e.costForOption=function(e){return u(e)/100},e.totalCost=function(){var t;return t=u("approximatePanelSize")+_(e.components).pluck("id").map(s).reduce(function(e,t){return e+t})+u("graphic")+e.data.backlightGraphic*a("graphic","backlightGraphic"),t/100},e.heardAboutUsString=c=function(){var t,n,r;return n=function(){var n,i;n=e.data.heardAboutVia,i=[];for(t in n)r=n[t],r?i.push(""+t):i.push(void 0);return i}(),n.join(", ")},l=function(){var t;return"Name: "+e.data.fullName+"\nCompany: "+e.data.company+"\nEmail: "+e.data.email+"\nPhone: "+e.data.phone+"\n\nRequest Type: "+e.data.requestType+"\nAdditional comments: "+e.data.requestTypeComments+"\n\nHow many panels they'd like to purchase: "+e.data.howManyPanels+"\n\nHow they heard about us: "+c()+"\nOther: "+((t=e.data.heardAboutVia)!=null?t.OtherMethod:void 0)+"\n\nWould like contact: "+e.data.wouldLikeContact+"\nProject details: "+e.data.projectDetails+"\n\n--- Switch panel configuration ---\nApproximate panel size: "+e.data.approximatePanelSize+"\nRocker switches:        "+e.data.rockerSwitches+"\nToggle switches:        "+e.data.toggleSwitches+"\nCircuit breakers:       "+e.data.circuitBreakers+"\nDC plugs:               "+e.data.dcPlugs+"\nVoltmeters:             "+e.data.voltmeters+"\nLED lights:             "+e.data.ledLights+"\nText labels:            "+e.data.textLabels+"\nAdditional empty holes: "+e.data.emptyHoles+"\nGraphic:                "+e.data.graphic+"\nBacklight graphic:      "+e.data.backlightGraphic+"\n\n--- Panel estimate ---\nActual:   $"+e.totalCost()+"\nLow end:  $"+f.range.low/100*e.totalCost()+"\nHigh end: $"+f.range.high/100*e.totalCost()},e.showEstimate=function(){if(e.calculator.$valid)return e.wantToDisplayEstimate=!0,i.post("calculator-email.php",{message:l()})},e.change=function(){return e.wantToDisplayEstimate=!1},e.displayEstimate=function(){return e.wantToDisplayEstimate},e.data=e.data.defaults,e.wantToDisplayEstimate=!1}]),"use strict",angular.module("app.directives",["app.services"]).directive("appVersion",["version",function(e){return function(t,n,r){return n.text(e)}}]),"use strict",angular.module("app.filters",[]).filter("interpolate",["version",function(e){return function(t){return String(t).replace(/\%VERSION\%/mg,e)}}]),"use strict",angular.module("app.services",[]).factory("version",function(){return"0.1"})