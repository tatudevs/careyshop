(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-c4f942c2"],{"04cf":function(t,e,a){"use strict";a("e596")},1213:function(t,e,a){"use strict";a.d(e,"a",(function(){return o})),a.d(e,"f",(function(){return r})),a.d(e,"n",(function(){return n})),a.d(e,"g",(function(){return c})),a.d(e,"e",(function(){return u})),a.d(e,"h",(function(){return d})),a.d(e,"l",(function(){return p})),a.d(e,"m",(function(){return m})),a.d(e,"b",(function(){return f})),a.d(e,"k",(function(){return h})),a.d(e,"j",(function(){return g})),a.d(e,"i",(function(){return b})),a.d(e,"d",(function(){return v})),a.d(e,"c",(function(){return y}));var l=a("5530"),i=a("bc07"),s="/v1/storage";function o(t){return Object(i["a"])({url:s,method:"post",data:Object(l["a"])({method:"add.storage.directory.item"},t)})}function r(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"desc",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"storage_id";return Object(i["a"])({url:s,method:"post",data:{method:"get.storage.directory.select",order_type:t,order_field:e}})}function n(t,e){return Object(i["a"])({url:s,method:"post",data:{method:"set.storage.directory.default",storage_id:t,is_default:e}})}function c(t){return Object(i["a"])({url:s,method:"post",data:Object(l["a"])({method:"get.storage.list"},t)})}function u(t){return Object(i["a"])({url:s,method:"post",data:Object(l["a"])({method:"get.storage.collection"},t)})}function d(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:1;return Object(i["a"])({url:s,method:"post",data:{method:"get.storage.navi",storage_id:t,is_layer:e}})}function p(t,e){return Object(i["a"])({url:s,method:"post",data:{method:"rename.storage.item",storage_id:t,name:e}})}function m(t,e){return Object(i["a"])({url:s,method:"post",data:{method:"set.storage.cover",storage_id:t,parent_id:e}})}function f(t){return Object(i["a"])({url:s,method:"post",data:{method:"clear.storage.cover",storage_id:t}})}function h(t,e){return Object(i["a"])({url:s,method:"post",data:{method:"move.storage.list",storage_id:t,parent_id:e}})}function g(t){return Object(i["a"])({url:s,method:"post",data:Object(l["a"])({method:"get.storage.thumb.url"},t)})}function b(t,e){return Object(i["a"])({url:s,method:"post",data:{method:"get.storage.thumb.info",url:t,source:e}})}function v(t){return Object(i["a"])({url:s,method:"post",data:{method:"del.storage.list",storage_id:t}})}function y(t){return Object(i["a"])({url:s,method:"post",data:{method:"clear.storage.thumb",storage_id:t}})}},a320:function(t,e,a){"use strict";a.d(e,"a",(function(){return s})),a.d(e,"b",(function(){return o})),a.d(e,"c",(function(){return r}));var l=a("bc07"),i="/v1/upload";function s(){return Object(l["a"])({url:i,method:"post",data:{method:"get.upload.module"}})}function o(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:void 0,e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"web";return Object(l["a"])({url:i,method:"post",data:{method:"get.upload.token",module:t,type:e}})}function r(t){return Object(l["a"])({url:i,method:"post",data:{method:"replace.upload.item",storage_id:t}})}},d10f:function(t,e,a){"use strict";a.r(e);var l=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"cs-p"},[a("el-form",{attrs:{inline:!0,size:"small"}},[t.auth.add?a("el-form-item",[a("el-button",{attrs:{icon:"el-icon-plus",disabled:t.loading},on:{click:t.create}},[t._v("新增样式")])],1):t._e(),t.auth.enable||t.auth.disable?a("el-form-item",[a("el-button-group",[t.auth.enable?a("el-button",{attrs:{icon:"el-icon-check",disabled:t.loading},on:{click:function(e){return t.handleStatus(null,1,!0)}}},[t._v("启用")]):t._e(),t.auth.disable?a("el-button",{attrs:{icon:"el-icon-close",disabled:t.loading},on:{click:function(e){return t.handleStatus(null,0,!0)}}},[t._v("禁用")]):t._e()],1)],1):t._e(),t.auth.del?a("el-form-item",[a("el-button",{attrs:{icon:"el-icon-delete",disabled:t.loading},on:{click:function(e){return t.handleDelete(null)}}},[t._v("删除")])],1):t._e(),a("cs-help",{staticStyle:{"padding-bottom":"19px"},attrs:{router:t.$route.path}})],1),a("el-table",{attrs:{data:t.currentTableData,"highlight-current-row":!0},on:{"selection-change":t.handleSelectionChange,"sort-change":t.sortChange}},[a("el-table-column",{attrs:{align:"center",type:"selection",width:"55"}}),a("el-table-column",{attrs:{label:"名称",prop:"name",sortable:"custom","min-width":"140","show-overflow-tooltip":!0}}),a("el-table-column",{attrs:{label:"编码",prop:"code",sortable:"custom","min-width":"160","show-overflow-tooltip":!0}}),a("el-table-column",{attrs:{label:"平台",prop:"platform",sortable:"custom"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(" "+t._s(t.platformTable[e.row.platform])+" ")]}}])}),a("el-table-column",{attrs:{label:"输出格式",prop:"suffix"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(" "+t._s(e.row.suffix||"原图格式")+" ")]}}])}),a("el-table-column",{attrs:{label:"图片质量",prop:"quality"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(" "+t._s(e.row.quality||"系统默认")+" ")]}}])}),a("el-table-column",{attrs:{label:"缩放方式",prop:"resize","min-width":"120"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(" "+t._s(t.resizeMap[e.row.resize].text)+" ")]}}])}),a("el-table-column",{attrs:{label:"第三方样式",prop:"style","min-width":"90","show-overflow-tooltip":!0}}),a("el-table-column",{attrs:{label:"状态",prop:"status",sortable:"custom",align:"center",width:"100"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-tag",{style:t.auth.enable||t.auth.disable?"cursor: pointer;":"",attrs:{size:"mini",type:t.statusMap[e.row.status].type,effect:t.auth.enable||t.auth.disable?"light":"plain"},nativeOn:{click:function(a){return t.handleStatus(e.$index)}}},[t._v(" "+t._s(t.statusMap[e.row.status].text)+" ")])]}}])}),a("el-table-column",{attrs:{label:"操作",align:"center","min-width":"100"},scopedSlots:t._u([{key:"default",fn:function(e){return[t.auth.set?a("el-button",{attrs:{size:"small",type:"text"},on:{click:function(a){return t.updata(e.$index)}}},[t._v("编辑")]):t._e(),t.auth.del?a("el-button",{attrs:{size:"small",type:"text"},on:{click:function(a){return t.handleDelete(e.$index)}}},[t._v("删除")]):t._e()]}}])})],1),a("el-dialog",{attrs:{title:t.textMap[t.dialogStatus],visible:t.dialogFormVisible,"append-to-body":!0,"close-on-click-modal":!1,width:"760px"},on:{"update:visible":function(e){t.dialogFormVisible=e}}},[a("el-form",{ref:"form",staticStyle:{"margin-top":"-35px"},attrs:{model:t.form,rules:t.rules,"label-width":"85px"}},[a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{span:13}},[a("el-divider",[t._v("基础")]),a("el-form-item",{attrs:{label:"名称",prop:"name"}},[a("el-input",{attrs:{placeholder:"请输入样式名称",clearable:!0},model:{value:t.form.name,callback:function(e){t.$set(t.form,"name",e)},expression:"form.name"}})],1),a("el-form-item",{attrs:{label:"编码",prop:"code"}},[a("el-input",{attrs:{placeholder:"请输入样式编码",clearable:!0},model:{value:t.form.code,callback:function(e){t.$set(t.form,"code",e)},expression:"form.code"}})],1),a("el-form-item",{attrs:{label:"平台",prop:"platform"}},[a("el-select",{staticStyle:{width:"100%"},attrs:{placeholder:"请选择"},model:{value:t.form.platform,callback:function(e){t.$set(t.form,"platform",e)},expression:"form.platform"}},t._l(t.platformTable,(function(t,e){return a("el-option",{key:e,attrs:{label:t,value:e}})})),1)],1),a("el-form-item",{attrs:{label:"状态",prop:"status"}},[a("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:t.form.status,callback:function(e){t.$set(t.form,"status",e)},expression:"form.status"}})],1),a("el-divider",[t._v("图片")]),t.form.style?[a("el-alert",{attrs:{title:"启用第三方样式后本地样式将失效",type:"warning",closable:!1,center:""}})]:[a("el-form-item",{attrs:{label:"缩放方式",prop:"resize"}},[a("el-select",{staticStyle:{width:"100%"},attrs:{placeholder:"请选择"},model:{value:t.form.resize,callback:function(e){t.$set(t.form,"resize",e)},expression:"form.resize"}},t._l(t.resizeMap,(function(t,e){return a("el-option",{key:e,attrs:{label:t.text,value:t.type}})})),1)],1),""!==t.form.resize?a("el-form-item",{attrs:{label:"缩放规格",prop:"scale"}},[a("el-tabs",{model:{value:t.scaleTab,callback:function(e){t.scaleTab=e},expression:"scaleTab"}},[a("el-tab-pane",{attrs:{label:"Pc",name:"Pc"}},[a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:5}},[a("span",[t._v(" 缩略 "),a("el-tooltip",{attrs:{content:t.scaleHelp.help,placement:"top"}},[a("i",{staticClass:"el-icon-warning-outline"})])],1)]),a("el-col",{attrs:{span:19}},["proportion"===t.form.resize?a("div",[a("el-slider",{staticClass:"proportion",on:{change:function(e){t.scale.pc.slider=e}},model:{value:t.slider.pc,callback:function(e){t.$set(t.slider,"pc",e)},expression:"slider.pc"}})],1):a("div",[a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:12}},[a("span",[t._v("宽 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.pc.size.width,callback:function(e){t.$set(t.scale.pc.size,"width",e)},expression:"scale.pc.size.width"}})],1),a("el-col",{attrs:{span:12}},[a("span",[t._v("高 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.pc.size.height,callback:function(e){t.$set(t.scale.pc.size,"height",e)},expression:"scale.pc.size.height"}})],1)],1)],1)])],1),a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:5}},[a("span",[t._v(" 裁剪 "),a("el-tooltip",{attrs:{content:t.scaleHelp.help,placement:"top"}},[a("i",{staticClass:"el-icon-warning-outline"})])],1)]),a("el-col",{attrs:{span:19}},[a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:12}},[a("span",[t._v("宽 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.pc.crop.width,callback:function(e){t.$set(t.scale.pc.crop,"width",e)},expression:"scale.pc.crop.width"}})],1),a("el-col",{attrs:{span:12}},[a("span",[t._v("高 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.pc.crop.height,callback:function(e){t.$set(t.scale.pc.crop,"height",e)},expression:"scale.pc.crop.height"}})],1)],1)],1)],1),a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:5}},[a("span",[t._v(" 顺序 "),a("el-tooltip",{attrs:{content:t.scaleHelp.order,placement:"top"}},[a("i",{staticClass:"el-icon-warning-outline"})])],1)]),a("el-col",{attrs:{span:19}},[a("el-radio-group",{model:{value:t.scale.pc.order,callback:function(e){t.$set(t.scale.pc,"order",e)},expression:"scale.pc.order"}},[a("el-radio",{attrs:{label:!0}},[t._v("先缩后裁")]),a("el-radio",{attrs:{label:!1}},[t._v("先裁后缩")])],1)],1)],1)],1),a("el-tab-pane",{attrs:{label:"Mobile",name:"Mobile"}},[a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:5}},[a("span",[t._v(" 缩略 "),a("el-tooltip",{attrs:{content:t.scaleHelp.help,placement:"top"}},[a("i",{staticClass:"el-icon-warning-outline"})])],1)]),a("el-col",{attrs:{span:19}},["proportion"===t.form.resize?a("div",[a("el-slider",{staticClass:"proportion",on:{change:function(e){t.scale.mobile.slider=e}},model:{value:t.slider.mobile,callback:function(e){t.$set(t.slider,"mobile",e)},expression:"slider.mobile"}})],1):a("div",[a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:12}},[a("span",[t._v("宽 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.mobile.size.width,callback:function(e){t.$set(t.scale.mobile.size,"width",e)},expression:"scale.mobile.size.width"}})],1),a("el-col",{attrs:{span:12}},[a("span",[t._v("高 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.mobile.size.height,callback:function(e){t.$set(t.scale.mobile.size,"height",e)},expression:"scale.mobile.size.height"}})],1)],1)],1)])],1),a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:5}},[a("span",[t._v(" 裁剪 "),a("el-tooltip",{attrs:{content:t.scaleHelp.help,placement:"top"}},[a("i",{staticClass:"el-icon-warning-outline"})])],1)]),a("el-col",{attrs:{span:19}},[a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:12}},[a("span",[t._v("宽 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.mobile.crop.width,callback:function(e){t.$set(t.scale.mobile.crop,"width",e)},expression:"scale.mobile.crop.width"}})],1),a("el-col",{attrs:{span:12}},[a("span",[t._v("高 ")]),a("el-input-number",{staticClass:"size-input",attrs:{"controls-position":"right",min:0,size:"mini"},model:{value:t.scale.mobile.crop.height,callback:function(e){t.$set(t.scale.mobile.crop,"height",e)},expression:"scale.mobile.crop.height"}})],1)],1)],1)],1),a("el-row",{attrs:{gutter:5}},[a("el-col",{attrs:{span:5}},[a("span",[t._v(" 顺序 "),a("el-tooltip",{attrs:{content:t.scaleHelp.order,placement:"top"}},[a("i",{staticClass:"el-icon-warning-outline"})])],1)]),a("el-col",{attrs:{span:19}},[a("el-radio-group",{model:{value:t.scale.mobile.order,callback:function(e){t.$set(t.scale.mobile,"order",e)},expression:"scale.mobile.order"}},[a("el-radio",{attrs:{label:!0}},[t._v("先缩后裁")]),a("el-radio",{attrs:{label:!1}},[t._v("先裁后缩")])],1)],1)],1)],1)],1)],1):t._e(),a("el-form-item",{attrs:{label:"输出格式",prop:"suffix"}},[a("el-select",{staticStyle:{width:"100%"},attrs:{placeholder:"请选择"},model:{value:t.form.suffix,callback:function(e){t.$set(t.form,"suffix",e)},expression:"form.suffix"}},[a("el-option",{attrs:{label:"原图格式",value:""}}),t._l(t.suffixMap,(function(t,e){return a("el-option",{key:e,attrs:{label:t,value:t}})}))],2)],1),a("el-form-item",{attrs:{label:"图片质量",prop:"quality"}},[a("el-slider",{on:{change:function(e){t.form.quality=e}},model:{value:t.quality,callback:function(e){t.quality=e},expression:"quality"}})],1)],a("el-divider",[t._v("高级")]),a("el-form-item",{attrs:{label:"第三方样式",prop:"style"}},[a("el-input",{attrs:{placeholder:"可输入第三方样式",type:"textarea",rows:3},model:{value:t.form.style,callback:function(e){t.$set(t.form,"style",e)},expression:"form.style"}})],1)],2),a("el-col",{attrs:{span:11}},[a("el-divider",[t._v("效果预览")]),a("el-card",{attrs:{"body-style":{padding:"0px"},shadow:"never"}},[a("el-alert",{staticStyle:{"border-radius":"0"},attrs:{title:"原始图片",closable:!1,center:""}}),a("div",{staticClass:"image"},[t.imageUrl?a("el-image",{attrs:{src:t._f("getPreviewUrl")(t.imageUrl),fit:"fill"},nativeOn:{click:function(e){return t.$open(t.imageUrl)}}}):t._e()],1),a("div",{staticStyle:{padding:"10px"}},[a("div",{staticClass:"bottom clearfix"},[a("span",{staticClass:"image-info"},[t._v(t._s(t.imageInfo))])]),a("cs-upload",{ref:"upload",attrs:{type:"slot",accept:"image/*",limit:1,multiple:!1},on:{confirm:t._getUploadFileList}},[a("el-button",{staticClass:"button",attrs:{slot:"control",type:"text"},slot:"control"},[t._v("上传原图")])],1),a("el-popover",{attrs:{placement:"top",trigger:"click"}},[a("el-select",{attrs:{placeholder:"请选择"},on:{change:t.setModule},model:{value:t.uploadModule,callback:function(e){t.uploadModule=e},expression:"uploadModule"}},t._l(t.uploadTable,(function(t,e){return a("el-option",{key:e,attrs:{label:t.name,value:t.module}})})),1),a("el-button",{staticClass:"button",attrs:{slot:"reference",type:"text"},slot:"reference"},[t._v("更换模块")])],1)],1)],1),a("el-card",{directives:[{name:"loading",rawName:"v-loading",value:t.imageLoading,expression:"imageLoading"}],staticStyle:{"margin-top":"20px"},attrs:{"body-style":{padding:"0px"},shadow:"never"}},[a("el-alert",{staticStyle:{"border-radius":"0"},attrs:{title:"实际结果 "+(t.form.style||!t.form.resize?"":t.scaleTab),closable:!1,center:""}}),a("div",{staticClass:"image"},[t.imageResult["url_prefix"]?a("el-image",{attrs:{src:t.imageResult["url_prefix"],fit:"fill"},nativeOn:{click:function(e){return t.$open(t.imageResult["url_prefix"])}}}):t._e()],1),a("div",{staticStyle:{padding:"10px"}},[a("div",{staticClass:"bottom clearfix"},[a("span",{staticClass:"image-info"},[t._v(t._s(t.imageResultInfo))])]),a("el-button",{staticClass:"button",attrs:{type:"text"},on:{click:t.getThumbUrl}},[t._v("刷新效果")])],1)],1)],1)],1)],1),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{size:"small"},on:{click:function(e){t.dialogFormVisible=!1}}},[t._v("取消")]),"create"===t.dialogStatus?a("el-button",{attrs:{type:"primary",loading:t.dialogLoading,size:"small"},on:{click:function(e){return t.handleEdit(!0)}}},[t._v("确定")]):a("el-button",{attrs:{type:"primary",loading:t.dialogLoading,size:"small"},on:{click:function(e){return t.handleEdit(!1)}}},[t._v("修改")])],1)],1)],1)},i=[],s=(a("4160"),a("c975"),a("d3b7"),a("25f0"),a("159b"),a("5530")),o=a("b27e"),r=a("a320"),n=a("1213"),c=a("ca00"),u=a("60bb"),d={components:{csUpload:function(){return a.e("chunk-287c7adc").then(a.bind(null,"27d4"))}},props:{tableData:{default:function(){return[]}},platformTable:{default:function(){return[]}},loading:{default:!1}},computed:{changeData:function(){var t=this.scaleTab,e=this.imageUrl,a=this.scale,l=this.form,i=l.resize,s=l.suffix,o=l.quality,r=l.style;return{resize:i,suffix:s,quality:o,style:r,scaleTab:t,imageUrl:e,scale:a}}},watch:{tableData:{handler:function(t){this.currentTableData=t},immediate:!0},changeData:{handler:function(){var t=this;this.$nextTick((function(){t.getThumbUrl()}))},deep:!0}},filters:{getPreviewUrl:function(t){var e="&size[]=350";return c["a"].getImageStyleUrl(t,e)}},data:function(){return{currentTableData:[],multipleSelection:[],dialogLoading:!1,dialogFormVisible:!1,dialogStatus:"",scaleTab:"Pc",imageUrl:"",imageInfo:"",imageResult:{},imageResultInfo:"",imageLoading:!1,uploadModule:"",uploadTable:[],quality:100,slider:{pc:0,mobile:0},scale:{pc:{slider:0,size:{width:0,height:0},crop:{width:0,height:0},order:!0},mobile:{slider:0,size:{width:0,height:0},crop:{width:0,height:0},order:!0}},form:{},auth:{add:!1,set:!1,del:!1,enable:!1,disable:!1},rules:{name:[{required:!0,message:"名称不能为空",trigger:"blur"},{max:64,message:"长度不能大于 64 个字符",trigger:"blur"}],code:[{required:!0,message:"编码不能为空",trigger:"blur"},{max:32,message:"长度不能大于 32 个字符",trigger:"blur"}],platform:[{required:!0,message:"至少选择一项",trigger:"change"}],style:[{max:64,message:"长度不能大于 64 个字符",trigger:"blur"}]},textMap:{update:"编辑样式",create:"新增样式"},statusMap:{0:{text:"禁用",type:"danger"},1:{text:"启用",type:"success"},2:{text:"...",type:"info"}},resizeMap:{"":{text:"不使用缩放",type:""},scaling:{text:"指定宽高缩放",type:"scaling"},proportion:{text:"按百分比缩小",type:"proportion"},pad:{text:"固定宽高填充",type:"pad"}},suffixMap:["jpg","png","svg","gif","bmp","tiff","webp"],scaleHelp:{help:"宽或高的某一项值为 0 时，该项会进行自适应",order:"缩略与裁剪的先后顺序会影响最终的成图"}}},mounted:function(){this._validationAuth()},methods:{_validationAuth:function(){this.auth.add=this.$permission("/system/storage/style/add"),this.auth.set=this.$permission("/system/storage/style/set"),this.auth.del=this.$permission("/system/storage/style/del"),this.auth.enable=this.$permission("/system/storage/style/enable"),this.auth.disable=this.$permission("/system/storage/style/disable")},_getIdList:function(t){null===t&&(t=this.multipleSelection);var e=[];return Array.isArray(t)?t.forEach((function(t){e.push(t.storage_style_id)})):e.push(this.currentTableData[t].storage_style_id),e},_getUploadFileList:function(t){if(t.length){var e=t[0].response;if(e&&200===e.status){var a=e.data[0];0===a.type&&(this.imageInfo="大小: ".concat(c["a"].bytesFormatter(a.size)," "),this.imageInfo+="宽: ".concat(a.pixel.width," PX "),this.imageInfo+="高: ".concat(a.pixel.height," PX"),this.imageUrl=a.url)}}},handleSelectionChange:function(t){this.multipleSelection=t},sortChange:function(t){var e=t.column,a=t.prop,l=t.order,i={order_type:void 0,order_field:void 0};e&&l&&(i.order_type="ascending"===l?"asc":"desc",i.order_field=a),this.$emit("sort",i)},handleStatus:function(t){var e=this,a=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,l=arguments.length>2&&void 0!==arguments[2]&&arguments[2],i=this._getIdList(t);if(0!==i.length){if(!l){var r=this.currentTableData[t],n=r.status?0:1;if(r.status>1)return;if(0===n&&!this.auth.disable)return;if(1===n&&!this.auth.enable)return;return this.$set(this.currentTableData,t,Object(s["a"])(Object(s["a"])({},r),{},{status:2})),void c(i,n,this)}this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then((function(){c(i,a,e)})).catch((function(){}))}else this.$message.error("请选择要操作的数据");function c(t,e,a){Object(o["e"])(t,e).then((function(){a.currentTableData.forEach((function(l,i){-1!==t.indexOf(l.storage_style_id)&&a.$set(a.currentTableData,i,Object(s["a"])(Object(s["a"])({},l),{},{status:e}))})),a.$message.success("操作成功")}))}},handleDelete:function(t){var e=this,a=this._getIdList(t);0!==a.length?this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then((function(){Object(o["b"])(a).then((function(){c["a"].deleteDataList(e.currentTableData,a,"storage_style_id"),e.currentTableData.length<=0&&e.$emit("refresh",!0),e.$message.success("操作成功")}))})).catch((function(){})):this.$message.error("请选择要操作的数据")},create:function(){var t=this;this.form={name:"",code:"",platform:0,scale:{},resize:"",quality:100,suffix:"",style:"",status:"1"},this.scale={pc:{slider:0,size:{width:0,height:0},crop:{width:0,height:0},order:!0},mobile:{slider:0,size:{width:0,height:0},crop:{width:0,height:0},order:!0}},this.quality=100,this.slider={pc:0,mobile:0},this.scaleTab="Pc",this.imageUrl="",this.imageInfo="",this.imageResult={},this.imageResultInfo="",this.imageLoading=!1,this.uploadModule="",this.uploadTable.length||Object(r["a"])().then((function(e){t.uploadTable=e.data||[],t.uploadTable.unshift({name:"使用系统默认",module:""})})),this.$nextTick((function(){t.$refs.form&&t.$refs.form.clearValidate(),t.dialogStatus="create",t.dialogLoading=!1,t.dialogFormVisible=!0}))},handleEdit:function(t){var e=this;this.$refs.form.validate((function(a){a&&function(){var a={pc:{},mobile:{}},l=function(t){if(!Object.prototype.hasOwnProperty.call(e.scale,t))return"continue";var l=e.scale[t],i=l.order?["size","crop"]:["crop","size"];i.forEach((function(i){"proportion"===e.form.resize&&"size"===i?a[t].size=[l.slider]:a[t][i]=[l[i].width,l[i].height]})),a[t].slider=l.slider,a[t].order=l.order};for(var i in e.scale)l(i);e.dialogLoading=!0,t?Object(o["a"])(Object(s["a"])(Object(s["a"])({},e.form),{},{scale:a})).then((function(t){e.currentTableData.unshift(t.data),e.dialogFormVisible=!1,e.$message.success("操作成功")})).catch((function(){e.dialogLoading=!1})):Object(o["d"])(Object(s["a"])(Object(s["a"])({},e.form),{},{scale:a})).then((function(t){e.$set(e.currentTableData,e.currentIndex,t.data),e.dialogFormVisible=!1,e.$message.success("操作成功")})).catch((function(){e.dialogLoading=!1}))}()}))},getThumbUrl:Object(u["debounce"])((function(){var t=this;if(this.imageUrl){var e={url:this.imageUrl,quality:this.form.quality,suffix:this.form.suffix,style:this.form.style};if(this.form.resize&&!this.form.style){e.resize=this.form.resize;var a=this.scale[this.scaleTab.toLowerCase()],l=a.order?["size","crop"]:["crop","size"];l.forEach((function(l){"proportion"===t.form.resize&&"size"===l?e.size=[a.slider]:e[l]=[a[l].width,a[l].height]}))}this.imageLoading=!0,this.imageResult.url_prefix="",Object(n["j"])(e).then((function(e){t.imageResult=e.data||{}})).then((function(){Object(n["i"])(t.imageResult.url_prefix,t.imageResult.source).then((function(e){t.imageResultInfo="大小: ".concat(c["a"].bytesFormatter(e.data.size)," "),t.imageResultInfo+="宽: ".concat(e.data.width," PX "),t.imageResultInfo+="高: ".concat(e.data.height," PX")}))})).finally((function(){t.imageLoading=!1}))}}),300),updata:function(t){var e=this;this.currentIndex=t;var a=this.currentTableData[t],l={};for(var i in a.scale)if(Object.prototype.hasOwnProperty.call(a.scale,i))for(var o in l[i]={},a.scale[i])if(Object.prototype.hasOwnProperty.call(a.scale[i],o)){var n=a.scale[i][o];"size"===o||"crop"===o?(l[i][o]={width:n[0],height:n[1]},"proportion"===a.resize&&"size"===o&&(this.slider[i]=n[0],l[i][o]={width:0,height:0})):l[i][o]=n}this.form=Object(s["a"])(Object(s["a"])({},a),{},{status:a.status.toString(),scale:{}}),this.scale=Object(s["a"])({},l),this.scaleTab="Pc",this.imageUrl="",this.imageInfo="",this.imageResult={},this.imageResultInfo="",this.imageLoading=!1,this.uploadModule="",this.quality=a.quality,this.uploadTable.length||Object(r["a"])().then((function(t){e.uploadTable=t.data||[],e.uploadTable.unshift({name:"使用系统默认",module:""})})),Object.prototype.hasOwnProperty.call(this.platformTable,this.form.platform)||(this.form.platform=void 0),this.$nextTick((function(){e.$refs.form&&e.$refs.form.clearValidate(),e.dialogStatus="update",e.dialogLoading=!1,e.dialogFormVisible=!0}))},setModule:function(t){this.$refs.upload.setModuleName(t)}}},p=d,m=(a("04cf"),a("2877")),f=Object(m["a"])(p,l,i,!1,null,"2842f982",null);e["default"]=f.exports},e596:function(t,e,a){}}]);