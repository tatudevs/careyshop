(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-dee3a3b0"],{8111:function(e,t,a){"use strict";a.r(t);var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("el-drawer",{ref:"drawer",attrs:{visible:e.dialog,"with-header":!1,"destroy-on-close":!0,"custom-class":"setting-drawer",direction:"rtl",size:"550px","before-close":e.handleClose},on:{"update:visible":function(t){e.dialog=t},open:e.handleOpen}},[a("div",{staticClass:"setting-drawer__content"},[a("el-form",{attrs:{model:e.form,"label-position":"left","label-width":"105px"}},[a("el-form-item",{attrs:{label:"API_BASE"}},[a("el-input",{attrs:{placeholder:e.$t("apibase"),clearable:""},model:{value:e.form.apiBase,callback:function(t){e.$set(e.form,"apiBase",t)},expression:"form.apiBase"}})],1),a("el-form-item",{attrs:{label:"APP_KEY"}},[a("el-input",{attrs:{placeholder:e.$t("appkey"),clearable:""},model:{value:e.form.appKey,callback:function(t){e.$set(e.form,"appKey",t)},expression:"form.appKey"}})],1),a("el-form-item",{attrs:{label:"APP_SECRET"}},[a("el-input",{attrs:{placeholder:e.$t("appsecret"),clearable:""},model:{value:e.form.appSecret,callback:function(t){e.$set(e.form,"appSecret",t)},expression:"form.appSecret"}})],1),a("div",{staticClass:"variable"},[e._l(e.form.variable,(function(t,n){return a("el-form-item",{key:n,attrs:{label:e.$t("variable")+(n+1)}},[a("el-row",[a("el-col",{attrs:{span:8}},[a("el-input",{attrs:{placeholder:e.$t("key"),clearable:""},model:{value:t.name,callback:function(a){e.$set(t,"name",a)},expression:"value.name"}})],1),a("el-col",{staticStyle:{padding:"0 10px"},attrs:{span:14}},[a("el-input",{attrs:{placeholder:e.$t("value"),clearable:""},model:{value:t.value,callback:function(a){e.$set(t,"value",a)},expression:"value.value"}})],1),a("el-col",{attrs:{span:2}},[a("el-button",{attrs:{size:"small",type:"text"},on:{click:function(t){return t.preventDefault(),e.form.variable.splice(n,1)}}},[e._v("x")])],1)],1)],1)})),a("el-form-item",[a("el-button",{on:{click:e.addVariable}},[e._v(e._s(e.$t("add variable")))])],1)],2)],1),a("div",{staticClass:"setting-drawer__footer"},[a("el-button",{attrs:{type:"primary",loading:e.loading},on:{click:e.saveData}},[e._v(e._s(e.$t("save")))])],1)],1)])},i=[],l=(a("b64b"),a("96cf"),a("1da1")),o=a("5530"),r=a("2f62"),s=a("2ef0"),c=a("ca00"),p={name:"cs-setting",computed:Object(o["a"])({},Object(r["c"])("careyshop/setting",["setting"])),data:function(){return{dialog:!1,loading:!1,form:{apiBase:"",appKey:"",appSecret:"",variable:[]}}},mounted:function(){this.setting.apiBase&&this.setting.appKey&&this.setting.appSecret||this.$notify({title:this.$t("warning"),message:this.$t("not setting"),type:"warning",position:"bottom-right",duration:0})},methods:Object(o["a"])(Object(o["a"])({},Object(r["b"])("careyshop/setting",["set"])),{},{handleClose:function(e){this.loading||this.$confirm(this.$t("close setting"),this.$t("warning"),{type:"warning"}).then((function(){e()})).catch((function(){}))},handleOpen:function(){Object.keys(this.setting).length>0&&(this.form=Object(s["cloneDeep"])(this.setting))},saveData:function(){var e=this;return Object(l["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e.loading=!0,t.next=3,e.set(e.form);case 3:setTimeout((function(){c["a"].cookies.remove("mode"),c["a"].cookies.remove("name"),c["a"].cookies.remove("token"),e.dialog=!1,e.loading=!1,location.reload()}),1e3);case 4:case"end":return t.stop()}}),t)})))()},addVariable:function(){this.form.variable.push({name:"",value:""})}})},u=p,d=(a("979b"),a("2877")),f=Object(d["a"])(u,n,i,!1,null,"def45594",null);t["default"]=f.exports},8612:function(e,t,a){},"979b":function(e,t,a){"use strict";a("8612")}}]);