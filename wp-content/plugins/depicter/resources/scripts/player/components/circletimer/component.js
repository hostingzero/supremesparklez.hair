!function(e,t){if("object"==typeof exports&&"object"==typeof module)module.exports=t(require("preact/compat"));else if("function"==typeof define&&define.amd)define(["preact/compat"],t);else{var r="object"==typeof exports?t(require("preact/compat")):t(e.PreactCompat);for(var o in r)("object"==typeof exports?exports:e)[o]=r[o]}}(Depicter,(function(e){return function(){"use strict";var t={314:function(t){t.exports=e}},r={};function o(e){var n=r[e];if(void 0!==n)return n.exports;var s=r[e]={exports:{}};return t[e](s,s.exports,o),s.exports}o.d=function(e,t){for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var n={};return function(){o.r(n),o.d(n,{dpcCircleTimer:function(){return p}});var e,t=o(314);function r(){return r=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},r.apply(this,arguments)}var s,c=o=>t.createElement("svg",r({width:12,height:16,viewBox:"0 0 12 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},o),e||(e=t.createElement("path",{fillRule:"evenodd",clipRule:"evenodd",d:"M7.99805 14.0029V1.99902C7.99805 0.89453 8.89453 0 9.99805 0C11.1035 0 11.9981 0.89453 11.9981 1.99902V14.0029C11.9981 15.1035 11.1035 16.0029 9.99805 16.0029C8.89453 16.0029 7.99805 15.1035 7.99805 14.0029ZM0 14.0029V1.99902C0 0.89453 0.89453 0 2 0C3.10547 0 4 0.89453 4 1.99902V14.0029C4 15.1035 3.10547 16.0029 2 16.0029C0.89453 16.0029 0 15.1035 0 14.0029Z"})));function a(){return a=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},a.apply(this,arguments)}var i=e=>t.createElement("svg",a({width:11,height:16,viewBox:"0 0 11 16",fill:"none",xmlns:"http://www.w3.org/2000/svg",style:{left:"8%"}},e),s||(s=t.createElement("path",{fillRule:"evenodd",clipRule:"evenodd",d:"M10.0541 7.22605C10.5431 7.62615 10.5431 8.37385 10.0541 8.77395L1.63338 15.6637C0.980451 16.1979 0.000137329 15.7334 0.000137329 14.8897L0.000137329 1.11026C0.000137329 0.266631 0.98045 -0.197916 1.63338 0.336306L10.0541 7.22605Z"}))),l=e=>{const{className:r="dpc-circle-timer",progress:o=50,lineCap:n=!1,thickness:s=10,status:a="play",showSymbol:l=!1,changeStatusOnClick:u=!1,showSymbolOnHover:p=!1,onPause:f,onResume:d}=e,h=50-s/2,m=(0,t.useMemo)((()=>{const e=2*Math.PI*h;return{strokeDasharray:e,strokeDashoffset:e*((100-Math.min(100,Math.max(0,o)))/100)}}),[o,h]),b=(0,t.useMemo)((()=>({inset:`${s+22}%`})),[s]),v=(0,t.useCallback)((()=>{u&&("play"===a?f?.():d?.())}),[f,d,u,a]);return Depicter.h("div",{className:r+(u?" dpc-circle-timer-clickable":""),onClick:v},Depicter.h("svg",{width:"100%",height:"100%",viewBox:"0 0 100 100"},Depicter.h("circle",{className:"dpc-circle-timer-bg-bar",r:h,cx:"50%",cy:"50%",fill:"transparent",stroke:"#e0e0e0",strokeWidth:s}),Depicter.h("circle",{className:"dpc-circle-timer-progress-bar",r:h,cx:"50%",cy:"50%",fill:"transparent",stroke:"#60e6a8",strokeWidth:s,strokeLinecap:n?"round":void 0,style:m})),l&&Depicter.h("div",{className:`dpc-circle-timer-symbol ${p?"dpc-circle-timer-hover-symbol":""} `,style:b},"play"!==a?Depicter.h(i,null):Depicter.h(c,null)))};function u(){return u=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},u.apply(this,arguments)}const p={component:e=>{const{composer:r,...o}=e,[n,s]=(0,t.useState)(0),[c,a]=(0,t.useState)("play"),i=(0,t.useRef)(0),p=(0,t.useRef)(0);(0,t.useEffect)((()=>{let e;const t=()=>{const r=p.current+(i.current-p.current)/10;Math.abs(i.current-p.current)>=.02&&s(r),p.current=r,e=window.requestAnimationFrame(t)};return t(),()=>{e&&window.cancelAnimationFrame(e)}}),[]);const f=(0,t.useCallback)(((e,t)=>{i.current=t/100*115}),[]),d=(0,t.useCallback)((()=>{a("pause")}),[]),h=(0,t.useCallback)((()=>{a("play")}),[]),m=(0,t.useCallback)((()=>{r.slideshow.pause()}),[r.slideshow]),b=(0,t.useCallback)((()=>{r.slideshow.resume()}),[r.slideshow]);return(0,t.useEffect)((()=>(r.on("slideshowTimerUpdate",f),r.on("slideshowPaused",d),r.on("slideshowStart",h),()=>{r.off("slideshowTimerUpdate",f),r.off("slideshowPaused",d),r.off("slideshowStart",h)})),[r,d,f,h]),Depicter.h(l,u({},o,{progress:n,status:c,onPause:m,onResume:b}))},async:!1}}(),n}()}));