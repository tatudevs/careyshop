(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-b25f1084"],{"7f04":function(t,e,n){"use strict";n.d(e,"a",(function(){return r})),n.d(e,"e",(function(){return i})),n.d(e,"b",(function(){return s})),n.d(e,"c",(function(){return u})),n.d(e,"d",(function(){return c}));var o=n("5530"),a=n("bc07"),d="/v1/goods_type";function r(t){return Object(a["a"])({url:d,method:"post",data:{method:"add.goods.type.item",type_name:t}})}function i(t){return Object(a["a"])({url:d,method:"post",data:Object(o["a"])({method:"set.goods.type.item"},t)})}function s(t){return Object(a["a"])({url:d,method:"post",data:{method:"del.goods.type.list",goods_type_id:t}})}function u(t){return Object(a["a"])({url:d,method:"post",data:Object(o["a"])({method:"get.goods.type.list"},t)})}function c(t){return Object(a["a"])({url:d,method:"post",data:Object(o["a"])({method:"get.goods.type.select"},t)})}},"8d36":function(t,e,n){"use strict";n.r(e);var o=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("cs-container",[n("page-header",{ref:"header",attrs:{slot:"header",loading:t.loading,"type-id":t.goods_type_id,"type-data":t.typeList},on:{submit:t.handleSubmit},slot:"header"}),n("page-main",{attrs:{loading:t.loading,"table-data":t.table,"type-data":t.typeList,"select-id":t.selectTypeId},on:{sort:t.handleSort,refresh:t.handleRefresh}}),n("page-footer",{attrs:{slot:"footer",loading:t.loading,current:t.page.current,size:t.page.size,total:t.page.total},on:{change:t.handlePaginationChange},slot:"footer"})],1)},a=[],d=n("5530"),r=(n("d3b7"),n("3ca3"),n("ddb0"),n("a9e3"),n("7f04")),i=n("b9ad"),s={name:"goods-setting-spec",components:{PageHeader:function(){return n.e("chunk-2d0b93c6").then(n.bind(null,"31a3"))},PageMain:function(){return n.e("chunk-5ef1ba38").then(n.bind(null,"0982"))},PageFooter:function(){return n.e("chunk-2d0bd262").then(n.bind(null,"2b84"))}},props:{goods_type_id:{type:[String,Number],required:!1}},data:function(){return{table:[],loading:!1,typeList:[],selectTypeId:null,page:{current:1,size:0,total:0},order:{order_type:void 0,order_field:void 0}}},mounted:function(){var t=this;Promise.all([Object(r["d"])({order_field:"goods_type_id",order_type:"asc"}),this.$store.dispatch("careyshop/db/databasePage",{user:!0})]).then((function(e){t.typeList=e[0].data||[],t.page.size=e[1].get("size").value()||25})).then((function(){t.handleSubmit({goods_type_id:t.goods_type_id},!0)}))},methods:{handleRefresh:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]&&arguments[0];e&&(!(this.page.current-1)||this.page.current--),this.$nextTick((function(){t.$refs.header.handleFormSubmit()}))},handlePaginationChange:function(t){var e=this;this.page=t,this.$nextTick((function(){e.$refs.header.handleFormSubmit()}))},handleSort:function(t){var e=this;this.order=t,this.$nextTick((function(){e.$refs.header.handleFormSubmit()}))},handleSubmit:function(t){var e=this,n=arguments.length>1&&void 0!==arguments[1]&&arguments[1];n&&(this.page.current=1),this.loading=!0,this.selectTypeId=t.goods_type_id||null,Object(i["e"])(Object(d["a"])(Object(d["a"])(Object(d["a"])({},t),this.order),{},{page_no:this.page.current,page_size:this.page.size})).then((function(t){e.table=t.data.items||[],e.page.total=t.data.total_result})).finally((function(){e.loading=!1}))}}},u=s,c=n("2877"),l=Object(c["a"])(u,o,a,!1,null,null,null);e["default"]=l.exports},b9ad:function(t,e,n){"use strict";n.d(e,"a",(function(){return r})),n.d(e,"f",(function(){return i})),n.d(e,"e",(function(){return s})),n.d(e,"d",(function(){return u})),n.d(e,"c",(function(){return c})),n.d(e,"b",(function(){return l})),n.d(e,"g",(function(){return h})),n.d(e,"h",(function(){return p}));var o=n("5530"),a=n("bc07"),d="/v1/spec";function r(t){return Object(a["a"])({url:d,method:"post",data:Object(o["a"])({method:"add.goods.spec.item"},t)})}function i(t){return Object(a["a"])({url:d,method:"post",data:Object(o["a"])({method:"set.goods.spec.item"},t)})}function s(t){return Object(a["a"])({url:d,method:"post",data:Object(o["a"])({method:"get.goods.spec.page"},t)})}function u(t){return Object(a["a"])({url:d,method:"post",data:{method:"get.goods.spec.list",goods_type_id:t}})}function c(){return Object(a["a"])({url:d,method:"post",data:{method:"get.goods.spec.all"}})}function l(t){return Object(a["a"])({url:d,method:"post",data:{method:"del.goods.spec.list",spec_id:t}})}function h(t,e){return Object(a["a"])({url:d,method:"post",data:{method:"set.goods.spec.key",spec_id:t,spec_index:e}})}function p(t,e){return Object(a["a"])({url:d,method:"post",data:{method:"set.goods.spec.sort",spec_id:t,sort:e}})}}}]);