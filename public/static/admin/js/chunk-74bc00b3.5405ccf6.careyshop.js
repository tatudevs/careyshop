(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-74bc00b3"],{"0b45":function(t,e,a){"use strict";a.r(e);var o=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"cs-p"},[a("el-form",{attrs:{inline:!0,size:"small"}},[t.auth.add?a("el-form-item",[a("el-button",{attrs:{icon:"el-icon-plus",disabled:t.loading},on:{click:t.handleCreate}},[t._v("新增等级")])],1):t._e(),a("el-form-item",[a("el-button-group",[t.auth.del?a("el-button",{attrs:{icon:"el-icon-delete",disabled:t.loading},on:{click:function(e){return t.handleDelete(null)}}},[t._v("删除")]):t._e(),a("el-button",{attrs:{icon:"el-icon-refresh",disabled:t.loading},on:{click:function(e){return t.$emit("refresh")}}},[t._v("刷新")])],1)],1),a("cs-help",{staticStyle:{"padding-bottom":"19px"},attrs:{router:t.$route.path}})],1),a("el-table",{attrs:{data:t.currentTableData,"highlight-current-row":!0},on:{"selection-change":t.handleSelectionChange}},[a("el-table-column",{attrs:{align:"center",type:"selection",width:"55"}}),a("el-table-column",{attrs:{label:"名称",prop:"name"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.name))]),e.row.icon?a("el-image",{staticClass:"level-icon",attrs:{src:e.row.icon,fit:"fill"}},[a("div",{staticClass:"image-slot",attrs:{slot:"error"},slot:"error"},[a("i",{staticClass:"el-icon-picture-outline"})])]):t._e()]}}])}),a("el-table-column",{attrs:{label:"消费金额",prop:"amount"}}),a("el-table-column",{attrs:{label:"折扣(%)",prop:"discount"}}),a("el-table-column",{attrs:{label:"描述",prop:"description","min-width":"220"}}),a("el-table-column",{attrs:{label:"操作",align:"center","min-width":"100"},scopedSlots:t._u([{key:"default",fn:function(e){return[t.auth.set?a("el-button",{attrs:{size:"small",type:"text"},on:{click:function(a){return t.handleUpdate(e.$index)}}},[t._v("编辑")]):t._e(),t.auth.del?a("el-button",{attrs:{size:"small",type:"text"},on:{click:function(a){return t.handleDelete(e.$index)}}},[t._v("删除")]):t._e()]}}])})],1),a("el-dialog",{attrs:{title:t.textMap[t.dialogStatus],visible:t.dialogFormVisible,"append-to-body":!0,"close-on-click-modal":!1,width:"600px"},on:{"update:visible":function(e){t.dialogFormVisible=e}}},[a("el-form",{ref:"form",attrs:{model:t.form,rules:t.rules,"label-width":"80px"}},[a("el-form-item",{attrs:{label:"名称",prop:"name"}},[a("el-input",{attrs:{placeholder:"请输入等级名称",clearable:!0},model:{value:t.form.name,callback:function(e){t.$set(t.form,"name",e)},expression:"form.name"}})],1),a("el-form-item",{attrs:{label:"等级图标",prop:"icon"}},[a("el-input",{attrs:{placeholder:"可输入等级图标",clearable:!0},model:{value:t.form.icon,callback:function(e){t.$set(t.form,"icon",e)},expression:"form.icon"}},[a("el-dropdown",{attrs:{slot:"append","show-timeout":50},on:{command:t.handleCommand},slot:"append"},[a("el-button",{attrs:{icon:"el-icon-upload"}}),a("el-dropdown-menu",{attrs:{slot:"dropdown"},slot:"dropdown"},[a("el-dropdown-item",{attrs:{command:"storage",icon:"el-icon-finished"}},[t._v("资源选择")]),a("el-dropdown-item",{attrs:{command:"upload",icon:"el-icon-upload2"}},[t._v("上传资源")])],1)],1)],1)],1),a("el-row",[a("el-col",{attrs:{span:12}},[a("el-form-item",{attrs:{label:"消费金额",prop:"amount"}},[a("el-input-number",{attrs:{"controls-position":"right",precision:2,min:0},model:{value:t.form.amount,callback:function(e){t.$set(t.form,"amount",e)},expression:"form.amount"}})],1)],1),a("el-col",{attrs:{span:12}},[a("el-form-item",{attrs:{label:"折扣(%)",prop:"discount"}},[a("el-input-number",{attrs:{"controls-position":"right",min:0,max:100},model:{value:t.form.discount,callback:function(e){t.$set(t.form,"discount",e)},expression:"form.discount"}})],1)],1)],1),a("el-form-item",{attrs:{label:"描述",prop:"description"}},[a("el-input",{attrs:{placeholder:"可输入等级描述",type:"textarea",rows:3},model:{value:t.form.description,callback:function(e){t.$set(t.form,"description",e)},expression:"form.description"}})],1)],1),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{size:"small"},on:{click:function(e){t.dialogFormVisible=!1}}},[t._v("取消")]),"create"===t.dialogStatus?a("el-button",{attrs:{type:"primary",loading:t.dialogLoading,size:"small"},on:{click:t.create}},[t._v("确定")]):a("el-button",{attrs:{type:"primary",loading:t.dialogLoading,size:"small"},on:{click:t.update}},[t._v("修改")])],1),a("cs-storage",{ref:"storage",staticStyle:{display:"none"},attrs:{limit:1},on:{confirm:t._getStorageFileList}}),a("cs-upload",{ref:"upload",staticStyle:{display:"none"},attrs:{type:"slot",accept:"image/*",limit:1,multiple:!1},on:{confirm:t._getUploadFileList}})],1)],1)},i=[],n=a("5530"),l=a("b85c"),r=(a("d3b7"),a("3ca3"),a("ddb0"),a("159b"),a("2f46")),s=a("ca00"),c={components:{csUpload:function(){return a.e("chunk-584c16a3").then(a.bind(null,"27d4"))},csStorage:function(){return a.e("chunk-66d17b97").then(a.bind(null,"85ce"))}},props:{loading:{default:!1},tableData:{default:function(){return[]}}},data:function(){return{currentTableData:[],multipleSelection:[],auth:{add:!1,set:!1,del:!1},dialogLoading:!1,dialogFormVisible:!1,dialogStatus:"",textMap:{update:"编辑等级",create:"新增等级"},form:{name:void 0,icon:void 0,amount:void 0,discount:void 0,description:void 0},rules:{name:[{required:!0,message:"名称不能为空",trigger:"blur"},{max:30,message:"长度不能大于 30 个字符",trigger:"blur"}],icon:[{max:512,message:"长度不能大于 200 个字符",trigger:"blur"}],amount:[{required:!0,message:"消费金额不能为空",trigger:"blur"}],discount:[{required:!0,message:"折扣不能为空",trigger:"blur"}],description:[{max:200,message:"长度不能大于 200 个字符",trigger:"blur"}]}}},watch:{tableData:{handler:function(t){this.currentTableData=t},immediate:!0}},mounted:function(){this._validationAuth()},methods:{_validationAuth:function(){this.auth.add=this.$permission("/member/user/level/add"),this.auth.set=this.$permission("/member/user/level/set"),this.auth.del=this.$permission("/member/user/level/del")},_getIdList:function(t){null===t&&(t=this.multipleSelection);var e=[];return Array.isArray(t)?t.forEach((function(t){e.push(t.user_level_id)})):e.push(this.currentTableData[t].user_level_id),e},handleCommand:function(t){switch(t){case"storage":this.$refs.storage.handleStorageDlg([0,2]);break;case"upload":this.$refs.upload.handleUpload();break}},_getUploadFileList:function(t){if(t.length){var e=t[0].response;e&&200===e.status&&0===e.data[0].type&&(this.form.icon=s["a"].checkUrl(e.data[0].url))}},_getStorageFileList:function(t){if(t.length){var e,a=Object(l["a"])(t);try{for(a.s();!(e=a.n()).done;){var o=e.value;if(0===o.type){this.form.icon=s["a"].checkUrl(o.url);break}}}catch(i){a.e(i)}finally{a.f()}}},handleSelectionChange:function(t){this.multipleSelection=t},handleDelete:function(t){var e=this,a=this._getIdList(t);0!==a.length?this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then((function(){Object(r["b"])(a).then((function(){s["a"].deleteDataList(e.currentTableData,a,"user_level_id"),e.$message.success("操作成功")}))})).catch((function(){})):this.$message.error("请选择要操作的数据")},handleCreate:function(){var t=this;this.form={name:"",amount:0,icon:"",discount:0,description:""},this.$nextTick((function(){t.$refs.form&&t.$refs.form.clearValidate(),t.dialogStatus="create",t.dialogLoading=!1,t.dialogFormVisible=!0}))},create:function(){var t=this;this.$refs.form.validate((function(e){e&&(t.dialogLoading=!0,Object(r["a"])(t.form).then((function(e){t.currentTableData.push(e.data),t.dialogFormVisible=!1,t.$message.success("操作成功")})).catch((function(){t.dialogLoading=!1})))}))},handleUpdate:function(t){var e=this;this.currentIndex=t,this.form=Object(n["a"])({},this.currentTableData[t]),this.$nextTick((function(){e.$refs.form&&e.$refs.form.clearValidate(),e.dialogStatus="update",e.dialogLoading=!1,e.dialogFormVisible=!0}))},update:function(){var t=this;this.$refs.form.validate((function(e){e&&(t.dialogLoading=!0,Object(r["d"])(t.form).then((function(e){t.$set(t.currentTableData,t.currentIndex,Object(n["a"])(Object(n["a"])({},t.currentTableData[t.currentIndex]),e.data)),t.dialogFormVisible=!1,t.$message.success("操作成功")})).catch((function(){t.dialogLoading=!1})))}))}}},d=c,u=(a("f888"),a("2877")),m=Object(u["a"])(d,o,i,!1,null,"91887db4",null);e["default"]=m.exports},aae4:function(t,e,a){},f888:function(t,e,a){"use strict";a("aae4")}}]);