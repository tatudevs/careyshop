(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-3c5c5fda"],{"6e5a":function(e,t,a){},a48e:function(e,t,a){"use strict";a("6e5a")},d262:function(e,t,a){"use strict";a.r(t);var l=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("div",{staticClass:"cs-mb",staticStyle:{display:"flex"}},[a("label",{staticClass:"el-form-item__label",staticStyle:{width:"90px"}},[e._v(e._s(e.$t("headers")))]),a("el-select",{staticStyle:{flex:"auto"},attrs:{"value-key":"key"},model:{value:e.select,callback:function(t){e.select=t},expression:"select"}},e._l(e.options,(function(t){return a("el-option-group",{key:t.label,attrs:{label:t.label}},e._l(t.options,(function(e,l){return a("el-option",{key:l,attrs:{label:e.name+": "+e.value,value:Object.assign({},e,{type:t.type,index:l})}})})),1)})),1),a("el-dropdown",{staticStyle:{margin:"0 10px"},attrs:{type:"primary","split-button":""},on:{click:e.addSelectedHeader,command:e.selectedCommand}},[e._v(" "+e._s(e.$t("add selected header"))+" "),a("el-dropdown-menu",{attrs:{slot:"dropdown"},slot:"dropdown"},[a("el-dropdown-item",{attrs:{command:"set",disabled:e.isDropdown}},[e._v(e._s(e.$t("edit saved header")))]),a("el-dropdown-item",{attrs:{command:"del",disabled:e.isDropdown}},[e._v(e._s(e.$t("delete saved header")))])],1)],1),a("el-button",{attrs:{type:"info"},on:{click:e.addDialogHeader}},[e._v(e._s(e.$t("add header")))])],1),a("el-table",{attrs:{data:e.tableData,border:""}},[a("el-table-column",{attrs:{prop:"name",label:e.$t("header name")}}),a("el-table-column",{attrs:{prop:"value",label:e.$t("header value")}}),a("el-table-column",{attrs:{label:e.$t("actions"),align:"center",width:"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[a("div",{staticClass:"table-button"},[a("el-button",{attrs:{icon:"el-icon-delete",size:"mini",type:"text"},on:{click:function(a){return e.delTableRow(t.$index)}}}),a("el-button",{attrs:{icon:"el-icon-edit-outline",size:"mini",type:"text"},on:{click:function(a){return e.setTableRow(t.$index)}}})],1)]}}])})],1),a("el-dialog",{attrs:{title:e.$t("headers"),visible:e.visible,"close-on-click-modal":!1,width:"600px"},on:{"update:visible":function(t){e.visible=t}}},[a("el-form",{attrs:{model:e.visibleForm,"label-width":"100px"}},[a("el-form-item",{attrs:{label:e.$t("header name")}},[a("el-autocomplete",{staticStyle:{width:"100%"},attrs:{"suffix-icon":"el-icon-arrow-down",placeholder:"Content-Type","fetch-suggestions":e.querySearch,clearable:""},model:{value:e.visibleForm.name,callback:function(t){e.$set(e.visibleForm,"name",t)},expression:"visibleForm.name"}})],1),a("el-form-item",{attrs:{label:e.$t("header value")}},[a("el-input",{attrs:{placeholder:"application/json;charset=utf-8",clearable:""},model:{value:e.visibleForm.value,callback:function(t){e.$set(e.visibleForm,"value",t)},expression:"visibleForm.value"}})],1),a("el-form-item",{directives:[{name:"show",rawName:"v-show",value:e.visibleForm.show,expression:"visibleForm.show"}],attrs:{label:e.$t("header save")}},[a("el-switch",{model:{value:e.visibleForm.save,callback:function(t){e.$set(e.visibleForm,"save",t)},expression:"visibleForm.save"}})],1)],1),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{size:"medium"},on:{click:function(t){e.visible=!1}}},[e._v(e._s(e.$t("cancel")))]),a("el-button",{attrs:{disabled:!e.visibleForm.name||!e.visibleForm.value,type:"primary",size:"medium"},on:{click:e.saveHeaders}},[e._v(e._s(e.$t("save")))])],1)],1)],1)},i=[],s=a("5530"),o=(a("b64b"),a("4de4"),a("d3b7"),a("a434"),a("b0c0"),a("2f62")),n={name:"cs-headers",computed:Object(s["a"])(Object(s["a"])({},Object(o["c"])("careyshop/headers",["headers","examples"])),{},{options:function(){var e=[{label:this.$t("examples"),type:"examples",options:this.examples}];return this.headers.length>0&&e.unshift({label:this.$t("custom"),type:"custom",options:this.headers}),e},isDropdown:function(){return!Object.keys(this.select).length||"examples"===this.select.type}}),props:{value:{type:Array,required:!0,default:function(){return[]}}},data:function(){return{visible:!1,visibleForm:{},restaurants:[{value:"Cookie"},{value:"User-Agent"},{value:"Content-Type"},{value:"Host"},{value:"Authorization"},{value:"Referer"},{value:"Accept"},{value:"Accept-Charset"},{value:"Accept-Encoding"},{value:"Accept-Language"},{value:"Accept-Ranges"},{value:"Cache-Control"},{value:"Connection"},{value:"Date"},{value:"Expect"},{value:"From"},{value:"If-Match"},{value:"If-Modified-Since"},{value:"If-None-Match"},{value:"If-Range"},{value:"If-Unmodified-Since"},{value:"Max-Forwards"},{value:"Pragma"},{value:"Proxy-Authorization"},{value:"Range"},{value:"TE"},{value:"Upgrade"},{value:"Via"},{value:"Warning"}],select:{},tableData:[]}},watch:{value:{handler:function(e){this.tableData=e},immediate:!0}},methods:Object(s["a"])(Object(s["a"])({},Object(o["b"])("careyshop/headers",["addHeader","delHeader","setHeader"])),{},{querySearch:function(e,t){var a=this.restaurants,l=e?a.filter(this.createFilter(e)):a;t(l)},createFilter:function(e){return function(t){return 0===t.value.toLowerCase().indexOf(e.toLowerCase())}},addSelectedHeader:function(){Object.keys(this.select).length>0&&(this.tableData.unshift(this.select),this.$emit("input",this.tableData))},delTableRow:function(e){this.tableData.splice(e,1),this.$emit("input",this.tableData)},setTableRow:function(e){this.visibleForm=Object(s["a"])(Object(s["a"])({},this.tableData[e]),{},{index:e,type:"table",show:!0}),this.visible=!0},addDialogHeader:function(){var e=this;this.$nextTick((function(){e.visibleForm={type:"add",show:!0},e.visible=!0}))},selectedCommand:function(e){switch(e){case"del":this.delHeader(this.select.index),this.select={};break;case"set":this.visibleForm=Object(s["a"])(Object(s["a"])({},this.select),{},{type:"header"}),this.visible=!0;break}},saveHeaders:function(){var e=this.visibleForm,t=e.type,a=e.name,l=e.value,i=e.key,o=e.index;"add"===t&&(this.tableData.unshift({name:a,value:l}),this.$emit("input",this.tableData)),"header"===t&&(this.select=this.visibleForm,this.setHeader({key:o,value:{name:a,value:l,key:i}})),"table"===t&&(this.$set(this.tableData,o,Object(s["a"])(Object(s["a"])({},this.tableData[o]),{},{value:l,name:a})),this.$emit("input",this.tableData)),this.visibleForm.save&&this.addHeader({name:a,value:l}),this.visible=!1}})},r=n,c=(a("a48e"),a("2877")),d=Object(c["a"])(r,l,i,!1,null,"5ed82d52",null);t["default"]=d.exports}}]);