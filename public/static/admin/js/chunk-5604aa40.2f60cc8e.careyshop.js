(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-5604aa40"],{"2f46":function(e,t,i){"use strict";i.d(t,"c",(function(){return r})),i.d(t,"a",(function(){return l})),i.d(t,"d",(function(){return s})),i.d(t,"b",(function(){return u}));var n=i("5530"),o=i("bc07"),a="/v1/user_level";function r(){return Object(o["a"])({url:a,method:"post",data:{method:"get.user.level.list"}})}function l(e){return Object(o["a"])({url:a,method:"post",data:Object(n["a"])({method:"add.user.level.item"},e)})}function s(e){return Object(o["a"])({url:a,method:"post",data:Object(n["a"])({method:"set.user.level.item"},e)})}function u(e){return Object(o["a"])({url:a,method:"post",data:{method:"del.user.level.list",user_level_id:e}})}},"4de4f":function(e,t,i){"use strict";i.d(t,"e",(function(){return r})),i.d(t,"d",(function(){return l})),i.d(t,"c",(function(){return s})),i.d(t,"a",(function(){return u})),i.d(t,"f",(function(){return c})),i.d(t,"b",(function(){return d}));var n=i("5530"),o=i("bc07"),a="/v1/coupon_give";function r(e){return Object(o["a"])({url:a,method:"post",data:Object(n["a"])({method:"give.coupon.user"},e)})}function l(e,t){return Object(o["a"])({url:a,method:"post",data:{method:"give.coupon.live",coupon_id:e,give_number:t}})}function s(e){return Object(o["a"])({url:a,method:"post",data:Object(n["a"])({method:"get.coupon.give.list"},e)})}function u(e){return Object(o["a"])({url:a,method:"post",data:{method:"del.coupon.give.list",coupon_give_id:e}})}function c(e){return Object(o["a"])({url:a,method:"post",data:{method:"rec.coupon.give.list",coupon_give_id:e}})}function d(e){return Object(o["a"])({url:a,method:"post",data:{method:"get.coupon.give.export",coupon_id:e}})}},"707d":function(e,t,i){"use strict";
/*! *****************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
function n(e,t,i,n){return new(i||(i=Promise))((function(o,a){function r(e){try{s(n.next(e))}catch(e){a(e)}}function l(e){try{s(n.throw(e))}catch(e){a(e)}}function s(e){var t;e.done?o(e.value):(t=e.value,t instanceof i?t:new i((function(e){e(t)}))).then(r,l)}s((n=n.apply(e,t||[])).next())}))}function o(e,t){var i,n,o,a,r={label:0,sent:function(){if(1&o[0])throw o[1];return o[1]},trys:[],ops:[]};return a={next:l(0),throw:l(1),return:l(2)},"function"==typeof Symbol&&(a[Symbol.iterator]=function(){return this}),a;function l(a){return function(l){return function(a){if(i)throw new TypeError("Generator is already executing.");for(;r;)try{if(i=1,n&&(o=2&a[0]?n.return:a[0]?n.throw||((o=n.return)&&o.call(n),0):n.next)&&!(o=o.call(n,a[1])).done)return o;switch(n=0,o&&(a=[2&a[0],o.value]),a[0]){case 0:case 1:o=a;break;case 4:return r.label++,{value:a[1],done:!1};case 5:r.label++,n=a[1],a=[0];continue;case 7:a=r.ops.pop(),r.trys.pop();continue;default:if(o=r.trys,!((o=o.length>0&&o[o.length-1])||6!==a[0]&&2!==a[0])){r=0;continue}if(3===a[0]&&(!o||a[1]>o[0]&&a[1]<o[3])){r.label=a[1];break}if(6===a[0]&&r.label<o[1]){r.label=o[1],o=a;break}if(o&&r.label<o[2]){r.label=o[2],r.ops.push(a);break}o[2]&&r.ops.pop(),r.trys.pop();continue}a=t.call(e,r)}catch(e){a=[6,e],n=0}finally{i=o=0}if(5&a[0])throw a[1];return{value:a[0]?a[1]:void 0,done:!0}}([a,l])}}}i.d(t,"a",(function(){return $}));var a=function(e){};function r(e){a(e)}(function(){(console.warn||console.log).apply(console,arguments)}).bind("[clipboard-polyfill]");var l,s,u,c,d="undefined"==typeof navigator?void 0:navigator,m=null==d?void 0:d.clipboard,p=(null===(l=null==m?void 0:m.read)||void 0===l||l.bind(m),null===(s=null==m?void 0:m.readText)||void 0===s||s.bind(m),null===(u=null==m?void 0:m.write)||void 0===u||u.bind(m),null===(c=null==m?void 0:m.writeText)||void 0===c?void 0:c.bind(m)),f="undefined"==typeof window?void 0:window,v=(null==f||f.ClipboardItem,f);function g(){return"undefined"==typeof ClipboardEvent&&void 0!==v.clipboardData&&void 0!==v.clipboardData.setData}var h=function(){this.success=!1};function b(e,t,i){for(var n in r("listener called"),e.success=!0,t){var o=t[n],a=i.clipboardData;a.setData(n,o),"text/plain"===n&&a.getData(n)!==o&&(r("setting text/plain failed"),e.success=!1)}i.preventDefault()}function _(e){var t=new h,i=b.bind(this,t,e);document.addEventListener("copy",i);try{document.execCommand("copy")}finally{document.removeEventListener("copy",i)}return t.success}function y(e,t){w(e);var i=_(t);return x(),i}function w(e){var t=document.getSelection();if(t){var i=document.createRange();i.selectNodeContents(e),t.removeAllRanges(),t.addRange(i)}}function x(){var e=document.getSelection();e&&e.removeAllRanges()}function k(e){return n(this,void 0,void 0,(function(){var t;return o(this,(function(i){if(t="text/plain"in e,g()){if(!t)throw new Error("No `text/plain` value was specified.");if(n=e["text/plain"],v.clipboardData.setData("Text",n))return[2,!0];throw new Error("Copying failed, possibly because the user rejected it.")}var n;return _(e)?(r("regular execCopy worked"),[2,!0]):navigator.userAgent.indexOf("Edge")>-1?(r('UA "Edge" => assuming success'),[2,!0]):y(document.body,e)?(r("copyUsingTempSelection worked"),[2,!0]):function(e){var t=document.createElement("div");t.setAttribute("style","-webkit-user-select: text !important"),t.textContent="temporary element",document.body.appendChild(t);var i=y(t,e);return document.body.removeChild(t),i}(e)?(r("copyUsingTempElem worked"),[2,!0]):function(e){r("copyTextUsingDOM");var t=document.createElement("div");t.setAttribute("style","-webkit-user-select: text !important");var i=t;t.attachShadow&&(r("Using shadow DOM."),i=t.attachShadow({mode:"open"}));var n=document.createElement("span");n.innerText=e,i.appendChild(n),document.body.appendChild(t),w(n);var o=document.execCommand("copy");return x(),document.body.removeChild(t),o}(e["text/plain"])?(r("copyTextUsingDOM worked"),[2,!0]):[2,!1]}))}))}function $(e){return n(this,void 0,void 0,(function(){return o(this,(function(t){if(p)return r("Using `navigator.clipboard.writeText()`."),[2,p(e)];if(!k(function(e){var t={};return t["text/plain"]=e,t}(e)))throw new Error("writeText() failed");return[2]}))}))}(function(){function e(e,t){var i;for(var n in void 0===t&&(t={}),this.types=Object.keys(e),this._items={},e){var o=e[n];this._items[n]="string"==typeof o?O(n,o):o}this.presentationStyle=null!==(i=null==t?void 0:t.presentationStyle)&&void 0!==i?i:"unspecified"}e.prototype.getType=function(e){return n(this,void 0,void 0,(function(){return o(this,(function(t){return[2,this._items[e]]}))}))}})();function O(e,t){return new Blob([t],{type:e})}},b254:function(e,t,i){},b4e1:function(e,t,i){"use strict";i.r(t);var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"cs-p"},[i("el-form",{attrs:{inline:!0,size:"small"}},[e.auth.add?i("el-form-item",[i("el-button",{attrs:{icon:"el-icon-plus",disabled:e.loading},on:{click:e.handleCreate}},[e._v("新增优惠劵")])],1):e._e(),e.auth.enable||e.auth.disable?i("el-form-item",[i("el-button-group",[e.auth.enable?i("el-button",{attrs:{icon:"el-icon-check",disabled:e.loading},on:{click:function(t){return e.handleStatus(null,1,!0)}}},[e._v("启用")]):e._e(),e.auth.disable?i("el-button",{attrs:{icon:"el-icon-close",disabled:e.loading},on:{click:function(t){return e.handleStatus(null,0,!0)}}},[e._v("禁用")]):e._e()],1)],1):e._e(),e.auth.normal||e.auth.invalid?i("el-form-item",[i("el-button-group",[e.auth.normal?i("el-button",{attrs:{icon:"el-icon-circle-check",disabled:e.loading},on:{click:function(t){return e.handleInvalid(null,0,!0)}}},[e._v("正常")]):e._e(),e.auth.invalid?i("el-button",{attrs:{icon:"el-icon-circle-close",disabled:e.loading},on:{click:function(t){return e.handleInvalid(null,1,!0)}}},[e._v("作废")]):e._e()],1)],1):e._e(),e.auth.del?i("el-form-item",[i("el-button",{attrs:{icon:"el-icon-delete",disabled:e.loading},on:{click:function(t){return e.handleDelete(null)}}},[e._v("删除")])],1):e._e(),i("cs-help",{staticStyle:{"padding-bottom":"19px"},attrs:{router:e.$route.path}})],1),i("el-table",{attrs:{data:e.currentTableData,"highlight-current-row":!0},on:{"selection-change":e.handleSelectionChange,"sort-change":e.sortChange}},[i("el-table-column",{attrs:{align:"center",type:"selection",width:"55"}}),i("el-table-column",{attrs:{type:"expand"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("el-form",{staticClass:"table-expand",attrs:{"label-position":"left",inline:""}},[i("el-form-item",{attrs:{label:"名称"}},[i("span",[e._v(e._s(t.row.name))])]),i("el-form-item",{attrs:{label:"描述"}},[i("span",[e._v(e._s(t.row.description))])]),i("el-form-item",{attrs:{label:"类型"}},[i("span",[e._v(e._s(e.typeMap[t.row.type]))])]),i("el-form-item",{attrs:{label:"领取码"}},[i("span",[e._v(e._s(t.row.give_code))])]),i("el-form-item",{attrs:{label:"优惠金额"}},[i("span",[e._v(e._s(e._f("getNumber")(t.row.money)))])]),i("el-form-item",{attrs:{label:"使用门槛"}},[i("span",[e._v("满 "+e._s(e._f("getNumber")(t.row.quota)))])]),i("el-form-item",{attrs:{label:"限领次数"}},[i("span",[e._v(e._s(t.row.frequency||"不限次数"))])]),i("el-form-item",{attrs:{label:"发放数"}},[i("span",[e._v(e._s(t.row.give_num))])]),i("el-form-item",{attrs:{label:"领取数"}},[i("span",[e._v(e._s(t.row.receive_num))])]),i("el-form-item",{attrs:{label:"使用数"}},[i("span",[e._v(e._s(t.row.use_num))])]),i("el-form-item",{attrs:{label:"状态"}},[i("el-tag",{attrs:{type:e.statusMap[t.row.status].type,effect:"plain",size:"mini"}},[e._v(" "+e._s(e.statusMap[t.row.status].text)+" ")])],1),i("el-form-item",{attrs:{label:"是否有效"}},[i("el-tag",{attrs:{type:e.invalidMap[t.row.is_invalid].type,effect:"plain",size:"mini"}},[e._v(" "+e._s(e.invalidMap[t.row.is_invalid].text)+" ")])],1),i("el-divider"),i("el-form-item",{attrs:{label:"发放开始日期"}},[i("span",[e._v(e._s(t.row.give_begin_time))])]),i("el-form-item",{attrs:{label:"发放结束日期"}},[i("span",[e._v(e._s(t.row.give_end_time))])]),i("el-form-item",{attrs:{label:"使用开始日期"}},[i("span",[e._v(e._s(t.row.use_begin_time))])]),i("el-form-item",{attrs:{label:"使用截止日期"}},[i("span",[e._v(e._s(t.row.use_end_time))])])],1)]}}])}),i("el-table-column",{attrs:{label:"名称",prop:"name",sortable:"custom","min-width":"180","show-overflow-tooltip":!0},scopedSlots:e._u([{key:"default",fn:function(t){return[t.row.description?i("el-tooltip",{attrs:{content:"描述："+t.row.description,placement:"top-start"}},[i("i",{staticClass:"el-icon-tickets cs-pr-5"})]):e._e(),i("span",{class:{link:e.auth.use,"cs-expired":e.dayjs().isAfter(e.dayjs(t.row.use_end_time))},on:{click:function(i){return e.handleGive(t.row.coupon_id)}}},[e._v(e._s(t.row.name))])]}}])}),i("el-table-column",{attrs:{label:"类型",sortable:"custom",prop:"type"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v(" "+e._s(e.typeMap[t.row.type])+" ")]}}])}),i("el-table-column",{attrs:{label:"发放数",sortable:"custom",prop:"give_num"}}),i("el-table-column",{attrs:{label:"优惠金额",prop:"money"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v(" "+e._s(e._f("getNumber")(t.row.money))+" ")]}}])}),i("el-table-column",{attrs:{label:"使用门槛",prop:"quota"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v(" 满 "+e._s(e._f("getNumber")(t.row.quota))+" ")]}}])}),i("el-table-column",{attrs:{label:"状态",sortable:"custom",prop:"status",align:"center",width:"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("el-tag",{style:e.auth.enable||e.auth.disable?"cursor: pointer;":"",attrs:{size:"mini",type:e.statusMap[t.row.status].type,effect:e.auth.enable||e.auth.disable?"light":"plain"},nativeOn:{click:function(i){return e.handleStatus(t.$index)}}},[e._v(" "+e._s(e.statusMap[t.row.status].text)+" ")])]}}])}),i("el-table-column",{attrs:{label:"是否有效",sortable:"custom",prop:"is_invalid",align:"center",width:"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("el-tag",{style:e.auth.normal||e.auth.invalid?"cursor: pointer;":"",attrs:{size:"mini",type:e.invalidMap[t.row.is_invalid].type,effect:e.auth.normal||e.auth.invalid?"light":"plain"},nativeOn:{click:function(i){return e.handleInvalid(t.$index)}}},[e._v(" "+e._s(e.invalidMap[t.row.is_invalid].text)+" ")])]}}])}),i("el-table-column",{attrs:{label:"操作",align:"center","min-width":"120"},scopedSlots:e._u([{key:"default",fn:function(t){return[e.auth.set?i("el-button",{attrs:{size:"small",type:"text"},on:{click:function(i){return e.handleUpdate(t.$index)}}},[e._v("编辑")]):e._e(),e.auth.del?i("el-button",{attrs:{size:"small",type:"text"},on:{click:function(i){return e.handleDelete(t.$index)}}},[e._v("删除")]):e._e(),3!==t.row.type&&e.auth.give?i("el-dropdown",{attrs:{"show-timeout":50,size:"small"}},[i("el-button",{staticClass:"cs-ml-10",attrs:{size:"small",type:"text"}},[e._v("发放操作")]),i("el-dropdown-menu",{attrs:{slot:"dropdown"},slot:"dropdown"},[2===t.row.type?[i("el-dropdown-item",{nativeOn:{click:function(i){return e.handleCopyGiveCode(t.row.give_code)}}},[e._v(" 复制领取码 ")]),i("el-dropdown-item",{nativeOn:{click:function(i){return e.handleCopyGuide(t.$index)}}},[e._v(" 复制领取地址 ")])]:e._e(),1===t.row.type?[i("el-dropdown-item",{nativeOn:{click:function(i){return e.handleLive(t.$index)}}},[e._v(" 生成优惠劵 ")]),i("el-dropdown-item",{nativeOn:{click:function(i){return e.handleExport(t.$index)}}},[e._v(" 导出优惠劵 ")])]:e._e(),0===t.row.type?[i("el-dropdown-item",{nativeOn:{click:function(i){return e.handleGiveUser("user",t.$index)}}},[e._v(" 会员账号发放 ")]),i("el-dropdown-item",{nativeOn:{click:function(i){return e.handleGiveUser("level",t.$index)}}},[e._v(" 账号等级发放 ")])]:e._e()],2)],1):e._e()]}}])})],1),i("el-dialog",{attrs:{title:e.textMap[e.dialogStatus],visible:e.dialogFormVisible,"append-to-body":!0,"close-on-click-modal":!1,width:"670px"},on:{"update:visible":function(t){e.dialogFormVisible=t}}},[i("el-form",{ref:"form",attrs:{model:e.form,rules:e.rules,"label-width":"110px"}},[i("el-form-item",{attrs:{label:"名称",prop:"name"}},[i("el-input",{attrs:{placeholder:"请输入优惠劵名称",clearable:!0},model:{value:e.form.name,callback:function(t){e.$set(e.form,"name",t)},expression:"form.name"}})],1),i("el-form-item",{attrs:{label:"描述",prop:"description"}},[i("el-input",{attrs:{type:"textarea",placeholder:"可输入优惠劵描述",autosize:{minRows:3},maxlength:"255","show-word-limit":""},model:{value:e.form.description,callback:function(t){e.$set(e.form,"description",t)},expression:"form.description"}})],1),"2"===e.form.type?i("el-form-item",{attrs:{label:"引导地址",prop:"guide"}},[i("el-input",{attrs:{placeholder:"可输入优惠劵引导地址",clearable:!0},model:{value:e.form.guide,callback:function(t){e.$set(e.form,"guide",t)},expression:"form.guide"}}),i("div",{staticClass:"help-block"},[i("span",[e._v("引导顾客到特定的页面上进行领取")])])],1):e._e(),i("el-row",{attrs:{gutter:20}},[i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"类型",prop:"type"}},[i("el-select",{attrs:{placeholder:"请选择",disabled:"create"!==e.dialogStatus,clearable:""},model:{value:e.form.type,callback:function(t){e.$set(e.form,"type",t)},expression:"form.type"}},e._l(e.typeMap,(function(e,t){return i("el-option",{key:t,attrs:{label:e,value:t}})})),1)],1)],1),i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"发放数",prop:"give_num"}},[i("el-input-number",{attrs:{placeholder:"请输入发放数","controls-position":"right",min:0},model:{value:e.form.give_num,callback:function(t){e.$set(e.form,"give_num",t)},expression:"form.give_num"}})],1)],1)],1),i("el-row",{attrs:{gutter:20}},[i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"优惠金额",prop:"money"}},[i("el-input-number",{attrs:{placeholder:"请输入优惠金额","controls-position":"right",precision:2,min:0},model:{value:e.form.money,callback:function(t){e.$set(e.form,"money",t)},expression:"form.money"}})],1)],1),i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"使用门槛",prop:"quota"}},[i("el-input-number",{attrs:{placeholder:"请输入使用门槛","controls-position":"right",precision:2,min:0},model:{value:e.form.quota,callback:function(t){e.$set(e.form,"quota",t)},expression:"form.quota"}})],1)],1)],1),i("el-form-item",{attrs:{label:"指定分类",prop:"category"}},[i("cs-goods-category",{attrs:{type:"all"},model:{value:e.form.category,callback:function(t){e.$set(e.form,"category",t)},expression:"form.category"}},[i("el-button",{attrs:{slot:"control"},slot:"control"},[e._v("商品分类选取")])],1),i("div",{staticClass:"help-block"},[i("span",[e._v("指定商品分类后，该优惠劵只能对分类范围内的商品有效")])])],1),i("el-form-item",{attrs:{label:"排除分类",prop:"exclude_category"}},[i("cs-goods-category",{attrs:{type:"all"},model:{value:e.form.exclude_category,callback:function(t){e.$set(e.form,"exclude_category",t)},expression:"form.exclude_category"}},[i("el-button",{attrs:{slot:"control"},slot:"control"},[e._v("商品分类选取")])],1),i("div",{staticClass:"help-block"},[i("span",[e._v("排除商品分类后，该优惠劵对分类范围内的商品没有效果")])])],1),"2"===e.form.type?i("el-row",{attrs:{gutter:20}},[i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"会员等级",prop:"level"}},[i("el-select",{attrs:{placeholder:"不选表示全部有效","collapse-tags":"",multiple:""},model:{value:e.form.level,callback:function(t){e.$set(e.form,"level",t)},expression:"form.level"}},e._l(e.userLevel,(function(e){return i("el-option",{key:e.user_level_id,attrs:{label:e.name,value:e.user_level_id}})})),1)],1)],1),i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"限领次数",prop:"frequency"}},[i("el-input-number",{attrs:{placeholder:"可输入限领次数","controls-position":"right",max:255,min:0},model:{value:e.form.frequency,callback:function(t){e.$set(e.form,"frequency",t)},expression:"form.frequency"}})],1)],1)],1):e._e(),i("el-row",{attrs:{gutter:20}},[i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"发放开始日期",prop:"give_begin_time"}},[i("el-date-picker",{staticStyle:{width:"100%"},attrs:{type:"datetime","value-format":"yyyy-MM-dd HH:mm:ss",placeholder:"请选择发放开始日期"},model:{value:e.form.give_begin_time,callback:function(t){e.$set(e.form,"give_begin_time",t)},expression:"form.give_begin_time"}})],1)],1),i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"发放结束日期",prop:"give_end_time"}},[i("el-date-picker",{staticStyle:{width:"100%"},attrs:{type:"datetime","value-format":"yyyy-MM-dd HH:mm:ss",placeholder:"请选择发放开始日期"},model:{value:e.form.give_end_time,callback:function(t){e.$set(e.form,"give_end_time",t)},expression:"form.give_end_time"}})],1)],1)],1),i("el-row",{attrs:{gutter:20}},[i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"使用开始日期",prop:"use_begin_time"}},[i("el-date-picker",{staticStyle:{width:"100%"},attrs:{type:"datetime","value-format":"yyyy-MM-dd HH:mm:ss",placeholder:"请选择使用开始日期"},model:{value:e.form.use_begin_time,callback:function(t){e.$set(e.form,"use_begin_time",t)},expression:"form.use_begin_time"}})],1)],1),i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"使用截止日期",prop:"use_end_time"}},[i("el-date-picker",{staticStyle:{width:"100%"},attrs:{type:"datetime","value-format":"yyyy-MM-dd HH:mm:ss",placeholder:"请选择使用截止日期"},model:{value:e.form.use_end_time,callback:function(t){e.$set(e.form,"use_end_time",t)},expression:"form.use_end_time"}})],1)],1)],1),i("el-row",{attrs:{gutter:20}},[i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"状态",prop:"status"}},[i("el-switch",{attrs:{"active-value":1,"inactive-value":0},model:{value:e.form.status,callback:function(t){e.$set(e.form,"status",t)},expression:"form.status"}})],1)],1),i("el-col",{attrs:{span:12}},[i("el-form-item",{attrs:{label:"是否有效",prop:"is_invalid"}},[i("el-switch",{attrs:{"active-value":0,"inactive-value":1},model:{value:e.form.is_invalid,callback:function(t){e.$set(e.form,"is_invalid",t)},expression:"form.is_invalid"}})],1)],1)],1)],1),i("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[i("el-button",{attrs:{size:"small"},on:{click:function(t){e.dialogFormVisible=!1}}},[e._v("取消")]),"create"===e.dialogStatus?i("el-button",{attrs:{type:"primary",loading:e.dialogLoading,size:"small"},on:{click:e.create}},[e._v("确定")]):i("el-button",{attrs:{type:"primary",loading:e.dialogLoading,size:"small"},on:{click:e.update}},[e._v("修改")])],1)],1),i("el-dialog",{attrs:{title:"发放优惠劵",visible:e.dialogGiveFormVisible,"append-to-body":!0,"close-on-click-modal":!1,"destroy-on-close":!0,width:"600px"},on:{"update:visible":function(t){e.dialogGiveFormVisible=t}}},[i("el-form",["level"===e.dialogGiveType?i("el-form-item",{attrs:{label:"会员等级"}},[i("el-select",{attrs:{placeholder:"请选择","collapse-tags":"",multiple:""},model:{value:e.giveForm.user_level_id,callback:function(t){e.$set(e.giveForm,"user_level_id",t)},expression:"giveForm.user_level_id"}},e._l(e.userLevel,(function(e){return i("el-option",{key:e.user_level_id,attrs:{label:e.name,value:e.user_level_id}})})),1)],1):e._e(),"user"===e.dialogGiveType?i("el-form-item",{attrs:{label:"会员账号"}},[i("cs-user-select",{on:{confirm:e.handleUserConfirm}},[i("el-button",{attrs:{slot:"control"},slot:"control"},[e._v("账号选取")])],1)],1):e._e()],1),i("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[i("div",{staticStyle:{float:"left","font-size":"13px"}},[i("span",[e._v("剩余 "+e._s(e.giveNumber)+" 张优惠劵可发放")])]),i("el-button",{attrs:{size:"small"},on:{click:function(t){e.dialogGiveFormVisible=!1}}},[e._v("取消")]),i("el-button",{attrs:{type:"primary",loading:e.dialogGiveLoading,size:"small"},on:{click:e.giveCoupon}},[e._v("确定")])],1)],1)],1)},o=[],a=i("5530"),r=(i("d3b7"),i("3ca3"),i("ddb0"),i("159b"),i("25f0"),i("a9e3"),i("b0c0"),i("0572")),l=i("4de4f"),s=i("707d"),u=i("ca00"),c=i("5a0c"),d=i.n(c),m=i("2f46"),p=i("64f1"),f=i.n(p),v={components:{csUserSelect:function(){return i.e("chunk-47b1ee08").then(i.bind(null,"b903"))},csGoodsCategory:function(){return i.e("chunk-fc4a9a92").then(i.bind(null,"9da0"))}},props:{loading:{default:!1},typeMap:{default:function(){}},tableData:{default:function(){return[]}}},data:function(){return{dayjs:d.a,currentTableData:[],multipleSelection:[],userLevel:[],dialogLoading:!1,dialogFormVisible:!1,dialogStatus:"",textMap:{update:"编辑优惠劵",create:"新增优惠劵"},statusMap:{0:{text:"禁用",type:"danger"},1:{text:"启用",type:"success"},2:{text:"...",type:"info"}},invalidMap:{0:{text:"正常",type:"success"},1:{text:"作废",type:"danger"},2:{text:"...",type:"info"}},auth:{use:!1,add:!1,set:!1,del:!1,give:!1,enable:!1,disable:!1,normal:!1,invalid:!1},form:{name:void 0,description:void 0,guide:void 0,type:void 0,money:void 0,quota:void 0,category:void 0,exclude_category:void 0,level:void 0,frequency:void 0,give_num:void 0,give_begin_time:void 0,give_end_time:void 0,use_begin_time:void 0,use_end_time:void 0,status:void 0,is_invalid:void 0},rules:{name:[{required:!0,message:"名称不能为空",trigger:"blur"},{max:50,message:"长度不能大于 50 个字符",trigger:"blur"}],description:[{max:255,message:"长度不能大于 255 个字符",trigger:"blur"}],guide:[{max:255,message:"长度不能大于 255 个字符",trigger:"blur"}],type:[{required:!0,message:"至少选择一项",trigger:"change"}],money:[{required:!0,message:"优惠金额不能为空",trigger:"blur"}],quota:[{required:!0,message:"使用门槛不能为空",trigger:"blur"}],give_num:[{required:!0,message:"发放数不能为空",trigger:"blur"}],give_begin_time:[{required:!0,message:"发放开始日期不能为空",trigger:"change"}],give_end_time:[{required:!0,message:"发放结束日期不能为空",trigger:"change"}],use_begin_time:[{required:!0,message:"使用开始日期不能为空",trigger:"change"}],use_end_time:[{required:!0,message:"使用截止日期不能为空",trigger:"change"}]},dialogGiveLoading:!1,dialogGiveFormVisible:!1,dialogGiveType:"",giveNumber:0,giveForm:{coupon_id:void 0,username:void 0,user_level_id:void 0}}},filters:{getNumber:function(e){return u["a"].getNumber(e)}},watch:{tableData:{handler:function(e){this.currentTableData=e},immediate:!0}},mounted:function(){var e=this;this._validationAuth(),Object(m["c"])().then((function(t){e.userLevel=t.data||[]}))},methods:{_validationAuth:function(){this.auth.use=this.$permission("/marketing/coupon/give/list"),this.auth.add=this.$permission("/marketing/coupon/list/add"),this.auth.set=this.$permission("/marketing/coupon/list/set"),this.auth.del=this.$permission("/marketing/coupon/list/del"),this.auth.give=this.$permission("/marketing/coupon/list/give"),this.auth.enable=this.$permission("/marketing/coupon/list/enable"),this.auth.disable=this.$permission("/marketing/coupon/list/disable"),this.auth.normal=this.$permission("/marketing/coupon/list/normal"),this.auth.invalid=this.$permission("/marketing/coupon/list/invalid")},_getIdList:function(e){null===e&&(e=this.multipleSelection);var t=[];return Array.isArray(e)?e.forEach((function(e){t.push(e.coupon_id)})):t.push(this.currentTableData[e].coupon_id),t},handleSelectionChange:function(e){this.multipleSelection=e},sortChange:function(e){var t=e.column,i=e.prop,n=e.order,o={order_type:void 0,order_field:void 0};t&&n&&(o.order_type="ascending"===n?"asc":"desc",o.order_field=i),this.$emit("sort",o)},handleGive:function(e){this.auth.use&&this.$router.push({name:"marketing-coupon-give",params:{coupon_id:e}})},handleDelete:function(e){var t=this,i=this._getIdList(e);0!==i.length?this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then((function(){Object(r["b"])(i).then((function(){u["a"].deleteDataList(t.currentTableData,i,"coupon_id"),t.currentTableData.length<=0&&t.$emit("refresh",!0),t.$message.success("操作成功")}))})).catch((function(){})):this.$message.error("请选择要操作的数据")},handleStatus:function(e){var t=this,i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,n=arguments.length>2&&void 0!==arguments[2]&&arguments[2],o=this._getIdList(e);if(0!==o.length){if(!n){var l=this.currentTableData[e],s=l.status?0:1;if(l.status>1)return;if(0===s&&!this.auth.disable)return;if(1===s&&!this.auth.enable)return;return this.$set(this.currentTableData,e,Object(a["a"])(Object(a["a"])({},l),{},{status:2})),void u(o,s,this)}this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then((function(){u(o,i,t)})).catch((function(){}))}else this.$message.error("请选择要操作的数据");function u(e,t,i){Object(r["h"])(e,t).then((function(){i.currentTableData.forEach((function(n,o){-1!==e.indexOf(n.coupon_id)&&i.$set(i.currentTableData,o,Object(a["a"])(Object(a["a"])({},n),{},{status:t}))})),i.$message.success("操作成功")}))}},handleInvalid:function(e){var t=this,i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,n=arguments.length>2&&void 0!==arguments[2]&&arguments[2],o=this._getIdList(e);if(0!==o.length){if(!n){var l=this.currentTableData[e],s=l.is_invalid?0:1;if(l.is_invalid>1)return;if(0===s&&!this.auth.normal)return;if(1===s&&!this.auth.invalid)return;return this.$set(this.currentTableData,e,Object(a["a"])(Object(a["a"])({},l),{},{is_invalid:2})),void u(o,s,this)}this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then((function(){u(o,i,t)})).catch((function(){}))}else this.$message.error("请选择要操作的数据");function u(e,t,i){Object(r["f"])(e,t).then((function(){i.currentTableData.forEach((function(n,o){-1!==e.indexOf(n.coupon_id)&&i.$set(i.currentTableData,o,Object(a["a"])(Object(a["a"])({},n),{},{is_invalid:t}))})),i.$message.success("操作成功")}))}},handleCreate:function(){var e=this;this.form={name:"",description:"",guide:"",type:void 0,money:void 0,quota:void 0,category:[],exclude_category:[],level:[],frequency:0,give_num:void 0,give_begin_time:void 0,give_end_time:void 0,use_begin_time:void 0,use_end_time:void 0,status:1,is_invalid:0},this.$nextTick((function(){e.$refs.form&&e.$refs.form.clearValidate(),e.dialogStatus="create",e.dialogLoading=!1,e.dialogFormVisible=!0}))},create:function(){var e=this;this.$refs.form.validate((function(t){t&&(e.dialogLoading=!0,Object(r["a"])(e.form).then((function(t){e.currentTableData.unshift(Object(a["a"])(Object(a["a"])({},t.data),{},{receive_num:0,use_num:0})),e.dialogFormVisible=!1,e.$message.success("操作成功")})).catch((function(){e.dialogLoading=!1})))}))},handleUpdate:function(e){var t=this;this.currentIndex=e,this.form=Object(a["a"])(Object(a["a"])({},this.currentTableData[e]),{},{type:this.currentTableData[e].type.toString()}),this.$nextTick((function(){t.$refs.form&&t.$refs.form.clearValidate(),t.dialogStatus="update",t.dialogLoading=!1,t.dialogFormVisible=!0}))},update:function(){var e=this;this.$refs.form.validate((function(t){t&&(e.dialogLoading=!0,Object(r["g"])(e.form).then((function(t){e.$set(e.currentTableData,e.currentIndex,Object(a["a"])(Object(a["a"])({},e.currentTableData[e.currentIndex]),t.data)),e.dialogFormVisible=!1,e.$message.success("操作成功")})).catch((function(){e.dialogLoading=!1})))}))},handleCopyGiveCode:function(e){var t=this;s["a"](e).then((function(){t.$message.success("已复制到剪贴板")})).catch((function(e){t.$message.error(e)}))},handleCopyGuide:function(e){var t=this,i=this.currentTableData[e],n=i.guide?"/":"/v1/coupon_give.html",o=u["a"].getBaseApi(n,i.guide||this.$baseConfig.BASE_API);o+=i.guide?"give_code=":"method=give.coupon.code&give_code=",o+=i.give_code,s["a"](o).then((function(){t.$message.success("已复制到剪贴板")})).catch((function(e){t.$message.error(e)}))},handleLive:function(e){var t=this,i=this.currentTableData[e],n="请填写生成数量（最多还可生成 ".concat(i.give_num-i.receive_num," 张）");this.$prompt(n,"生成优惠劵",{confirmButtonText:"确定",cancelButtonText:"取消",inputPattern:/\S/,inputErrorMessage:"请填写生成数量"}).then((function(e){var n=e.value;Object(l["d"])(i.coupon_id,n).then((function(){t.$set(i,"receive_num",i.receive_num+Number(n)),t.$message.success("操作成功")}))})).catch((function(){}))},handleExport:function(e){var t=this.currentTableData[e];if(t.receive_num<=0)this.$message.error("没有可导出的数据，请先生成优惠劵");else{var i=[{label:"编号",prop:"coupon_give_id"},{label:"兑换码",prop:"exchange_code"},{label:"使用时间",prop:"use_time"},{label:"创建时间",prop:"create_time"},{label:"是否删除",prop:"is_delete"}],n={is_delete:{0:"否",1:"是"}};Object(l["b"])(t.coupon_id).then((function(e){f.a.excel({columns:i,data:u["a"].dataReplace(e.data,n),title:t.name})}))}},handleGiveUser:function(e,t){var i=this;this.currentIndex=t,this.dialogGiveType=e;var n=this.currentTableData[t];this.giveNumber=n.give_num-n.receive_num,this.giveForm={coupon_id:n.coupon_id,username:void 0,user_level_id:void 0},this.$nextTick((function(){i.dialogGiveLoading=!1,i.dialogGiveFormVisible=!0}))},handleUserConfirm:function(e){var t=[];e.forEach((function(e){t.push(e.username)})),this.giveForm.username=t},giveCoupon:function(){var e=this;this.dialogGiveLoading=!0;var t=this.currentTableData[this.currentIndex];Object(l["e"])(this.giveForm).then((function(i){e.$set(t,"receive_num",t.receive_num+i.data),e.dialogGiveFormVisible=!1,e.$message.success("操作成功")})).catch((function(){e.dialogGiveLoading=!1}))}}},g=v,h=(i("cb36"),i("2877")),b=Object(h["a"])(g,n,o,!1,null,"2e3e250b",null);t["default"]=b.exports},cb36:function(e,t,i){"use strict";i("b254")}}]);