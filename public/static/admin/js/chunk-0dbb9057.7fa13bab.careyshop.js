(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0dbb9057"],{2680:function(e,t,n){"use strict";n.d(t,"a",(function(){return i})),n.d(t,"b",(function(){return s})),n.d(t,"c",(function(){return d})),n.d(t,"e",(function(){return u})),n.d(t,"h",(function(){return c})),n.d(t,"j",(function(){return h})),n.d(t,"d",(function(){return l})),n.d(t,"f",(function(){return f})),n.d(t,"g",(function(){return p})),n.d(t,"i",(function(){return g}));var a=n("5530"),r=n("bc07"),o="/v1/order_service";function i(e,t){return Object(r["a"])({url:o,method:"post",data:{method:"add.order.service.message",service_no:e,message:t}})}function s(e){return Object(r["a"])({url:o,method:"post",data:{method:"get.order.service.item",service_no:e}})}function d(e){return Object(r["a"])({url:o,method:"post",data:Object(a["a"])({method:"get.order.service.list"},e)})}function u(e){return Object(r["a"])({url:o,method:"post",data:{method:"set.order.service.agree",service_no:e}})}function c(e,t){return Object(r["a"])({url:o,method:"post",data:{method:"set.order.service.refused",service_no:e,result:t}})}function h(e,t){return Object(r["a"])({url:o,method:"post",data:{method:"set.order.service.sendback",service_no:e,is_return:t}})}function l(e){return Object(r["a"])({url:o,method:"post",data:{method:"set.order.service.after",service_no:e}})}function f(e){return Object(r["a"])({url:o,method:"post",data:{method:"set.order.service.cancel",service_no:e}})}function p(e){return Object(r["a"])({url:o,method:"post",data:Object(a["a"])({method:"set.order.service.complete"},e)})}function g(e){return Object(r["a"])({url:o,method:"post",data:Object(a["a"])({method:"set.order.service.remark"},e)})}},"34c0":function(e,t,n){"use strict";n.r(t);var a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("cs-container",[n("page-header",{ref:"header",attrs:{slot:"header",loading:e.loading,"type-map":e.typeMap},on:{submit:e.handleSubmit},slot:"header"}),n("page-main",{attrs:{loading:e.loading,"type-map":e.typeMap,"table-data":e.table},on:{tabs:e.handleTabs,refresh:e.handleRefresh}}),n("page-footer",{attrs:{slot:"footer",loading:e.loading,"page-no":e.page.page_no,"page-size":e.page.page_size,total:e.pageTotal},on:{change:e.handlePaginationChange},slot:"footer"})],1)},r=[],o=n("5530"),i=(n("d3b7"),n("3ca3"),n("ddb0"),n("b0c0"),n("2680")),s={name:"order-service-list",components:{PageHeader:function(){return n.e("chunk-6264ee86").then(n.bind(null,"b6c2"))},PageMain:function(){return n.e("chunk-3386ae32").then(n.bind(null,"4350"))},PageFooter:function(){return n.e("chunk-2d0bd262").then(n.bind(null,"2b84"))}},data:function(){return{loading:!1,table:[],pageTotal:0,status:null,typeMap:{0:"仅退款",1:"退货退款",2:"换货",3:"维修"},page:{page_no:1,page_size:0}}},mounted:function(){var e=this;this.$store.dispatch("careyshop/db/databasePage",{user:!0}).then((function(t){e.page.page_size=t.get("size").value()||25})).then((function(){e.handleSubmit()}))},beforeRouteEnter:function(e,t,n){"order-service-info"===t.name?n((function(e){e.$refs.header&&e.$refs.header.handleFormSubmit()})):n()},methods:{handleRefresh:function(){var e=this,t=arguments.length>0&&void 0!==arguments[0]&&arguments[0];t&&(!(this.page.page_no-1)||this.page.page_no--),this.$nextTick((function(){e.$refs.header.handleFormSubmit()}))},handlePaginationChange:function(e){var t=this;this.page=e,(e.page_no-1)*e.page_size>this.pageTotal||this.$nextTick((function(){t.$refs.header.handleFormSubmit()}))},handleTabs:function(e){var t=this;this.status=e<=0?null:e-1,this.$nextTick((function(){t.$refs.header.handleFormSubmit(!0)}))},handleSubmit:function(e){var t=this,n=arguments.length>1&&void 0!==arguments[1]&&arguments[1];n&&(this.page.page_no=1),this.loading=!0,Object(i["c"])(Object(o["a"])(Object(o["a"])(Object(o["a"])({},e),this.page),{},{status:this.status})).then((function(e){t.table=e.data.items||[],t.pageTotal=e.data.total_result})).finally((function(){t.loading=!1}))}}},d=s,u=n("2877"),c=Object(u["a"])(d,a,r,!1,null,null,null);t["default"]=c.exports}}]);