(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-d9cadad8"],{"13f3":function(t,e,a){},"8d41":function(t,e,a){"use strict";a("13f3")},f1e5:function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"cs-p"},[a("el-card",{staticClass:"box-card",attrs:{shadow:"never"}},[a("div",{staticClass:"box-card-header",attrs:{slot:"header"},slot:"header"},[a("el-row",{staticClass:"cs-mb-10"},[a("el-col",{attrs:{span:18}},[a("span",{staticClass:"text-explode"},[t._v("商品：")]),a("span",{staticClass:"goods-link",on:{click:function(e){return t.handleView(t.tableData.get_goods.goods_id)}}},[t._v(t._s(t.tableData.get_goods.name))])]),a("el-col",{attrs:{span:6}},[a("span",{staticClass:"text-explode"},[t._v("创建日期：")]),a("span",[t._v(t._s(t.tableData.create_time))])])],1),a("el-row",[a("el-col",{attrs:{span:9}},[a("span",{staticClass:"text-explode"},[t._v("编号：")]),a("span",[t._v(t._s(t.tableData.goods_consult_id))])]),a("el-col",{attrs:{span:9}},[a("span",{staticClass:"text-explode"},[t._v("类型：")]),a("span",[t._v(t._s(null!==t.tableData.type?t.typeList[t.tableData.type]:""))])]),a("el-col",{attrs:{span:6}},[a("span",{staticClass:"text-explode"},[t._v("状态：")]),null!==t.tableData.status?a("el-tag",{attrs:{type:t.statusMap[t.tableData.status].type,effect:"plain",size:"mini"}},[t._v(" "+t._s(t.statusMap[t.tableData.status].text)+" ")]):t._e()],1)],1)],1),a("el-timeline",t._l(t.tableData.get_answer,(function(e,s){return a("el-timeline-item",{key:s,attrs:{timestamp:e.create_time,type:e.is_client?"primary":"danger",placement:"top"}},[a("el-card",{attrs:{shadow:"never"}},[a("div",{staticClass:"user-icon"},[e.is_client&&t.tableData.get_user.head_pic?a("el-avatar",{attrs:{size:"medium",src:t._f("getPreviewUrl")(t.tableData.get_user.head_pic)}},[a("img",{attrs:{src:t.$publicPath+"image/setting/user.png",alt:""}})]):e.is_client?a("el-avatar",{attrs:{size:"medium",src:t.$publicPath+"image/setting/user.png"}}):a("el-avatar",{attrs:{size:"medium",src:t.$publicPath+"image/setting/admin.png"}})],1),a("div",{staticClass:"problem"},[a("div",{staticClass:"consult-content cs-pb-10"},[t._v(t._s(e.content))]),a("div",{staticClass:"user-name"},[e.is_client?a("span",[t._v(t._s(t.tableData.get_user.username||"游客"))]):a("span",[t._v("客服人员")]),e.is_client&&t.tableData.get_user.level_icon?a("el-image",{staticClass:"level-icon",attrs:{src:t.tableData.get_user.level_icon,fit:"fill"}},[a("div",{staticClass:"image-slot",attrs:{slot:"error"},slot:"error"},[a("i",{staticClass:"el-icon-picture-outline"})])]):t._e()],1)])])],1)})),1),a("el-form",{directives:[{name:"permission",rawName:"v-permission",value:"/goods/opinion/consult/detail",expression:"'/goods/opinion/consult/detail'"}],ref:"form",attrs:{model:t.form,rules:t.rules,"label-width":"68px"}},[a("el-form-item",{attrs:{prop:"content"}},[a("el-input",{attrs:{placeholder:"请输入回复内容",type:"textarea",autosize:{minRows:5},"show-word-limit":!0,maxlength:"200"},model:{value:t.form.content,callback:function(e){t.$set(t.form,"content",e)},expression:"form.content"}}),a("el-button",{staticClass:"cs-mt-10",attrs:{type:"primary",loading:t.submitLoading,size:"small"},on:{click:t.handleFormSubmit}},[t._v("提交")])],1)],1)],1)],1)},i=[],l=(a("d3b7"),a("ca00")),n=a("041d"),r={props:{tableData:{default:function(){}}},data:function(){return{form:{content:void 0},rules:{content:[{required:!0,message:"回复内容不能为空",trigger:"blur"},{max:200,message:"长度不能大于 200 个字符",trigger:"blur"}]},typeList:{0:"商品咨询",1:"支付",2:"配送",3:"售后"},statusMap:{0:{text:"待回复",type:"warning"},1:{text:"已回复",type:"success"}},submitLoading:!1}},filters:{getPreviewUrl:function(t){return t?l["a"].getImageCodeUrl(t,"head_pic"):""}},methods:{handleFormSubmit:function(){var t=this;this.$refs.form.validate((function(e){if(e){t.submitLoading=!0;var a=t.tableData.goods_consult_id;Object(n["d"])(a,t.form.content).then((function(e){t.form.content=null,t.$emit("reply",a,e.data),t.$message.success("操作成功")})).finally((function(){t.submitLoading=!1}))}}))},handleView:function(t){this.$router.push({name:"goods-admin-view",params:{goods_id:t}})}}},o=r,c=(a("8d41"),a("2877")),u=Object(c["a"])(o,s,i,!1,null,"eb725fbc",null);e["default"]=u.exports}}]);