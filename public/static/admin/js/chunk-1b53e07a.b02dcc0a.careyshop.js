(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1b53e07a"],{"041d":function(t,n,e){"use strict";e.d(n,"a",(function(){return d})),e.d(n,"e",(function(){return u})),e.d(n,"d",(function(){return c})),e.d(n,"b",(function(){return i})),e.d(n,"c",(function(){return l}));var o=e("5530"),a=e("bc07"),s="/v1/goods_consult";function d(t){return Object(a["a"])({url:s,method:"post",data:{method:"del.goods.consult.list",goods_consult_id:t}})}function u(t,n){return Object(a["a"])({url:s,method:"post",data:{method:"set.goods.consult.show",goods_consult_id:t,is_show:n}})}function c(t,n){return Object(a["a"])({url:s,method:"post",data:{method:"reply.goods.consult.item",goods_consult_id:t,content:n}})}function i(t){return Object(a["a"])({url:s,method:"post",data:{method:"get.goods.consult.item",goods_consult_id:t}})}function l(t){return Object(a["a"])({url:s,method:"post",data:Object(o["a"])({method:"get.goods.consult.list"},t)})}},"78fb":function(t,n,e){"use strict";e.r(n);var o=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("cs-container",[e("page-main",{attrs:{"table-data":t.table},on:{reply:t.addReply}})],1)},a=[],s=(e("a9e3"),e("d3b7"),e("5530")),d=e("5880"),u=e("041d"),c={name:"goods-opinion-consult-detail",components:{PageMain:function(){return e.e("chunk-d9cadad8").then(e.bind(null,"f1e5"))}},props:{goods_consult_id:{type:[Number,String],required:!0}},data:function(){return{table:this.getInitData()}},watch:{goods_consult_id:{handler:function(){this.getGoodsConsultData()},immediate:!0}},methods:Object(s["a"])(Object(s["a"])({},Object(d["mapActions"])("careyshop/update",["updateData"])),{},{getInitData:function(){return{type:null,status:null,get_goods:{name:null}}},getGoodsConsultData:function(){var t=this;this.table=Object(s["a"])({},this.getInitData()),Object(u["b"])(this.goods_consult_id).then((function(n){n.data&&n.data.get_answer.unshift({goods_consult_id:n.data.goods_consult_id,content:n.data.content,create_time:n.data.create_time,is_client:!0}),t.table=Object(s["a"])({},n.data)}))},addReply:function(t,n){this.table.status=1,this.table.get_answer.push(Object(s["a"])({},n)),this.updateData({type:"set",name:"goods-opinion-consult",srcId:t,data:{status:1}})}})},i=c,l=e("2877"),r=Object(l["a"])(i,o,a,!1,null,null,null);n["default"]=r.exports}}]);