(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-7c130d36","chunk-b4638100"],{1213:function(t,e,n){"use strict";n.d(e,"a",(function(){return r})),n.d(e,"f",(function(){return c})),n.d(e,"n",(function(){return s})),n.d(e,"g",(function(){return l})),n.d(e,"e",(function(){return u})),n.d(e,"h",(function(){return d})),n.d(e,"l",(function(){return h})),n.d(e,"m",(function(){return g})),n.d(e,"b",(function(){return m})),n.d(e,"k",(function(){return f})),n.d(e,"j",(function(){return p})),n.d(e,"i",(function(){return b})),n.d(e,"d",(function(){return _})),n.d(e,"c",(function(){return v}));var i=n("5530"),a=n("bc07"),o="/v1/storage";function r(t){return Object(a["a"])({url:o,method:"post",data:Object(i["a"])({method:"add.storage.directory.item"},t)})}function c(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"desc",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"storage_id";return Object(a["a"])({url:o,method:"post",data:{method:"get.storage.directory.select",order_type:t,order_field:e}})}function s(t,e){return Object(a["a"])({url:o,method:"post",data:{method:"set.storage.directory.default",storage_id:t,is_default:e}})}function l(t){return Object(a["a"])({url:o,method:"post",data:Object(i["a"])({method:"get.storage.list"},t)})}function u(t){return Object(a["a"])({url:o,method:"post",data:Object(i["a"])({method:"get.storage.collection"},t)})}function d(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:1;return Object(a["a"])({url:o,method:"post",data:{method:"get.storage.navi",storage_id:t,is_layer:e}})}function h(t,e){return Object(a["a"])({url:o,method:"post",data:{method:"rename.storage.item",storage_id:t,name:e}})}function g(t,e){return Object(a["a"])({url:o,method:"post",data:{method:"set.storage.cover",storage_id:t,parent_id:e}})}function m(t){return Object(a["a"])({url:o,method:"post",data:{method:"clear.storage.cover",storage_id:t}})}function f(t,e){return Object(a["a"])({url:o,method:"post",data:{method:"move.storage.list",storage_id:t,parent_id:e}})}function p(t){return Object(a["a"])({url:o,method:"post",data:Object(i["a"])({method:"get.storage.thumb.url"},t)})}function b(t,e){return Object(a["a"])({url:o,method:"post",data:{method:"get.storage.thumb.info",url:t,source:e}})}function _(t){return Object(a["a"])({url:o,method:"post",data:{method:"del.storage.list",storage_id:t}})}function v(t){return Object(a["a"])({url:o,method:"post",data:{method:"clear.storage.thumb",storage_id:t}})}},"128d":function(t,e,n){"use strict";var i=n("b85c"),a=n("ca00"),o=n("60bb");e["a"]={data:function(){return{isCheckDirectory:!0}},filters:{getImageThumb:function(t){var e="/static/admin/",n=e+"image/storage/file.png";switch(t.type){case 0:n=t.url?a["a"].getImageCodeUrl(t.url,"storage_lists"):"";break;case 2:n=t.cover?a["a"].getImageCodeUrl(t.cover,"storage_lists"):e+(t.is_default?"image/storage/default.png":"image/storage/folder.png");break;case 3:n=t.cover?a["a"].getImageCodeUrl(t.cover,"storage_lists"):e+"image/storage/video.png";break}return n},getFileTypeIocn:function(t){switch(t){case 0:return"el-icon-picture-outline";case 1:return"el-icon-document";case 2:return"el-icon-folder";case 3:return"el-icon-video-camera"}return"el-icon-warning-outline"}},methods:{_getStorageIdList:function(){var t,e=[],n=Object(i["a"])(this.currentTableData);try{for(n.s();!(t=n.n()).done;){var a=t.value;(this.isCheckDirectory||2!==a.type)&&e.push(a.storage_id)}}catch(o){n.e(o)}finally{n.f()}return e},allCheckBox:function(){this.checkList=Object(o["union"])(this.checkList,this._getStorageIdList())},reverseCheckBox:function(){this.checkList=Object(o["xor"])(this.checkList,this._getStorageIdList())},cancelCheckBox:function(){this.checkList=Object(o["difference"])(this.checkList,this._getStorageIdList())}}}},"1ccc":function(t,e,n){"use strict";n("29ab")},"29ab":function(t,e,n){},"85ce":function(t,e,n){"use strict";n.r(e);var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("el-dialog",{attrs:{title:"资源选取",visible:t.visible,"append-to-body":!0,"close-on-click-modal":!1,width:"769px"},on:{"update:visible":function(e){t.visible=e}}},[n("el-form",{staticStyle:{"margin-top":"-25px"},attrs:{model:t.form,size:"small"},nativeOn:{submit:function(t){t.preventDefault()}}},[n("el-row",{attrs:{gutter:20}},[n("el-col",{attrs:{span:10}},[n("el-form-item",[n("el-button-group",[n("el-tooltip",{attrs:{content:"勾选当前页全部资源",placement:"top"}},[n("el-button",{attrs:{icon:"el-icon-plus"},on:{click:t.allCheckBox}},[t._v("全选")])],1),n("el-tooltip",{attrs:{content:"反向勾选当前页资源",placement:"top"}},[n("el-button",{attrs:{icon:"el-icon-minus"},on:{click:t.reverseCheckBox}},[t._v("反选")])],1),n("el-tooltip",{attrs:{content:"取消当前页勾选",placement:"top"}},[n("el-button",{attrs:{icon:"el-icon-close"},on:{click:t.cancelCheckBox}},[t._v("取消")])],1),n("el-tooltip",{attrs:{content:"清除所有已选中勾选",placement:"top"}},[n("el-button",{attrs:{icon:"el-icon-refresh"},on:{click:function(e){t.checkList=[]}}},[t._v("清除")])],1)],1)],1)],1),n("el-col",{attrs:{span:14}},[n("el-form-item",{attrs:{prop:"name"}},[n("el-input",{attrs:{placeholder:"输入资源名称进行搜索",clearable:!0,size:"small"},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.handleSearch()}},model:{value:t.form.name,callback:function(e){t.$set(t.form,"name",e)},expression:"form.name"}},[n("el-button",{attrs:{slot:"append",icon:"el-icon-search"},on:{click:t.handleSearch},slot:"append"})],1)],1)],1)],1)],1),n("el-breadcrumb",{staticClass:"breadcrumb cs-mb",attrs:{"separator-class":"el-icon-arrow-right"}},[n("el-breadcrumb-item",[n("a",{staticClass:"cs-cp",on:{click:function(e){return t.switchDirectory(0)}}},[t._v("资源管理")])]),t._l(t.naviData,(function(e){return n("el-breadcrumb-item",{key:e.storage_id},[n("a",{staticClass:"cs-cp",on:{click:function(n){return t.switchDirectory(e.storage_id)}}},[t._v(t._s(e.name))])])}))],2),n("el-checkbox-group",{model:{value:t.checkList,callback:function(e){t.checkList=e},expression:"checkList"}},[n("ul",{staticClass:"storage-list"},t._l(t.currentTableData,(function(e,i){return n("li",{key:i},[n("dl",[n("dt",[n("div",{staticClass:"picture cs-m-5"},[2!==e.type?n("el-checkbox",{staticClass:"check",attrs:{label:e.storage_id}},[t._v(" "+t._s(t.checkIndex[e.storage_id])+" ")]):t._e(),n("el-image",{attrs:{fit:"fill",src:t._f("getImageThumb")(e),lazy:""},nativeOn:{click:function(e){return t.handleOpen(i)}}})],1),n("el-tooltip",{attrs:{placement:"top",enterable:!1,"open-delay":300}},[n("div",{attrs:{slot:"content"},slot:"content"},[n("span",[t._v("名称："+t._s(e.name))]),n("br"),n("span",[t._v("日期："+t._s(e.create_time))]),n("br"),0===e.type?n("span",[t._v("尺寸："+t._s(e.pixel["width"]+","+e.pixel["height"]))]):n("span",[t._v("类型："),n("i",{class:t._f("getFileTypeIocn")(e.type)})])]),n("span",{staticClass:"storage-name cs-ml-5"},[t._v(t._s(e.name))])])],1)])])})),0)]),n("page-footer",{staticStyle:{margin:"0",padding:"20px 0 0 0"},attrs:{current:t.page.current,size:t.page.size,total:t.page.total,"is-size":!1},on:{change:t.handlePaginationChange}}),n("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("div",{staticStyle:{float:"left","font-size":"13px"}},[t.checkList.length>t.limit&&0!==t.limit?n("span",{staticStyle:{color:"#F56C6C"}},[t._v(" 当前已选 "+t._s(t.checkList.length)+" 个，最多允许选择 "+t._s(t.limit)+" 个资源 ")]):n("span",[t._v("当前已选 "+t._s(t.checkList.length)+" 个资源")])]),n("el-button",{attrs:{size:"small"},on:{click:function(e){t.visible=!1}}},[t._v("取消")]),n("el-button",{attrs:{type:"primary",loading:t.loadingCollection,disabled:t.checkList.length>t.limit&&0!==t.limit,size:"small"},on:{click:t.handleConfirm}},[t._v("确定")])],1)],1)},a=[],o=(n("4160"),n("b0c0"),n("a9e3"),n("d3b7"),n("159b"),n("5530")),r=n("128d"),c=n("1213"),s={name:"cs-storage",mixins:[r["a"]],components:{PageFooter:function(){return n.e("chunk-2d0bd262").then(n.bind(null,"2b84"))}},props:{confirm:{type:Function},limit:{type:Number,required:!1,default:0}},data:function(){return{visible:!1,loadingCollection:!1,naviData:[],checkList:[],checkIndex:{},currentTableData:[],isCheckDirectory:!1,source:"",storageType:[],form:{name:"",storage_id:0,order_type:"desc",order_field:"storage_id"},page:{current:1,size:48,total:0}}},watch:{"form.storage_id":{handler:function(t){var e=this;Object(c["h"])(t).then((function(t){e.naviData=t.data||[]}))}},checkList:{handler:function(t){var e={};t.forEach((function(t,n){e[t]=n+1})),this.checkIndex=Object(o["a"])({},e)}}},methods:{handleStorageDlg:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"";this.visible=!0,this.storageType=t,this.source=e,this.checkList=[],this.loadingCollection=!1,this.handleSubmit()},switchDirectory:function(t){this.form.name=null,this.form.storage_id=t||0,this.handleSubmit()},handleOpen:function(t){2===this.currentTableData[t].type&&this.switchDirectory(this.currentTableData[t].storage_id)},handlePaginationChange:function(t){var e=this;this.page=t,this.$nextTick((function(){e.handleSubmit()}))},handleSubmit:function(){var t=this;Object(c["g"])(Object(o["a"])(Object(o["a"])({},this.form),{},{type:this.storageType,page_no:this.page.current,page_size:this.page.size})).then((function(e){t.currentTableData=e.data.items||[],t.page.total=e.data.total_result}))},handleConfirm:function(){var t=this;if(this.checkList.length<=0)return this.$emit("confirm",[],this.source),void(this.visible=!1);this.loadingCollection=!0,Object(c["e"])({storage_id:this.checkList,order_type:this.form.order_type,order_field:this.form.order_field}).then((function(e){t.checkList=[],t.visible=!1,t.$emit("confirm",e.data||[],t.source)})).finally((function(){t.loadingCollection=!1}))},handleSearch:function(){this.page.current=1,this.form.storage_id=0,this.handleSubmit()}}},l=s,u=(n("1ccc"),n("2877")),d=Object(u["a"])(l,i,a,!1,null,"ad70480e",null);e["default"]=d.exports}}]);