(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-7671b9a2"],{bf33:function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"cs-p"},[a("el-card",{staticClass:"box-card",attrs:{shadow:"never"}},[a("div",{staticClass:"box-card-header",attrs:{slot:"header"},slot:"header"},[a("el-row",{staticClass:"cs-mb-10"},[a("el-col",{attrs:{span:18}},[a("span",{staticClass:"text-explode"},[t._v("主题：")]),a("span",[t._v(t._s(t.tableData.title))])]),a("el-col",{attrs:{span:6}},[a("span",{staticClass:"text-explode"},[t._v("创建日期：")]),a("span",[t._v(t._s(t.tableData.create_time))])])],1),a("el-row",[a("el-col",{attrs:{span:9}},[a("span",{staticClass:"text-explode"},[t._v("编号：")]),a("span",[t._v(t._s(t.tableData.ask_id))])]),a("el-col",{attrs:{span:9}},[a("span",{staticClass:"text-explode"},[t._v("类型：")]),a("span",[t._v(t._s(null!==t.tableData.ask_type?t.typeList[t.tableData.ask_type]:""))])]),a("el-col",{attrs:{span:6}},[a("span",{staticClass:"text-explode"},[t._v("状态：")]),null!==t.tableData.status?a("el-tag",{attrs:{type:t.statusMap[t.tableData.status].type,effect:"plain",size:"mini"}},[t._v(" "+t._s(t.statusMap[t.tableData.status].text)+" ")]):t._e()],1)],1)],1),a("el-timeline",t._l(t.tableData.get_items,(function(e,s){return a("el-timeline-item",{key:s,attrs:{timestamp:e.create_time,type:1===e.type?"primary":"danger",placement:"top"}},[a("el-card",{attrs:{shadow:"never"}},[a("div",{staticClass:"user-icon"},[1===e.type?a("el-avatar",{attrs:{size:"medium",src:t._f("getPreviewUrl")(t.tableData.get_user.head_pic)}},[a("img",{attrs:{src:t.$publicPath+"image/setting/user.png",alt:""}})]):a("el-avatar",{attrs:{size:"medium",src:t.$publicPath+"image/setting/admin.png"}})],1),a("div",{staticClass:"problem"},[a("div",{staticClass:"ask-content cs-pb-10"},[t._v(t._s(1===e.type?e.ask:e.answer))]),a("div",{staticClass:"user-name"},[a("span",[t._v(t._s(1===e.type?t.tableData.get_user.username:"客服人员"))]),1===e.type&&t.tableData.get_user.level_icon?a("el-image",{staticClass:"level-icon",attrs:{src:t.tableData.get_user.level_icon,fit:"fill"}},[a("div",{staticClass:"image-slot",attrs:{slot:"error"},slot:"error"},[a("i",{staticClass:"el-icon-picture-outline"})])]):t._e()],1)])])],1)})),1),a("el-form",{directives:[{name:"permission",rawName:"v-permission",value:"/member/ask/detail",expression:"'/member/ask/detail'"}],ref:"form",attrs:{model:t.form,rules:t.rules,"label-width":"68px"}},[a("el-form-item",{attrs:{prop:"answer"}},[a("el-input",{attrs:{placeholder:"请输入回复内容",type:"textarea",autosize:{minRows:5},"show-word-limit":!0,maxlength:"200"},model:{value:t.form.answer,callback:function(e){t.$set(t.form,"answer",e)},expression:"form.answer"}}),a("el-button",{staticClass:"cs-mt-10",attrs:{type:"primary",loading:t.submitLoading,size:"small"},on:{click:t.handleFormSubmit}},[t._v("提交")])],1)],1)],1)],1)},l=[],i=(a("d3b7"),a("ca00")),r=a("331c"),n={props:{tableData:{default:function(){}}},data:function(){return{form:{},rules:{answer:[{required:!0,message:"回复内容不能为空",trigger:"blur"},{max:200,message:"长度不能大于 200 个字符",trigger:"blur"}]},typeList:{0:"咨询",1:"售后",2:"投诉",3:"求购"},statusMap:{0:{text:"待回复",type:"warning"},1:{text:"已回复",type:"success"}},submitLoading:!1}},filters:{getPreviewUrl:function(t){return t?i["a"].getImageCodeUrl(t,"head_pic"):""}},methods:{handleFormSubmit:function(){var t=this;this.$refs.form.validate((function(e){if(e){t.submitLoading=!0;var a=t.tableData.ask_id;Object(r["d"])(a,t.form.answer).then((function(e){t.form.answer=null,t.$emit("reply",a,e.data),t.$message.success("操作成功")})).finally((function(){t.submitLoading=!1}))}}))}}},c=n,o=(a("d4f9"),a("2877")),p=Object(o["a"])(c,s,l,!1,null,"1661bdcc",null);e["default"]=p.exports},d4f9:function(t,e,a){"use strict";a("de7c")},de7c:function(t,e,a){}}]);