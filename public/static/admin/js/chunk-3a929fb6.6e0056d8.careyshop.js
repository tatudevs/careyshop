(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-3a929fb6"],{"0b45":function(e,t,a){"use strict";a.r(t);var o=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"cs-p"},[a("el-form",{attrs:{inline:!0,size:"small"}},[e.auth.add?a("el-form-item",[a("el-button",{attrs:{icon:"el-icon-plus",disabled:e.loading},on:{click:e.handleCreate}},[e._v("新增等级")])],1):e._e(),a("el-form-item",[a("el-button-group",[e.auth.del?a("el-button",{attrs:{icon:"el-icon-delete",disabled:e.loading},on:{click:function(t){return e.handleDelete(null)}}},[e._v("删除")]):e._e(),a("el-button",{attrs:{icon:"el-icon-refresh",disabled:e.loading},on:{click:function(t){return e.$emit("refresh")}}},[e._v("刷新")])],1)],1),a("cs-help",{staticStyle:{"padding-bottom":"19px"},attrs:{router:e.$route.path}})],1),a("el-table",{attrs:{data:e.currentTableData,"highlight-current-row":!0},on:{"selection-change":e.handleSelectionChange}},[a("el-table-column",{attrs:{align:"center",type:"selection",width:"55"}}),a("el-table-column",{attrs:{label:"名称",prop:"name"},scopedSlots:e._u([{key:"default",fn:function(t){return[a("span",[e._v(e._s(t.row.name))]),t.row.icon?a("el-image",{staticClass:"level-icon",attrs:{src:t.row.icon,fit:"fill"}},[a("div",{staticClass:"image-slot",attrs:{slot:"error"},slot:"error"},[a("i",{staticClass:"el-icon-picture-outline"})])]):e._e()]}}])}),a("el-table-column",{attrs:{label:"消费金额",prop:"amount"}}),a("el-table-column",{attrs:{label:"折扣(%)",prop:"discount"}}),a("el-table-column",{attrs:{label:"描述",prop:"description","min-width":"220"}}),a("el-table-column",{attrs:{label:"操作",align:"center","min-width":"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[e.auth.set?a("el-button",{attrs:{size:"small",type:"text"},on:{click:function(a){return e.handleUpdate(t.$index)}}},[e._v("编辑")]):e._e(),e.auth.del?a("el-button",{attrs:{size:"small",type:"text"},on:{click:function(a){return e.handleDelete(t.$index)}}},[e._v("删除")]):e._e()]}}])})],1),a("el-dialog",{attrs:{title:e.textMap[e.dialogStatus],visible:e.dialogFormVisible,"append-to-body":!0,"close-on-click-modal":!1,width:"600px"},on:{"update:visible":function(t){e.dialogFormVisible=t}}},[a("el-form",{ref:"form",attrs:{model:e.form,rules:e.rules,"label-width":"80px"}},[a("el-form-item",{attrs:{label:"名称",prop:"name"}},[a("el-input",{attrs:{placeholder:"请输入等级名称",clearable:!0},model:{value:e.form.name,callback:function(t){e.$set(e.form,"name",t)},expression:"form.name"}})],1),a("el-form-item",{attrs:{label:"等级图标",prop:"icon"}},[a("el-input",{attrs:{placeholder:"可输入等级图标",clearable:!0},model:{value:e.form.icon,callback:function(t){e.$set(e.form,"icon",t)},expression:"form.icon"}},[a("el-dropdown",{attrs:{slot:"append","show-timeout":50},on:{command:e.handleCommand},slot:"append"},[a("el-button",{attrs:{icon:"el-icon-upload"}}),a("el-dropdown-menu",{attrs:{slot:"dropdown"},slot:"dropdown"},[a("el-dropdown-item",{attrs:{command:"storage",icon:"el-icon-finished"}},[e._v("资源选择")]),a("el-dropdown-item",{attrs:{command:"upload",icon:"el-icon-upload2"}},[e._v("上传资源")])],1)],1)],1)],1),a("el-row",[a("el-col",{attrs:{span:12}},[a("el-form-item",{attrs:{label:"消费金额",prop:"amount"}},[a("el-input-number",{attrs:{"controls-position":"right",precision:2,min:0},model:{value:e.form.amount,callback:function(t){e.$set(e.form,"amount",t)},expression:"form.amount"}})],1)],1),a("el-col",{attrs:{span:12}},[a("el-form-item",{attrs:{label:"折扣(%)",prop:"discount"}},[a("el-input-number",{attrs:{"controls-position":"right",min:0,max:100},model:{value:e.form.discount,callback:function(t){e.$set(e.form,"discount",t)},expression:"form.discount"}})],1)],1)],1),a("el-form-item",{attrs:{label:"描述",prop:"description"}},[a("el-input",{attrs:{placeholder:"可输入等级描述",type:"textarea",rows:3},model:{value:e.form.description,callback:function(t){e.$set(e.form,"description",t)},expression:"form.description"}})],1)],1),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{size:"small"},on:{click:function(t){e.dialogFormVisible=!1}}},[e._v("取消")]),"create"===e.dialogStatus?a("el-button",{attrs:{type:"primary",loading:e.dialogLoading,size:"small"},on:{click:e.create}},[e._v("确定")]):a("el-button",{attrs:{type:"primary",loading:e.dialogLoading,size:"small"},on:{click:e.update}},[e._v("修改")])],1),a("cs-storage",{ref:"storage",staticStyle:{display:"none"},attrs:{limit:1},on:{confirm:e._getStorageFileList}}),a("cs-upload",{ref:"upload",staticStyle:{display:"none"},attrs:{type:"slot",accept:"image/*",limit:1,multiple:!1},on:{confirm:e._getUploadFileList}})],1)],1)},i=[],n=(a("4160"),a("d3b7"),a("159b"),a("5530")),l=a("b85c"),r=a("2f46"),s=a("ca00"),c={components:{csUpload:function(){return a.e("chunk-6b65b9bf").then(a.bind(null,"27d4"))},csStorage:function(){return a.e("chunk-7c130d36").then(a.bind(null,"85ce"))}},props:{loading:{default:!1},tableData:{default:function(){return[]}}},data:function(){return{currentTableData:[],multipleSelection:[],auth:{add:!1,set:!1,del:!1},dialogLoading:!1,dialogFormVisible:!1,dialogStatus:"",textMap:{update:"编辑等级",create:"新增等级"},form:{name:void 0,icon:void 0,amount:void 0,discount:void 0,description:void 0},rules:{name:[{required:!0,message:"名称不能为空",trigger:"blur"},{max:30,message:"长度不能大于 30 个字符",trigger:"blur"}],icon:[{max:512,message:"长度不能大于 200 个字符",trigger:"blur"}],amount:[{required:!0,message:"消费金额不能为空",trigger:"blur"}],discount:[{required:!0,message:"折扣不能为空",trigger:"blur"}],description:[{max:200,message:"长度不能大于 200 个字符",trigger:"blur"}]}}},watch:{tableData:{handler:function(e){this.currentTableData=e},immediate:!0}},mounted:function(){this._validationAuth()},methods:{_validationAuth:function(){this.auth.add=this.$permission("/member/user/level/add"),this.auth.set=this.$permission("/member/user/level/set"),this.auth.del=this.$permission("/member/user/level/del")},_getIdList:function(e){null===e&&(e=this.multipleSelection);var t=[];return Array.isArray(e)?e.forEach((function(e){t.push(e.user_level_id)})):t.push(this.currentTableData[e].user_level_id),t},handleCommand:function(e){switch(e){case"storage":this.$refs.storage.handleStorageDlg([0,2]);break;case"upload":this.$refs.upload.handleUploadDlg();break}},_getUploadFileList:function(e){if(e.length){var t=e[0].response;t&&200===t.status&&0===t.data[0].type&&(this.form.icon=s["a"].checkUrl(t.data[0].url))}},_getStorageFileList:function(e){if(e.length){var t,a=Object(l["a"])(e);try{for(a.s();!(t=a.n()).done;){var o=t.value;if(0===o.type){this.form.icon=s["a"].checkUrl(o.url);break}}}catch(i){a.e(i)}finally{a.f()}}},handleSelectionChange:function(e){this.multipleSelection=e},handleDelete:function(e){var t=this,a=this._getIdList(e);0!==a.length?this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then((function(){Object(r["b"])(a).then((function(){s["a"].deleteDataList(t.currentTableData,a,"user_level_id"),t.$message.success("操作成功")}))})).catch((function(){})):this.$message.error("请选择要操作的数据")},handleCreate:function(){var e=this;this.form={name:"",amount:0,icon:"",discount:0,description:""},this.$nextTick((function(){e.$refs.form&&e.$refs.form.clearValidate(),e.dialogStatus="create",e.dialogLoading=!1,e.dialogFormVisible=!0}))},create:function(){var e=this;this.$refs.form.validate((function(t){t&&(e.dialogLoading=!0,Object(r["a"])(e.form).then((function(t){e.currentTableData.push(t.data),e.dialogFormVisible=!1,e.$message.success("操作成功")})).catch((function(){e.dialogLoading=!1})))}))},handleUpdate:function(e){var t=this;this.currentIndex=e,this.form=Object(n["a"])({},this.currentTableData[e]),this.$nextTick((function(){t.$refs.form&&t.$refs.form.clearValidate(),t.dialogStatus="update",t.dialogLoading=!1,t.dialogFormVisible=!0}))},update:function(){var e=this;this.$refs.form.validate((function(t){t&&(e.dialogLoading=!0,Object(r["d"])(e.form).then((function(t){e.$set(e.currentTableData,e.currentIndex,Object(n["a"])(Object(n["a"])({},e.currentTableData[e.currentIndex]),t.data)),e.dialogFormVisible=!1,e.$message.success("操作成功")})).catch((function(){e.dialogLoading=!1})))}))}}},u=c,d=(a("e543"),a("2877")),m=Object(d["a"])(u,o,i,!1,null,"a4b5ecba",null);t["default"]=m.exports},"31ee":function(e,t,a){},e543:function(e,t,a){"use strict";a("31ee")}}]);