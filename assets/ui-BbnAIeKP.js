import{r as c,a as ht,R as vt,b as yt}from"./vendor-B_9pl2FA.js";var Ae={exports:{}},X={};/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var pt=c,mt=Symbol.for("react.element"),gt=Symbol.for("react.fragment"),kt=Object.prototype.hasOwnProperty,bt=pt.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,Et={key:!0,ref:!0,__self:!0,__source:!0};function Pe(e,t,n){var r,o={},a=null,i=null;n!==void 0&&(a=""+n),t.key!==void 0&&(a=""+t.key),t.ref!==void 0&&(i=t.ref);for(r in t)kt.call(t,r)&&!Et.hasOwnProperty(r)&&(o[r]=t[r]);if(e&&e.defaultProps)for(r in t=e.defaultProps,t)o[r]===void 0&&(o[r]=t[r]);return{$$typeof:mt,type:e,key:a,ref:i,props:o,_owner:bt.current}}X.Fragment=gt;X.jsx=Pe;X.jsxs=Pe;Ae.exports=X;var m=Ae.exports;function D(e,t,{checkForDefaultPrevented:n=!0}={}){return function(o){if(e==null||e(o),n===!1||!o.defaultPrevented)return t==null?void 0:t(o)}}function pe(e,t){if(typeof e=="function")return e(t);e!=null&&(e.current=t)}function De(...e){return t=>{let n=!1;const r=e.map(o=>{const a=pe(o,t);return!n&&typeof a=="function"&&(n=!0),a});if(n)return()=>{for(let o=0;o<r.length;o++){const a=r[o];typeof a=="function"?a():pe(e[o],null)}}}}function T(...e){return c.useCallback(De(...e),e)}function wt(e,t){const n=c.createContext(t),r=a=>{const{children:i,...s}=a,f=c.useMemo(()=>s,Object.values(s));return m.jsx(n.Provider,{value:f,children:i})};r.displayName=e+"Provider";function o(a){const i=c.useContext(n);if(i)return i;if(t!==void 0)return t;throw new Error(`\`${a}\` must be used within \`${e}\``)}return[r,o]}function Ct(e,t=[]){let n=[];function r(a,i){const s=c.createContext(i),f=n.length;n=[...n,i];const u=v=>{var k;const{scope:y,children:g,...C}=v,l=((k=y==null?void 0:y[e])==null?void 0:k[f])||s,p=c.useMemo(()=>C,Object.values(C));return m.jsx(l.Provider,{value:p,children:g})};u.displayName=a+"Provider";function h(v,y){var l;const g=((l=y==null?void 0:y[e])==null?void 0:l[f])||s,C=c.useContext(g);if(C)return C;if(i!==void 0)return i;throw new Error(`\`${v}\` must be used within \`${a}\``)}return[u,h]}const o=()=>{const a=n.map(i=>c.createContext(i));return function(s){const f=(s==null?void 0:s[e])||a;return c.useMemo(()=>({[`__scope${e}`]:{...s,[e]:f}}),[s,f])}};return o.scopeName=e,[r,xt(o,...t)]}function xt(...e){const t=e[0];if(e.length===1)return t;const n=()=>{const r=e.map(o=>({useScope:o(),scopeName:o.scopeName}));return function(a){const i=r.reduce((s,{useScope:f,scopeName:u})=>{const v=f(a)[`__scope${u}`];return{...s,...v}},{});return c.useMemo(()=>({[`__scope${t.scopeName}`]:i}),[i])}};return n.scopeName=t.scopeName,n}function le(e){const t=_t(e),n=c.forwardRef((r,o)=>{const{children:a,...i}=r,s=c.Children.toArray(a),f=s.find(Nt);if(f){const u=f.props.children,h=s.map(v=>v===f?c.Children.count(u)>1?c.Children.only(null):c.isValidElement(u)?u.props.children:null:v);return m.jsx(t,{...i,ref:o,children:c.isValidElement(u)?c.cloneElement(u,void 0,h):null})}return m.jsx(t,{...i,ref:o,children:a})});return n.displayName=`${e}.Slot`,n}var uo=le("Slot");function _t(e){const t=c.forwardRef((n,r)=>{const{children:o,...a}=n;if(c.isValidElement(o)){const i=St(o),s=Mt(a,o.props);return o.type!==c.Fragment&&(s.ref=r?De(r,i):i),c.cloneElement(o,s)}return c.Children.count(o)>1?c.Children.only(null):null});return t.displayName=`${e}.SlotClone`,t}var Oe=Symbol("radix.slottable");function lo(e){const t=({children:n})=>m.jsx(m.Fragment,{children:n});return t.displayName=`${e}.Slottable`,t.__radixId=Oe,t}function Nt(e){return c.isValidElement(e)&&typeof e.type=="function"&&"__radixId"in e.type&&e.type.__radixId===Oe}function Mt(e,t){const n={...t};for(const r in t){const o=e[r],a=t[r];/^on[A-Z]/.test(r)?o&&a?n[r]=(...s)=>{a(...s),o(...s)}:o&&(n[r]=o):r==="style"?n[r]={...o,...a}:r==="className"&&(n[r]=[o,a].filter(Boolean).join(" "))}return{...e,...n}}function St(e){var r,o;let t=(r=Object.getOwnPropertyDescriptor(e.props,"ref"))==null?void 0:r.get,n=t&&"isReactWarning"in t&&t.isReactWarning;return n?e.ref:(t=(o=Object.getOwnPropertyDescriptor(e,"ref"))==null?void 0:o.get,n=t&&"isReactWarning"in t&&t.isReactWarning,n?e.props.ref:e.props.ref||e.ref)}var Rt=["a","button","div","form","h2","h3","img","input","label","li","nav","ol","p","span","svg","ul"],S=Rt.reduce((e,t)=>{const n=le(`Primitive.${t}`),r=c.forwardRef((o,a)=>{const{asChild:i,...s}=o,f=i?n:t;return typeof window<"u"&&(window[Symbol.for("radix-ui")]=!0),m.jsx(f,{...s,ref:a})});return r.displayName=`Primitive.${t}`,{...e,[t]:r}},{});function At(e,t){e&&ht.flushSync(()=>e.dispatchEvent(t))}function O(e){const t=c.useRef(e);return c.useEffect(()=>{t.current=e}),c.useMemo(()=>(...n)=>{var r;return(r=t.current)==null?void 0:r.call(t,...n)},[])}function Pt(e,t=globalThis==null?void 0:globalThis.document){const n=O(e);c.useEffect(()=>{const r=o=>{o.key==="Escape"&&n(o)};return t.addEventListener("keydown",r,{capture:!0}),()=>t.removeEventListener("keydown",r,{capture:!0})},[n,t])}var Dt="DismissableLayer",ie="dismissableLayer.update",Ot="dismissableLayer.pointerDownOutside",Lt="dismissableLayer.focusOutside",me,Le=c.createContext({layers:new Set,layersWithOutsidePointerEventsDisabled:new Set,branches:new Set}),de=c.forwardRef((e,t)=>{const{disableOutsidePointerEvents:n=!1,onEscapeKeyDown:r,onPointerDownOutside:o,onFocusOutside:a,onInteractOutside:i,onDismiss:s,...f}=e,u=c.useContext(Le),[h,v]=c.useState(null),y=(h==null?void 0:h.ownerDocument)??(globalThis==null?void 0:globalThis.document),[,g]=c.useState({}),C=T(t,b=>v(b)),l=Array.from(u.layers),[p]=[...u.layersWithOutsidePointerEventsDisabled].slice(-1),k=l.indexOf(p),_=h?l.indexOf(h):-1,E=u.layersWithOutsidePointerEventsDisabled.size>0,w=_>=k,x=It(b=>{const A=b.target,W=[...u.branches].some(J=>J.contains(A));!w||W||(o==null||o(b),i==null||i(b),b.defaultPrevented||s==null||s())},y),R=$t(b=>{const A=b.target;[...u.branches].some(J=>J.contains(A))||(a==null||a(b),i==null||i(b),b.defaultPrevented||s==null||s())},y);return Pt(b=>{_===u.layers.size-1&&(r==null||r(b),!b.defaultPrevented&&s&&(b.preventDefault(),s()))},y),c.useEffect(()=>{if(h)return n&&(u.layersWithOutsidePointerEventsDisabled.size===0&&(me=y.body.style.pointerEvents,y.body.style.pointerEvents="none"),u.layersWithOutsidePointerEventsDisabled.add(h)),u.layers.add(h),ge(),()=>{n&&u.layersWithOutsidePointerEventsDisabled.size===1&&(y.body.style.pointerEvents=me)}},[h,y,n,u]),c.useEffect(()=>()=>{h&&(u.layers.delete(h),u.layersWithOutsidePointerEventsDisabled.delete(h),ge())},[h,u]),c.useEffect(()=>{const b=()=>g({});return document.addEventListener(ie,b),()=>document.removeEventListener(ie,b)},[]),m.jsx(S.div,{...f,ref:C,style:{pointerEvents:E?w?"auto":"none":void 0,...e.style},onFocusCapture:D(e.onFocusCapture,R.onFocusCapture),onBlurCapture:D(e.onBlurCapture,R.onBlurCapture),onPointerDownCapture:D(e.onPointerDownCapture,x.onPointerDownCapture)})});de.displayName=Dt;var Tt="DismissableLayerBranch",Te=c.forwardRef((e,t)=>{const n=c.useContext(Le),r=c.useRef(null),o=T(t,r);return c.useEffect(()=>{const a=r.current;if(a)return n.branches.add(a),()=>{n.branches.delete(a)}},[n.branches]),m.jsx(S.div,{...e,ref:o})});Te.displayName=Tt;function It(e,t=globalThis==null?void 0:globalThis.document){const n=O(e),r=c.useRef(!1),o=c.useRef(()=>{});return c.useEffect(()=>{const a=s=>{if(s.target&&!r.current){let f=function(){Ie(Ot,n,u,{discrete:!0})};const u={originalEvent:s};s.pointerType==="touch"?(t.removeEventListener("click",o.current),o.current=f,t.addEventListener("click",o.current,{once:!0})):f()}else t.removeEventListener("click",o.current);r.current=!1},i=window.setTimeout(()=>{t.addEventListener("pointerdown",a)},0);return()=>{window.clearTimeout(i),t.removeEventListener("pointerdown",a),t.removeEventListener("click",o.current)}},[t,n]),{onPointerDownCapture:()=>r.current=!0}}function $t(e,t=globalThis==null?void 0:globalThis.document){const n=O(e),r=c.useRef(!1);return c.useEffect(()=>{const o=a=>{a.target&&!r.current&&Ie(Lt,n,{originalEvent:a},{discrete:!1})};return t.addEventListener("focusin",o),()=>t.removeEventListener("focusin",o)},[t,n]),{onFocusCapture:()=>r.current=!0,onBlurCapture:()=>r.current=!1}}function ge(){const e=new CustomEvent(ie);document.dispatchEvent(e)}function Ie(e,t,n,{discrete:r}){const o=n.originalEvent.target,a=new CustomEvent(e,{bubbles:!1,cancelable:!0,detail:n});t&&o.addEventListener(e,t,{once:!0}),r?At(o,a):o.dispatchEvent(a)}var fo=de,ho=Te,G=globalThis!=null&&globalThis.document?c.useLayoutEffect:()=>{},jt="Portal",$e=c.forwardRef((e,t)=>{var s;const{container:n,...r}=e,[o,a]=c.useState(!1);G(()=>a(!0),[]);const i=n||o&&((s=globalThis==null?void 0:globalThis.document)==null?void 0:s.body);return i?vt.createPortal(m.jsx(S.div,{...r,ref:t}),i):null});$e.displayName=jt;function Ft(e,t){return c.useReducer((n,r)=>t[n][r]??n,e)}var Z=e=>{const{present:t,children:n}=e,r=Wt(t),o=typeof n=="function"?n({present:r.isPresent}):c.Children.only(n),a=T(r.ref,Bt(o));return typeof n=="function"||r.isPresent?c.cloneElement(o,{ref:a}):null};Z.displayName="Presence";function Wt(e){const[t,n]=c.useState(),r=c.useRef({}),o=c.useRef(e),a=c.useRef("none"),i=e?"mounted":"unmounted",[s,f]=Ft(i,{mounted:{UNMOUNT:"unmounted",ANIMATION_OUT:"unmountSuspended"},unmountSuspended:{MOUNT:"mounted",ANIMATION_END:"unmounted"},unmounted:{MOUNT:"mounted"}});return c.useEffect(()=>{const u=B(r.current);a.current=s==="mounted"?u:"none"},[s]),G(()=>{const u=r.current,h=o.current;if(h!==e){const y=a.current,g=B(u);e?f("MOUNT"):g==="none"||(u==null?void 0:u.display)==="none"?f("UNMOUNT"):f(h&&y!==g?"ANIMATION_OUT":"UNMOUNT"),o.current=e}},[e,f]),G(()=>{if(t){let u;const h=t.ownerDocument.defaultView??window,v=g=>{const l=B(r.current).includes(g.animationName);if(g.target===t&&l&&(f("ANIMATION_END"),!o.current)){const p=t.style.animationFillMode;t.style.animationFillMode="forwards",u=h.setTimeout(()=>{t.style.animationFillMode==="forwards"&&(t.style.animationFillMode=p)})}},y=g=>{g.target===t&&(a.current=B(r.current))};return t.addEventListener("animationstart",y),t.addEventListener("animationcancel",v),t.addEventListener("animationend",v),()=>{h.clearTimeout(u),t.removeEventListener("animationstart",y),t.removeEventListener("animationcancel",v),t.removeEventListener("animationend",v)}}else f("ANIMATION_END")},[t,f]),{isPresent:["mounted","unmountSuspended"].includes(s),ref:c.useCallback(u=>{u&&(r.current=getComputedStyle(u)),n(u)},[])}}function B(e){return(e==null?void 0:e.animationName)||"none"}function Bt(e){var r,o;let t=(r=Object.getOwnPropertyDescriptor(e.props,"ref"))==null?void 0:r.get,n=t&&"isReactWarning"in t&&t.isReactWarning;return n?e.ref:(t=(o=Object.getOwnPropertyDescriptor(e,"ref"))==null?void 0:o.get,n=t&&"isReactWarning"in t&&t.isReactWarning,n?e.props.ref:e.props.ref||e.ref)}function Ut({prop:e,defaultProp:t,onChange:n=()=>{}}){const[r,o]=zt({defaultProp:t,onChange:n}),a=e!==void 0,i=a?e:r,s=O(n),f=c.useCallback(u=>{if(a){const v=typeof u=="function"?u(e):u;v!==e&&s(v)}else o(u)},[a,e,o,s]);return[i,f]}function zt({defaultProp:e,onChange:t}){const n=c.useState(e),[r]=n,o=c.useRef(r),a=O(t);return c.useEffect(()=>{o.current!==r&&(a(r),o.current=r)},[r,o,a]),n}/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const qt=e=>e.replace(/([a-z0-9])([A-Z])/g,"$1-$2").toLowerCase(),Vt=e=>e.replace(/^([A-Z])|[\s-_]+(\w)/g,(t,n,r)=>r?r.toUpperCase():n.toLowerCase()),ke=e=>{const t=Vt(e);return t.charAt(0).toUpperCase()+t.slice(1)},je=(...e)=>e.filter((t,n,r)=>!!t&&t.trim()!==""&&r.indexOf(t)===n).join(" ").trim(),Ht=e=>{for(const t in e)if(t.startsWith("aria-")||t==="role"||t==="title")return!0};/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */var Kt={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:2,strokeLinecap:"round",strokeLinejoin:"round"};/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Gt=c.forwardRef(({color:e="currentColor",size:t=24,strokeWidth:n=2,absoluteStrokeWidth:r,className:o="",children:a,iconNode:i,...s},f)=>c.createElement("svg",{ref:f,...Kt,width:t,height:t,stroke:e,strokeWidth:r?Number(n)*24/Number(t):n,className:je("lucide",o),...!a&&!Ht(s)&&{"aria-hidden":"true"},...s},[...i.map(([u,h])=>c.createElement(u,h)),...Array.isArray(a)?a:[a]]));/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const d=(e,t)=>{const n=c.forwardRef(({className:r,...o},a)=>c.createElement(Gt,{ref:a,iconNode:t,className:je(`lucide-${qt(ke(e))}`,`lucide-${e}`,r),...o}));return n.displayName=ke(e),n};/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Yt=[["path",{d:"M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2",key:"169zse"}]],vo=d("activity",Yt);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Xt=[["rect",{width:"20",height:"5",x:"2",y:"3",rx:"1",key:"1wp1u1"}],["path",{d:"M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8",key:"1s80jp"}],["path",{d:"M10 12h4",key:"a56b0p"}]],yo=d("archive",Xt);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Zt=[["path",{d:"m12 19-7-7 7-7",key:"1l729n"}],["path",{d:"M19 12H5",key:"x3x0zl"}]],po=d("arrow-left",Zt);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Qt=[["path",{d:"M5 12h14",key:"1ays0h"}],["path",{d:"m12 5 7 7-7 7",key:"xquz4c"}]],mo=d("arrow-right",Qt);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Jt=[["path",{d:"m8 2 1.88 1.88",key:"fmnt4t"}],["path",{d:"M14.12 3.88 16 2",key:"qol33r"}],["path",{d:"M9 7.13v-1a3.003 3.003 0 1 1 6 0v1",key:"d7y7pr"}],["path",{d:"M12 20c-3.3 0-6-2.7-6-6v-3a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v3c0 3.3-2.7 6-6 6",key:"xs1cw7"}],["path",{d:"M12 20v-9",key:"1qisl0"}],["path",{d:"M6.53 9C4.6 8.8 3 7.1 3 5",key:"32zzws"}],["path",{d:"M6 13H2",key:"82j7cp"}],["path",{d:"M3 21c0-2.1 1.7-3.9 3.8-4",key:"4p0ekp"}],["path",{d:"M20.97 5c0 2.1-1.6 3.8-3.5 4",key:"18gb23"}],["path",{d:"M22 13h-4",key:"1jl80f"}],["path",{d:"M17.2 17c2.1.1 3.8 1.9 3.8 4",key:"k3fwyw"}]],go=d("bug",Jt);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const en=[["path",{d:"M8 2v4",key:"1cmpym"}],["path",{d:"M16 2v4",key:"4m81vk"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2",key:"1hopcy"}],["path",{d:"M3 10h18",key:"8toen8"}],["path",{d:"M8 14h.01",key:"6423bh"}],["path",{d:"M12 14h.01",key:"1etili"}],["path",{d:"M16 14h.01",key:"1gbofw"}],["path",{d:"M8 18h.01",key:"lrp35t"}],["path",{d:"M12 18h.01",key:"mhygvu"}],["path",{d:"M16 18h.01",key:"kzsmim"}]],ko=d("calendar-days",en);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const tn=[["path",{d:"M8 2v4",key:"1cmpym"}],["path",{d:"M16 2v4",key:"4m81vk"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2",key:"1hopcy"}],["path",{d:"M3 10h18",key:"8toen8"}]],bo=d("calendar",tn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const nn=[["path",{d:"M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z",key:"1tc9qg"}],["circle",{cx:"12",cy:"13",r:"3",key:"1vg3eu"}]],Eo=d("camera",nn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const rn=[["path",{d:"M3 3v16a2 2 0 0 0 2 2h16",key:"c24i48"}],["path",{d:"M18 17V9",key:"2bz60n"}],["path",{d:"M13 17V5",key:"1frdt8"}],["path",{d:"M8 17v-3",key:"17ska0"}]],wo=d("chart-column",rn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const on=[["line",{x1:"12",x2:"12",y1:"20",y2:"10",key:"1vz5eb"}],["line",{x1:"18",x2:"18",y1:"20",y2:"4",key:"cun8e5"}],["line",{x1:"6",x2:"6",y1:"20",y2:"16",key:"hq0ia6"}]],Co=d("chart-no-axes-column-increasing",on);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const an=[["path",{d:"M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z",key:"pzmjnu"}],["path",{d:"M21.21 15.89A10 10 0 1 1 8 2.83",key:"k2fpak"}]],xo=d("chart-pie",an);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const cn=[["path",{d:"M20 6 9 17l-5-5",key:"1gmf2c"}]],_o=d("check",cn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const sn=[["path",{d:"m6 9 6 6 6-6",key:"qrunsl"}]],No=d("chevron-down",sn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const un=[["path",{d:"m15 18-6-6 6-6",key:"1wnfg3"}]],Mo=d("chevron-left",un);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ln=[["path",{d:"m9 18 6-6-6-6",key:"mthhwq"}]],So=d("chevron-right",ln);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const dn=[["path",{d:"m18 15-6-6-6 6",key:"153udz"}]],Ro=d("chevron-up",dn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const fn=[["path",{d:"m11 17-5-5 5-5",key:"13zhaf"}],["path",{d:"m18 17-5-5 5-5",key:"h8a8et"}]],Ao=d("chevrons-left",fn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const hn=[["path",{d:"m6 17 5-5-5-5",key:"xnjwq"}],["path",{d:"m13 17 5-5-5-5",key:"17xmmf"}]],Po=d("chevrons-right",hn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const vn=[["path",{d:"m7 15 5 5 5-5",key:"1hf1tw"}],["path",{d:"m7 9 5-5 5 5",key:"sgt6xg"}]],Do=d("chevrons-up-down",vn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const yn=[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["line",{x1:"12",x2:"12",y1:"8",y2:"12",key:"1pkeuh"}],["line",{x1:"12",x2:"12.01",y1:"16",y2:"16",key:"4dfq90"}]],Oo=d("circle-alert",yn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const pn=[["path",{d:"M21.801 10A10 10 0 1 1 17 3.335",key:"yps3ct"}],["path",{d:"m9 11 3 3L22 4",key:"1pflzl"}]],Lo=d("circle-check-big",pn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const mn=[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"M8 12h8",key:"1wcyev"}],["path",{d:"M12 8v8",key:"napkw2"}]],To=d("circle-plus",mn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const gn=[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}]],Io=d("circle",gn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const kn=[["path",{d:"M12 6v6l4 2",key:"mmk7yg"}],["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}]],$o=d("clock",kn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const bn=[["rect",{width:"14",height:"14",x:"8",y:"8",rx:"2",ry:"2",key:"17jyea"}],["path",{d:"M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2",key:"zix9uf"}]],jo=d("copy",bn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const En=[["path",{d:"M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528",key:"1jaruq"}]],Fo=d("flag",En);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const wn=[["path",{d:"M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z",key:"1kt360"}]],Wo=d("folder",wn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Cn=[["path",{d:"M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z",key:"sc7q7i"}]],Bo=d("funnel",Cn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const xn=[["circle",{cx:"9",cy:"12",r:"1",key:"1vctgf"}],["circle",{cx:"9",cy:"5",r:"1",key:"hp0tcf"}],["circle",{cx:"9",cy:"19",r:"1",key:"fkjjf6"}],["circle",{cx:"15",cy:"12",r:"1",key:"1tmaij"}],["circle",{cx:"15",cy:"5",r:"1",key:"19l28e"}],["circle",{cx:"15",cy:"19",r:"1",key:"f4zoj3"}]],Uo=d("grip-vertical",xn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const _n=[["rect",{width:"18",height:"18",x:"3",y:"3",rx:"2",ry:"2",key:"1m3agn"}],["circle",{cx:"9",cy:"9",r:"2",key:"af1f0g"}],["path",{d:"m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21",key:"1xmnt7"}]],zo=d("image",_n);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Nn=[["path",{d:"m15.5 7.5 2.3 2.3a1 1 0 0 0 1.4 0l2.1-2.1a1 1 0 0 0 0-1.4L19 4",key:"g0fldk"}],["path",{d:"m21 2-9.6 9.6",key:"1j0ho8"}],["circle",{cx:"7.5",cy:"15.5",r:"5.5",key:"yqb3hr"}]],qo=d("key",Nn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Mn=[["path",{d:"M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z",key:"zw3jo"}],["path",{d:"M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12",key:"1wduqc"}],["path",{d:"M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17",key:"kqbvx6"}]],Vo=d("layers",Mn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Sn=[["rect",{width:"7",height:"9",x:"3",y:"3",rx:"1",key:"10lvy0"}],["rect",{width:"7",height:"5",x:"14",y:"3",rx:"1",key:"16une8"}],["rect",{width:"7",height:"9",x:"14",y:"12",rx:"1",key:"1hutg5"}],["rect",{width:"7",height:"5",x:"3",y:"16",rx:"1",key:"ldoo1y"}]],Ho=d("layout-dashboard",Sn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Rn=[["rect",{width:"18",height:"7",x:"3",y:"3",rx:"1",key:"f1a2em"}],["rect",{width:"9",height:"7",x:"3",y:"14",rx:"1",key:"jqznyg"}],["rect",{width:"5",height:"7",x:"16",y:"14",rx:"1",key:"q5h2i8"}]],Ko=d("layout-template",Rn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const An=[["path",{d:"M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5",key:"1gvzjb"}],["path",{d:"M9 18h6",key:"x1upvd"}],["path",{d:"M10 22h4",key:"ceow96"}]],Go=d("lightbulb",An);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Pn=[["path",{d:"m3 17 2 2 4-4",key:"1jhpwq"}],["path",{d:"m3 7 2 2 4-4",key:"1obspn"}],["path",{d:"M13 6h8",key:"15sg57"}],["path",{d:"M13 12h8",key:"h98zly"}],["path",{d:"M13 18h8",key:"oe0vm4"}]],Yo=d("list-checks",Pn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Dn=[["rect",{x:"3",y:"5",width:"6",height:"6",rx:"1",key:"1defrl"}],["path",{d:"m3 17 2 2 4-4",key:"1jhpwq"}],["path",{d:"M13 6h8",key:"15sg57"}],["path",{d:"M13 12h8",key:"h98zly"}],["path",{d:"M13 18h8",key:"oe0vm4"}]],Xo=d("list-todo",Dn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const On=[["path",{d:"M21 12a9 9 0 1 1-6.219-8.56",key:"13zald"}]],Zo=d("loader-circle",On);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ln=[["rect",{width:"18",height:"11",x:"3",y:"11",rx:"2",ry:"2",key:"1w4ew1"}],["path",{d:"M7 11V7a5 5 0 0 1 10 0v4",key:"fwvmzm"}]],Qo=d("lock",Ln);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Tn=[["path",{d:"m16 17 5-5-5-5",key:"1bji2h"}],["path",{d:"M21 12H9",key:"dn1m92"}],["path",{d:"M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4",key:"1uf3rs"}]],Jo=d("log-out",Tn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const In=[["path",{d:"m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7",key:"132q7q"}],["rect",{x:"2",y:"4",width:"20",height:"16",rx:"2",key:"izxlao"}]],ea=d("mail",In);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const $n=[["path",{d:"M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z",key:"169xi5"}],["path",{d:"M15 5.764v15",key:"1pn4in"}],["path",{d:"M9 3.236v15",key:"1uimfh"}]],ta=d("map",$n);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const jn=[["path",{d:"M4 12h16",key:"1lakjw"}],["path",{d:"M4 18h16",key:"19g7jn"}],["path",{d:"M4 6h16",key:"1o0s65"}]],na=d("menu",jn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Fn=[["path",{d:"M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z",key:"1a8usu"}]],ra=d("pen",Fn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Wn=[["path",{d:"M5 12h14",key:"1ays0h"}],["path",{d:"M12 5v14",key:"s699le"}]],oa=d("plus",Wn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Bn=[["path",{d:"M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8",key:"v9h5vc"}],["path",{d:"M21 3v5h-5",key:"1q7to0"}],["path",{d:"M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16",key:"3uifl3"}],["path",{d:"M8 16H3v5",key:"1cv678"}]],aa=d("refresh-cw",Bn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Un=[["path",{d:"m21 21-4.34-4.34",key:"14j7rj"}],["circle",{cx:"11",cy:"11",r:"8",key:"4ej97u"}]],ca=d("search",Un);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const zn=[["path",{d:"M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z",key:"1ffxy3"}],["path",{d:"m21.854 2.147-10.94 10.939",key:"12cjpa"}]],sa=d("send",zn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const qn=[["path",{d:"M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915",key:"1i5ecw"}],["circle",{cx:"12",cy:"12",r:"3",key:"1v7zrd"}]],ia=d("settings",qn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Vn=[["path",{d:"M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z",key:"oel41y"}]],ua=d("shield",Vn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Hn=[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["circle",{cx:"12",cy:"12",r:"6",key:"1vlfrh"}],["circle",{cx:"12",cy:"12",r:"2",key:"1c9p78"}]],la=d("target",Hn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Kn=[["path",{d:"M10 11v6",key:"nco0om"}],["path",{d:"M14 11v6",key:"outv1u"}],["path",{d:"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6",key:"miytrc"}],["path",{d:"M3 6h18",key:"d0wm0j"}],["path",{d:"M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2",key:"e791ji"}]],da=d("trash-2",Kn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Gn=[["path",{d:"M16 7h6v6",key:"box55l"}],["path",{d:"m22 7-8.5 8.5-5-5L2 17",key:"1t1m79"}]],fa=d("trending-up",Gn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Yn=[["path",{d:"m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3",key:"wmoenq"}],["path",{d:"M12 9v4",key:"juzpu7"}],["path",{d:"M12 17h.01",key:"p32p05"}]],ha=d("triangle-alert",Yn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Xn=[["path",{d:"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2",key:"1yyitq"}],["circle",{cx:"9",cy:"7",r:"4",key:"nufk8"}],["line",{x1:"22",x2:"16",y1:"11",y2:"11",key:"1shjgl"}]],va=d("user-minus",Xn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Zn=[["path",{d:"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2",key:"1yyitq"}],["circle",{cx:"9",cy:"7",r:"4",key:"nufk8"}],["line",{x1:"19",x2:"19",y1:"8",y2:"14",key:"1bvyxn"}],["line",{x1:"22",x2:"16",y1:"11",y2:"11",key:"1shjgl"}]],ya=d("user-plus",Zn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Qn=[["circle",{cx:"12",cy:"8",r:"5",key:"1hypcn"}],["path",{d:"M20 21a8 8 0 0 0-16 0",key:"rfgkzh"}]],pa=d("user-round",Qn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Jn=[["path",{d:"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2",key:"1yyitq"}],["path",{d:"M16 3.128a4 4 0 0 1 0 7.744",key:"16gr8j"}],["path",{d:"M22 21v-2a4 4 0 0 0-3-3.87",key:"kshegd"}],["circle",{cx:"9",cy:"7",r:"4",key:"nufk8"}]],ma=d("users",Jn);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const er=[["path",{d:"M18 6 6 18",key:"1bl5f8"}],["path",{d:"m6 6 12 12",key:"d8bk6v"}]],ga=d("x",er);/**
 * @license lucide-react v0.539.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const tr=[["path",{d:"M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z",key:"1xq2db"}]],ka=d("zap",tr);var nr=yt[" useId ".trim().toString()]||(()=>{}),rr=0;function ee(e){const[t,n]=c.useState(nr());return G(()=>{n(r=>r??String(rr++))},[e]),t?`radix-${t}`:""}var te=0;function or(){c.useEffect(()=>{const e=document.querySelectorAll("[data-radix-focus-guard]");return document.body.insertAdjacentElement("afterbegin",e[0]??be()),document.body.insertAdjacentElement("beforeend",e[1]??be()),te++,()=>{te===1&&document.querySelectorAll("[data-radix-focus-guard]").forEach(t=>t.remove()),te--}},[])}function be(){const e=document.createElement("span");return e.setAttribute("data-radix-focus-guard",""),e.tabIndex=0,e.style.outline="none",e.style.opacity="0",e.style.position="fixed",e.style.pointerEvents="none",e}var ne="focusScope.autoFocusOnMount",re="focusScope.autoFocusOnUnmount",Ee={bubbles:!1,cancelable:!0},ar="FocusScope",Fe=c.forwardRef((e,t)=>{const{loop:n=!1,trapped:r=!1,onMountAutoFocus:o,onUnmountAutoFocus:a,...i}=e,[s,f]=c.useState(null),u=O(o),h=O(a),v=c.useRef(null),y=T(t,l=>f(l)),g=c.useRef({paused:!1,pause(){this.paused=!0},resume(){this.paused=!1}}).current;c.useEffect(()=>{if(r){let l=function(E){if(g.paused||!s)return;const w=E.target;s.contains(w)?v.current=w:P(v.current,{select:!0})},p=function(E){if(g.paused||!s)return;const w=E.relatedTarget;w!==null&&(s.contains(w)||P(v.current,{select:!0}))},k=function(E){if(document.activeElement===document.body)for(const x of E)x.removedNodes.length>0&&P(s)};document.addEventListener("focusin",l),document.addEventListener("focusout",p);const _=new MutationObserver(k);return s&&_.observe(s,{childList:!0,subtree:!0}),()=>{document.removeEventListener("focusin",l),document.removeEventListener("focusout",p),_.disconnect()}}},[r,s,g.paused]),c.useEffect(()=>{if(s){Ce.add(g);const l=document.activeElement;if(!s.contains(l)){const k=new CustomEvent(ne,Ee);s.addEventListener(ne,u),s.dispatchEvent(k),k.defaultPrevented||(cr(dr(We(s)),{select:!0}),document.activeElement===l&&P(s))}return()=>{s.removeEventListener(ne,u),setTimeout(()=>{const k=new CustomEvent(re,Ee);s.addEventListener(re,h),s.dispatchEvent(k),k.defaultPrevented||P(l??document.body,{select:!0}),s.removeEventListener(re,h),Ce.remove(g)},0)}}},[s,u,h,g]);const C=c.useCallback(l=>{if(!n&&!r||g.paused)return;const p=l.key==="Tab"&&!l.altKey&&!l.ctrlKey&&!l.metaKey,k=document.activeElement;if(p&&k){const _=l.currentTarget,[E,w]=sr(_);E&&w?!l.shiftKey&&k===w?(l.preventDefault(),n&&P(E,{select:!0})):l.shiftKey&&k===E&&(l.preventDefault(),n&&P(w,{select:!0})):k===_&&l.preventDefault()}},[n,r,g.paused]);return m.jsx(S.div,{tabIndex:-1,...i,ref:y,onKeyDown:C})});Fe.displayName=ar;function cr(e,{select:t=!1}={}){const n=document.activeElement;for(const r of e)if(P(r,{select:t}),document.activeElement!==n)return}function sr(e){const t=We(e),n=we(t,e),r=we(t.reverse(),e);return[n,r]}function We(e){const t=[],n=document.createTreeWalker(e,NodeFilter.SHOW_ELEMENT,{acceptNode:r=>{const o=r.tagName==="INPUT"&&r.type==="hidden";return r.disabled||r.hidden||o?NodeFilter.FILTER_SKIP:r.tabIndex>=0?NodeFilter.FILTER_ACCEPT:NodeFilter.FILTER_SKIP}});for(;n.nextNode();)t.push(n.currentNode);return t}function we(e,t){for(const n of e)if(!ir(n,{upTo:t}))return n}function ir(e,{upTo:t}){if(getComputedStyle(e).visibility==="hidden")return!0;for(;e;){if(t!==void 0&&e===t)return!1;if(getComputedStyle(e).display==="none")return!0;e=e.parentElement}return!1}function ur(e){return e instanceof HTMLInputElement&&"select"in e}function P(e,{select:t=!1}={}){if(e&&e.focus){const n=document.activeElement;e.focus({preventScroll:!0}),e!==n&&ur(e)&&t&&e.select()}}var Ce=lr();function lr(){let e=[];return{add(t){const n=e[0];t!==n&&(n==null||n.pause()),e=xe(e,t),e.unshift(t)},remove(t){var n;e=xe(e,t),(n=e[0])==null||n.resume()}}}function xe(e,t){const n=[...e],r=n.indexOf(t);return r!==-1&&n.splice(r,1),n}function dr(e){return e.filter(t=>t.tagName!=="A")}var fr=function(e){if(typeof document>"u")return null;var t=Array.isArray(e)?e[0]:e;return t.ownerDocument.body},I=new WeakMap,U=new WeakMap,z={},oe=0,Be=function(e){return e&&(e.host||Be(e.parentNode))},hr=function(e,t){return t.map(function(n){if(e.contains(n))return n;var r=Be(n);return r&&e.contains(r)?r:(console.error("aria-hidden",n,"in not contained inside",e,". Doing nothing"),null)}).filter(function(n){return!!n})},vr=function(e,t,n,r){var o=hr(t,Array.isArray(e)?e:[e]);z[n]||(z[n]=new WeakMap);var a=z[n],i=[],s=new Set,f=new Set(o),u=function(v){!v||s.has(v)||(s.add(v),u(v.parentNode))};o.forEach(u);var h=function(v){!v||f.has(v)||Array.prototype.forEach.call(v.children,function(y){if(s.has(y))h(y);else try{var g=y.getAttribute(r),C=g!==null&&g!=="false",l=(I.get(y)||0)+1,p=(a.get(y)||0)+1;I.set(y,l),a.set(y,p),i.push(y),l===1&&C&&U.set(y,!0),p===1&&y.setAttribute(n,"true"),C||y.setAttribute(r,"true")}catch(k){console.error("aria-hidden: cannot operate on ",y,k)}})};return h(t),s.clear(),oe++,function(){i.forEach(function(v){var y=I.get(v)-1,g=a.get(v)-1;I.set(v,y),a.set(v,g),y||(U.has(v)||v.removeAttribute(r),U.delete(v)),g||v.removeAttribute(n)}),oe--,oe||(I=new WeakMap,I=new WeakMap,U=new WeakMap,z={})}},yr=function(e,t,n){n===void 0&&(n="data-aria-hidden");var r=Array.from(Array.isArray(e)?e:[e]),o=fr(e);return o?(r.push.apply(r,Array.from(o.querySelectorAll("[aria-live]"))),vr(r,o,n,"aria-hidden")):function(){return null}},M=function(){return M=Object.assign||function(t){for(var n,r=1,o=arguments.length;r<o;r++){n=arguments[r];for(var a in n)Object.prototype.hasOwnProperty.call(n,a)&&(t[a]=n[a])}return t},M.apply(this,arguments)};function Ue(e,t){var n={};for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.indexOf(r)<0&&(n[r]=e[r]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,r=Object.getOwnPropertySymbols(e);o<r.length;o++)t.indexOf(r[o])<0&&Object.prototype.propertyIsEnumerable.call(e,r[o])&&(n[r[o]]=e[r[o]]);return n}function pr(e,t,n){if(n||arguments.length===2)for(var r=0,o=t.length,a;r<o;r++)(a||!(r in t))&&(a||(a=Array.prototype.slice.call(t,0,r)),a[r]=t[r]);return e.concat(a||Array.prototype.slice.call(t))}var H="right-scroll-bar-position",K="width-before-scroll-bar",mr="with-scroll-bars-hidden",gr="--removed-body-scroll-bar-size";function ae(e,t){return typeof e=="function"?e(t):e&&(e.current=t),e}function kr(e,t){var n=c.useState(function(){return{value:e,callback:t,facade:{get current(){return n.value},set current(r){var o=n.value;o!==r&&(n.value=r,n.callback(r,o))}}}})[0];return n.callback=t,n.facade}var br=typeof window<"u"?c.useLayoutEffect:c.useEffect,_e=new WeakMap;function Er(e,t){var n=kr(null,function(r){return e.forEach(function(o){return ae(o,r)})});return br(function(){var r=_e.get(n);if(r){var o=new Set(r),a=new Set(e),i=n.current;o.forEach(function(s){a.has(s)||ae(s,null)}),a.forEach(function(s){o.has(s)||ae(s,i)})}_e.set(n,e)},[e]),n}function wr(e){return e}function Cr(e,t){t===void 0&&(t=wr);var n=[],r=!1,o={read:function(){if(r)throw new Error("Sidecar: could not `read` from an `assigned` medium. `read` could be used only with `useMedium`.");return n.length?n[n.length-1]:e},useMedium:function(a){var i=t(a,r);return n.push(i),function(){n=n.filter(function(s){return s!==i})}},assignSyncMedium:function(a){for(r=!0;n.length;){var i=n;n=[],i.forEach(a)}n={push:function(s){return a(s)},filter:function(){return n}}},assignMedium:function(a){r=!0;var i=[];if(n.length){var s=n;n=[],s.forEach(a),i=n}var f=function(){var h=i;i=[],h.forEach(a)},u=function(){return Promise.resolve().then(f)};u(),n={push:function(h){i.push(h),u()},filter:function(h){return i=i.filter(h),n}}}};return o}function xr(e){e===void 0&&(e={});var t=Cr(null);return t.options=M({async:!0,ssr:!1},e),t}var ze=function(e){var t=e.sideCar,n=Ue(e,["sideCar"]);if(!t)throw new Error("Sidecar: please provide `sideCar` property to import the right car");var r=t.read();if(!r)throw new Error("Sidecar medium not found");return c.createElement(r,M({},n))};ze.isSideCarExport=!0;function _r(e,t){return e.useMedium(t),ze}var qe=xr(),ce=function(){},Q=c.forwardRef(function(e,t){var n=c.useRef(null),r=c.useState({onScrollCapture:ce,onWheelCapture:ce,onTouchMoveCapture:ce}),o=r[0],a=r[1],i=e.forwardProps,s=e.children,f=e.className,u=e.removeScrollBar,h=e.enabled,v=e.shards,y=e.sideCar,g=e.noIsolation,C=e.inert,l=e.allowPinchZoom,p=e.as,k=p===void 0?"div":p,_=e.gapMode,E=Ue(e,["forwardProps","children","className","removeScrollBar","enabled","shards","sideCar","noIsolation","inert","allowPinchZoom","as","gapMode"]),w=y,x=Er([n,t]),R=M(M({},E),o);return c.createElement(c.Fragment,null,h&&c.createElement(w,{sideCar:qe,removeScrollBar:u,shards:v,noIsolation:g,inert:C,setCallbacks:a,allowPinchZoom:!!l,lockRef:n,gapMode:_}),i?c.cloneElement(c.Children.only(s),M(M({},R),{ref:x})):c.createElement(k,M({},R,{className:f,ref:x}),s))});Q.defaultProps={enabled:!0,removeScrollBar:!0,inert:!1};Q.classNames={fullWidth:K,zeroRight:H};var Nr=function(){if(typeof __webpack_nonce__<"u")return __webpack_nonce__};function Mr(){if(!document)return null;var e=document.createElement("style");e.type="text/css";var t=Nr();return t&&e.setAttribute("nonce",t),e}function Sr(e,t){e.styleSheet?e.styleSheet.cssText=t:e.appendChild(document.createTextNode(t))}function Rr(e){var t=document.head||document.getElementsByTagName("head")[0];t.appendChild(e)}var Ar=function(){var e=0,t=null;return{add:function(n){e==0&&(t=Mr())&&(Sr(t,n),Rr(t)),e++},remove:function(){e--,!e&&t&&(t.parentNode&&t.parentNode.removeChild(t),t=null)}}},Pr=function(){var e=Ar();return function(t,n){c.useEffect(function(){return e.add(t),function(){e.remove()}},[t&&n])}},Ve=function(){var e=Pr(),t=function(n){var r=n.styles,o=n.dynamic;return e(r,o),null};return t},Dr={left:0,top:0,right:0,gap:0},se=function(e){return parseInt(e||"",10)||0},Or=function(e){var t=window.getComputedStyle(document.body),n=t[e==="padding"?"paddingLeft":"marginLeft"],r=t[e==="padding"?"paddingTop":"marginTop"],o=t[e==="padding"?"paddingRight":"marginRight"];return[se(n),se(r),se(o)]},Lr=function(e){if(e===void 0&&(e="margin"),typeof window>"u")return Dr;var t=Or(e),n=document.documentElement.clientWidth,r=window.innerWidth;return{left:t[0],top:t[1],right:t[2],gap:Math.max(0,r-n+t[2]-t[0])}},Tr=Ve(),F="data-scroll-locked",Ir=function(e,t,n,r){var o=e.left,a=e.top,i=e.right,s=e.gap;return n===void 0&&(n="margin"),`
  .`.concat(mr,` {
   overflow: hidden `).concat(r,`;
   padding-right: `).concat(s,"px ").concat(r,`;
  }
  body[`).concat(F,`] {
    overflow: hidden `).concat(r,`;
    overscroll-behavior: contain;
    `).concat([t&&"position: relative ".concat(r,";"),n==="margin"&&`
    padding-left: `.concat(o,`px;
    padding-top: `).concat(a,`px;
    padding-right: `).concat(i,`px;
    margin-left:0;
    margin-top:0;
    margin-right: `).concat(s,"px ").concat(r,`;
    `),n==="padding"&&"padding-right: ".concat(s,"px ").concat(r,";")].filter(Boolean).join(""),`
  }
  
  .`).concat(H,` {
    right: `).concat(s,"px ").concat(r,`;
  }
  
  .`).concat(K,` {
    margin-right: `).concat(s,"px ").concat(r,`;
  }
  
  .`).concat(H," .").concat(H,` {
    right: 0 `).concat(r,`;
  }
  
  .`).concat(K," .").concat(K,` {
    margin-right: 0 `).concat(r,`;
  }
  
  body[`).concat(F,`] {
    `).concat(gr,": ").concat(s,`px;
  }
`)},Ne=function(){var e=parseInt(document.body.getAttribute(F)||"0",10);return isFinite(e)?e:0},$r=function(){c.useEffect(function(){return document.body.setAttribute(F,(Ne()+1).toString()),function(){var e=Ne()-1;e<=0?document.body.removeAttribute(F):document.body.setAttribute(F,e.toString())}},[])},jr=function(e){var t=e.noRelative,n=e.noImportant,r=e.gapMode,o=r===void 0?"margin":r;$r();var a=c.useMemo(function(){return Lr(o)},[o]);return c.createElement(Tr,{styles:Ir(a,!t,o,n?"":"!important")})},ue=!1;if(typeof window<"u")try{var q=Object.defineProperty({},"passive",{get:function(){return ue=!0,!0}});window.addEventListener("test",q,q),window.removeEventListener("test",q,q)}catch{ue=!1}var $=ue?{passive:!1}:!1,Fr=function(e){return e.tagName==="TEXTAREA"},He=function(e,t){if(!(e instanceof Element))return!1;var n=window.getComputedStyle(e);return n[t]!=="hidden"&&!(n.overflowY===n.overflowX&&!Fr(e)&&n[t]==="visible")},Wr=function(e){return He(e,"overflowY")},Br=function(e){return He(e,"overflowX")},Me=function(e,t){var n=t.ownerDocument,r=t;do{typeof ShadowRoot<"u"&&r instanceof ShadowRoot&&(r=r.host);var o=Ke(e,r);if(o){var a=Ge(e,r),i=a[1],s=a[2];if(i>s)return!0}r=r.parentNode}while(r&&r!==n.body);return!1},Ur=function(e){var t=e.scrollTop,n=e.scrollHeight,r=e.clientHeight;return[t,n,r]},zr=function(e){var t=e.scrollLeft,n=e.scrollWidth,r=e.clientWidth;return[t,n,r]},Ke=function(e,t){return e==="v"?Wr(t):Br(t)},Ge=function(e,t){return e==="v"?Ur(t):zr(t)},qr=function(e,t){return e==="h"&&t==="rtl"?-1:1},Vr=function(e,t,n,r,o){var a=qr(e,window.getComputedStyle(t).direction),i=a*r,s=n.target,f=t.contains(s),u=!1,h=i>0,v=0,y=0;do{var g=Ge(e,s),C=g[0],l=g[1],p=g[2],k=l-p-a*C;(C||k)&&Ke(e,s)&&(v+=k,y+=C),s instanceof ShadowRoot?s=s.host:s=s.parentNode}while(!f&&s!==document.body||f&&(t.contains(s)||t===s));return(h&&(Math.abs(v)<1||!o)||!h&&(Math.abs(y)<1||!o))&&(u=!0),u},V=function(e){return"changedTouches"in e?[e.changedTouches[0].clientX,e.changedTouches[0].clientY]:[0,0]},Se=function(e){return[e.deltaX,e.deltaY]},Re=function(e){return e&&"current"in e?e.current:e},Hr=function(e,t){return e[0]===t[0]&&e[1]===t[1]},Kr=function(e){return`
  .block-interactivity-`.concat(e,` {pointer-events: none;}
  .allow-interactivity-`).concat(e,` {pointer-events: all;}
`)},Gr=0,j=[];function Yr(e){var t=c.useRef([]),n=c.useRef([0,0]),r=c.useRef(),o=c.useState(Gr++)[0],a=c.useState(Ve)[0],i=c.useRef(e);c.useEffect(function(){i.current=e},[e]),c.useEffect(function(){if(e.inert){document.body.classList.add("block-interactivity-".concat(o));var l=pr([e.lockRef.current],(e.shards||[]).map(Re),!0).filter(Boolean);return l.forEach(function(p){return p.classList.add("allow-interactivity-".concat(o))}),function(){document.body.classList.remove("block-interactivity-".concat(o)),l.forEach(function(p){return p.classList.remove("allow-interactivity-".concat(o))})}}},[e.inert,e.lockRef.current,e.shards]);var s=c.useCallback(function(l,p){if("touches"in l&&l.touches.length===2||l.type==="wheel"&&l.ctrlKey)return!i.current.allowPinchZoom;var k=V(l),_=n.current,E="deltaX"in l?l.deltaX:_[0]-k[0],w="deltaY"in l?l.deltaY:_[1]-k[1],x,R=l.target,b=Math.abs(E)>Math.abs(w)?"h":"v";if("touches"in l&&b==="h"&&R.type==="range")return!1;var A=Me(b,R);if(!A)return!0;if(A?x=b:(x=b==="v"?"h":"v",A=Me(b,R)),!A)return!1;if(!r.current&&"changedTouches"in l&&(E||w)&&(r.current=x),!x)return!0;var W=r.current||x;return Vr(W,p,l,W==="h"?E:w,!0)},[]),f=c.useCallback(function(l){var p=l;if(!(!j.length||j[j.length-1]!==a)){var k="deltaY"in p?Se(p):V(p),_=t.current.filter(function(x){return x.name===p.type&&(x.target===p.target||p.target===x.shadowParent)&&Hr(x.delta,k)})[0];if(_&&_.should){p.cancelable&&p.preventDefault();return}if(!_){var E=(i.current.shards||[]).map(Re).filter(Boolean).filter(function(x){return x.contains(p.target)}),w=E.length>0?s(p,E[0]):!i.current.noIsolation;w&&p.cancelable&&p.preventDefault()}}},[]),u=c.useCallback(function(l,p,k,_){var E={name:l,delta:p,target:k,should:_,shadowParent:Xr(k)};t.current.push(E),setTimeout(function(){t.current=t.current.filter(function(w){return w!==E})},1)},[]),h=c.useCallback(function(l){n.current=V(l),r.current=void 0},[]),v=c.useCallback(function(l){u(l.type,Se(l),l.target,s(l,e.lockRef.current))},[]),y=c.useCallback(function(l){u(l.type,V(l),l.target,s(l,e.lockRef.current))},[]);c.useEffect(function(){return j.push(a),e.setCallbacks({onScrollCapture:v,onWheelCapture:v,onTouchMoveCapture:y}),document.addEventListener("wheel",f,$),document.addEventListener("touchmove",f,$),document.addEventListener("touchstart",h,$),function(){j=j.filter(function(l){return l!==a}),document.removeEventListener("wheel",f,$),document.removeEventListener("touchmove",f,$),document.removeEventListener("touchstart",h,$)}},[]);var g=e.removeScrollBar,C=e.inert;return c.createElement(c.Fragment,null,C?c.createElement(a,{styles:Kr(o)}):null,g?c.createElement(jr,{gapMode:e.gapMode}):null)}function Xr(e){for(var t=null;e!==null;)e instanceof ShadowRoot&&(t=e.host,e=e.host),e=e.parentNode;return t}const Zr=_r(qe,Yr);var Ye=c.forwardRef(function(e,t){return c.createElement(Q,M({},e,{ref:t,sideCar:Zr}))});Ye.classNames=Q.classNames;var fe="Dialog",[Xe,ba]=Ct(fe),[Qr,N]=Xe(fe),Ze=e=>{const{__scopeDialog:t,children:n,open:r,defaultOpen:o,onOpenChange:a,modal:i=!0}=e,s=c.useRef(null),f=c.useRef(null),[u=!1,h]=Ut({prop:r,defaultProp:o,onChange:a});return m.jsx(Qr,{scope:t,triggerRef:s,contentRef:f,contentId:ee(),titleId:ee(),descriptionId:ee(),open:u,onOpenChange:h,onOpenToggle:c.useCallback(()=>h(v=>!v),[h]),modal:i,children:n})};Ze.displayName=fe;var Qe="DialogTrigger",Je=c.forwardRef((e,t)=>{const{__scopeDialog:n,...r}=e,o=N(Qe,n),a=T(t,o.triggerRef);return m.jsx(S.button,{type:"button","aria-haspopup":"dialog","aria-expanded":o.open,"aria-controls":o.contentId,"data-state":ye(o.open),...r,ref:a,onClick:D(e.onClick,o.onOpenToggle)})});Je.displayName=Qe;var he="DialogPortal",[Jr,et]=Xe(he,{forceMount:void 0}),tt=e=>{const{__scopeDialog:t,forceMount:n,children:r,container:o}=e,a=N(he,t);return m.jsx(Jr,{scope:t,forceMount:n,children:c.Children.map(r,i=>m.jsx(Z,{present:n||a.open,children:m.jsx($e,{asChild:!0,container:o,children:i})}))})};tt.displayName=he;var Y="DialogOverlay",nt=c.forwardRef((e,t)=>{const n=et(Y,e.__scopeDialog),{forceMount:r=n.forceMount,...o}=e,a=N(Y,e.__scopeDialog);return a.modal?m.jsx(Z,{present:r||a.open,children:m.jsx(to,{...o,ref:t})}):null});nt.displayName=Y;var eo=le("DialogOverlay.RemoveScroll"),to=c.forwardRef((e,t)=>{const{__scopeDialog:n,...r}=e,o=N(Y,n);return m.jsx(Ye,{as:eo,allowPinchZoom:!0,shards:[o.contentRef],children:m.jsx(S.div,{"data-state":ye(o.open),...r,ref:t,style:{pointerEvents:"auto",...r.style}})})}),L="DialogContent",rt=c.forwardRef((e,t)=>{const n=et(L,e.__scopeDialog),{forceMount:r=n.forceMount,...o}=e,a=N(L,e.__scopeDialog);return m.jsx(Z,{present:r||a.open,children:a.modal?m.jsx(no,{...o,ref:t}):m.jsx(ro,{...o,ref:t})})});rt.displayName=L;var no=c.forwardRef((e,t)=>{const n=N(L,e.__scopeDialog),r=c.useRef(null),o=T(t,n.contentRef,r);return c.useEffect(()=>{const a=r.current;if(a)return yr(a)},[]),m.jsx(ot,{...e,ref:o,trapFocus:n.open,disableOutsidePointerEvents:!0,onCloseAutoFocus:D(e.onCloseAutoFocus,a=>{var i;a.preventDefault(),(i=n.triggerRef.current)==null||i.focus()}),onPointerDownOutside:D(e.onPointerDownOutside,a=>{const i=a.detail.originalEvent,s=i.button===0&&i.ctrlKey===!0;(i.button===2||s)&&a.preventDefault()}),onFocusOutside:D(e.onFocusOutside,a=>a.preventDefault())})}),ro=c.forwardRef((e,t)=>{const n=N(L,e.__scopeDialog),r=c.useRef(!1),o=c.useRef(!1);return m.jsx(ot,{...e,ref:t,trapFocus:!1,disableOutsidePointerEvents:!1,onCloseAutoFocus:a=>{var i,s;(i=e.onCloseAutoFocus)==null||i.call(e,a),a.defaultPrevented||(r.current||(s=n.triggerRef.current)==null||s.focus(),a.preventDefault()),r.current=!1,o.current=!1},onInteractOutside:a=>{var f,u;(f=e.onInteractOutside)==null||f.call(e,a),a.defaultPrevented||(r.current=!0,a.detail.originalEvent.type==="pointerdown"&&(o.current=!0));const i=a.target;((u=n.triggerRef.current)==null?void 0:u.contains(i))&&a.preventDefault(),a.detail.originalEvent.type==="focusin"&&o.current&&a.preventDefault()}})}),ot=c.forwardRef((e,t)=>{const{__scopeDialog:n,trapFocus:r,onOpenAutoFocus:o,onCloseAutoFocus:a,...i}=e,s=N(L,n),f=c.useRef(null),u=T(t,f);return or(),m.jsxs(m.Fragment,{children:[m.jsx(Fe,{asChild:!0,loop:!0,trapped:r,onMountAutoFocus:o,onUnmountAutoFocus:a,children:m.jsx(de,{role:"dialog",id:s.contentId,"aria-describedby":s.descriptionId,"aria-labelledby":s.titleId,"data-state":ye(s.open),...i,ref:u,onDismiss:()=>s.onOpenChange(!1)})}),m.jsxs(m.Fragment,{children:[m.jsx(oo,{titleId:s.titleId}),m.jsx(co,{contentRef:f,descriptionId:s.descriptionId})]})]})}),ve="DialogTitle",at=c.forwardRef((e,t)=>{const{__scopeDialog:n,...r}=e,o=N(ve,n);return m.jsx(S.h2,{id:o.titleId,...r,ref:t})});at.displayName=ve;var ct="DialogDescription",st=c.forwardRef((e,t)=>{const{__scopeDialog:n,...r}=e,o=N(ct,n);return m.jsx(S.p,{id:o.descriptionId,...r,ref:t})});st.displayName=ct;var it="DialogClose",ut=c.forwardRef((e,t)=>{const{__scopeDialog:n,...r}=e,o=N(it,n);return m.jsx(S.button,{type:"button",...r,ref:t,onClick:D(e.onClick,()=>o.onOpenChange(!1))})});ut.displayName=it;function ye(e){return e?"open":"closed"}var lt="DialogTitleWarning",[Ea,dt]=wt(lt,{contentName:L,titleName:ve,docsSlug:"dialog"}),oo=({titleId:e})=>{const t=dt(lt),n=`\`${t.contentName}\` requires a \`${t.titleName}\` for the component to be accessible for screen reader users.

If you want to hide the \`${t.titleName}\`, you can wrap it with our VisuallyHidden component.

For more information, see https://radix-ui.com/primitives/docs/components/${t.docsSlug}`;return c.useEffect(()=>{e&&(document.getElementById(e)||console.error(n))},[n,e]),null},ao="DialogDescriptionWarning",co=({contentRef:e,descriptionId:t})=>{const r=`Warning: Missing \`Description\` or \`aria-describedby={undefined}\` for {${dt(ao).contentName}}.`;return c.useEffect(()=>{var a;const o=(a=e.current)==null?void 0:a.getAttribute("aria-describedby");t&&o&&(document.getElementById(t)||console.warn(r))},[r,e,t]),null},wa=Ze,Ca=Je,xa=tt,_a=nt,Na=rt,Ma=at,Sa=st,Ra=ut,so="Label",ft=c.forwardRef((e,t)=>m.jsx(S.label,{...e,ref:t,onMouseDown:n=>{var o;n.target.closest("button, input, select, textarea")||((o=e.onMouseDown)==null||o.call(e,n),!n.defaultPrevented&&n.detail>1&&n.preventDefault())}}));ft.displayName=so;var Aa=ft;export{Aa as $,yr as A,ho as B,Oo as C,de as D,or as E,Fe as F,De as G,Ye as H,So as I,_o as J,Io as K,Ho as L,ta as M,Na as N,_a as O,S as P,Ra as Q,fo as R,uo as S,la as T,ma as U,Ma as V,Sa as W,ga as X,xa as Y,ka as Z,wa as _,le as a,Qo as a0,na as a1,No as a2,qo as a3,Jo as a4,ba as a5,Ca as a6,Ea as a7,ko as a8,Ro as a9,Ko as aA,jo as aB,Uo as aC,aa as aD,ca as aa,Do as ab,To as ac,Wo as ad,yo as ae,Bo as af,da as ag,ya as ah,va as ai,Yo as aj,Fo as ak,Go as al,po as am,oa as an,ia as ao,pa as ap,ea as aq,ua as ar,mo as as,Mo as at,vo as au,xo as av,sa as aw,Eo as ax,zo as ay,ra as az,Ut as b,Ct as c,Z as d,O as e,D as f,$e as g,G as h,At as i,m as j,lo as k,ee as l,Po as m,Ao as n,Vo as o,Xo as p,bo as q,Co as r,go as s,Zo as t,T as u,wo as v,fa as w,ha as x,$o as y,Lo as z};
