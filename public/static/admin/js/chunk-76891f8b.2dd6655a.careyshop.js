(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-76891f8b"],{"0572":function(t,e,o){"use strict";o.d(e,"a",(function(){return a})),o.d(e,"g",(function(){return r})),o.d(e,"c",(function(){return c})),o.d(e,"d",(function(){return s})),o.d(e,"e",(function(){return d})),o.d(e,"b",(function(){return l})),o.d(e,"h",(function(){return p})),o.d(e,"f",(function(){return f}));var n=o("5530"),i=o("bc07"),u="/v1/coupon";function a(t){return Object(i["a"])({url:u,method:"post",data:Object(n["a"])({method:"add.coupon.item"},t)})}function r(t){return Object(i["a"])({url:u,method:"post",data:Object(n["a"])({method:"set.coupon.item"},t)})}function c(t){return Object(i["a"])({url:u,method:"post",data:{method:"get.coupon.item",coupon_id:t}})}function s(t){return Object(i["a"])({url:u,method:"post",data:Object(n["a"])({method:"get.coupon.list"},t)})}function d(t){return Object(i["a"])({url:u,method:"post",data:Object(n["a"])({method:"get.coupon.select"},t)})}function l(t){return Object(i["a"])({url:u,method:"post",data:{method:"del.coupon.list",coupon_id:t}})}function p(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"set.coupon.status",coupon_id:t,status:e}})}function f(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"set.coupon.invalid",coupon_id:t,is_invalid:e}})}},"25d5":function(t,e,o){"use strict";o("ac30")},"51a7":function(t,e,o){"use strict";o.r(e);var n=function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",[o("el-table",{attrs:{data:t.discountList}},[o("el-table-column",{attrs:{label:"编号",prop:"goods_id","min-width":"15"}}),o("el-table-column",{attrs:{label:"商品名称",prop:"name"},scopedSlots:t._u([{key:"default",fn:function(e){return[o("div",{staticClass:"discount-text",attrs:{title:t._f("filterGoodsName")(e.row.goods)}},[o("span",{staticClass:"link",on:{click:function(o){return t.handleViewGoods(e.row.goods_id)}}},[t._v(t._s(t._f("filterGoodsName")(e.row.goods)))])])]}}])}),o("el-table-column",{attrs:{label:t.typeMap[t.type]||"折扣方式",width:"160"},scopedSlots:t._u([{key:"header",fn:function(e){return[o("el-tooltip",{attrs:{placement:"top",content:t.typeHelp[t.type]}},[o("i",{directives:[{name:"show",rawName:"v-show",value:t.typeHelp[t.type],expression:"typeHelp[type]"}],staticClass:"el-icon-warning-outline cs-mr-10"})]),o("span",[t._v(t._s(e.column.label))])]}},{key:"default",fn:function(e){return["3"===t.type?o("el-select",{attrs:{value:t.filterCoupon(e.row.discount),placeholder:"请选择",size:"mini"},on:{change:function(t){e.row.discount=t}}},t._l(t.couponData,(function(t){return o("el-option",{key:t.coupon_id,attrs:{label:t.name,value:t.coupon_id}})})),1):o("el-input-number",{attrs:{"controls-position":"right",placeholder:"请输入",size:"mini",max:"0"===t.type?100:Number.MAX_SAFE_INTEGER,min:0,precision:2},model:{value:e.row.discount,callback:function(o){t.$set(e.row,"discount",o)},expression:"scope.row.discount"}})]}}])}),o("el-table-column",{attrs:{label:t.type,align:"center",width:"80"},scopedSlots:t._u([{key:"header",fn:function(e){return[o("el-popover",{attrs:{placement:"top-end",trigger:"manual"},model:{value:t.batchVisible,callback:function(e){t.batchVisible=e},expression:"batchVisible"}},["3"===t.type?o("el-select",{staticClass:"cs-mb-10",attrs:{placeholder:"请选择"+t.typeMap[e.column.label],size:"mini"},model:{value:t.batchValue,callback:function(e){t.batchValue=e},expression:"batchValue"}},t._l(t.couponData,(function(t){return o("el-option",{key:t.coupon_id,attrs:{label:t.name,value:t.coupon_id}})})),1):o("el-input-number",{staticStyle:{width:"150px","margin-bottom":"10px"},attrs:{placeholder:"请输入"+t.typeMap[e.column.label],"controls-position":"right",size:"mini",max:"0"===t.type?100:Number.MAX_SAFE_INTEGER,min:0,precision:2},model:{value:t.batchValue,callback:function(e){t.batchValue=e},expression:"batchValue"}}),o("div",{staticClass:"cs-tr"},[o("el-button",{attrs:{size:"mini",type:"text"},on:{click:function(e){t.batchVisible=!1}}},[t._v("取消")]),o("el-button",{attrs:{type:"primary",size:"mini"},on:{click:t.batchDiscount}},[t._v("确定")])],1),o("el-button",{attrs:{slot:"reference",disabled:!t.type,type:"text"},on:{click:t.handleBatch},slot:"reference"},[t._v("批处理")])],1)]}},{key:"default",fn:function(e){return[o("el-button",{attrs:{type:"text",size:"small"},on:{click:function(o){return t.remove(e.$index)}}},[t._v("删除")])]}}])})],1),o("cs-goods-drawer",{ref:"goodsDrawer"})],1)},i=[],u=(o("7db0"),o("4160"),o("a434"),o("b0c0"),o("d3b7"),o("3ca3"),o("159b"),o("ddb0"),o("b85c")),a=o("0572"),r=o("a2a9"),c={components:{csGoodsDrawer:function(){return o.e("chunk-27edba6e").then(o.bind(null,"ed81"))}},props:{value:{type:Array,required:!0,default:function(){}},type:{type:String,required:!1,default:null},status:{type:String,required:!0,default:""},typeMap:{default:function(){}}},computed:{discountList:{get:function(){return this.value},set:function(t){this.$emit("input",t)}}},data:function(){return{typeHelp:{0:"打折额度，比如65表示按6.5折结算",1:"减多少额度，比如65表示在原价的基础上减去65",2:"固定价格，比如65则按65的价格结算",3:"赠送优惠劵，订单完成后赠送指定的优惠劵给顾客"},batchValue:void 0,batchVisible:!1,couponData:[]}},watch:{type:{handler:function(){this.discountList.forEach((function(t){t.discount=void 0}))}}},filters:{filterGoodsName:function(t){return t?t.name:""}},mounted:function(){var t=this,e=[Object(a["e"])({type:3,status:1,is_invalid:0,is_shelf_life:1})];if(this.value.length&&"update"===this.status){var o=[];this.value.forEach((function(t){o.push(t.goods_id)})),o.length&&e.push(Object(r["h"])(o))}Promise.all(e).then((function(e){if(t.couponData=e[0].data||[],e[1]&&e[1].data){var o,n=Object(u["a"])(t.discountList);try{var i=function(){var n=o.value,i=e[1].data.find((function(t){return t.goods_id===n.goods_id}));t.$set(n,"goods",i)};for(n.s();!(o=n.n()).done;)i()}catch(a){n.e(a)}finally{n.f()}}}))},methods:{remove:function(t){this.discountList.splice(t,1)},filterCoupon:function(t){return this.couponData.find((function(e){return e.coupon_id===t}))?t:null},handleBatch:function(){this.batchValue=void 0,this.batchVisible=!0},batchDiscount:function(){var t=this;this.batchVisible=!1,this.discountList.forEach((function(e){e.discount=t.batchValue}))},handleViewGoods:function(t){var e=this;this.$nextTick((function(){e.$refs.goodsDrawer.show(t)}))}}},s=c,d=(o("25d5"),o("2877")),l=Object(d["a"])(s,n,i,!1,null,"406b981a",null);e["default"]=l.exports},a2a9:function(t,e,o){"use strict";o.d(e,"a",(function(){return a})),o.d(e,"l",(function(){return r})),o.d(e,"g",(function(){return c})),o.d(e,"c",(function(){return s})),o.d(e,"h",(function(){return d})),o.d(e,"p",(function(){return l})),o.d(e,"o",(function(){return p})),o.d(e,"n",(function(){return f})),o.d(e,"q",(function(){return h})),o.d(e,"f",(function(){return m})),o.d(e,"j",(function(){return b})),o.d(e,"d",(function(){return g})),o.d(e,"m",(function(){return _})),o.d(e,"b",(function(){return v})),o.d(e,"e",(function(){return y})),o.d(e,"i",(function(){return j})),o.d(e,"k",(function(){return O}));var n=o("5530"),i=o("bc07"),u="/v1/goods";function a(t){return Object(i["a"])({url:u,method:"post",data:Object(n["a"])({method:"add.goods.item"},t)})}function r(t){return Object(i["a"])({url:u,method:"post",data:Object(n["a"])({method:"set.goods.item"},t)})}function c(t){return Object(i["a"])({url:u,method:"post",data:{method:"get.goods.item",goods_id:t}})}function s(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"del.goods.list",goods_id:t,is_delete:e}})}function d(t){return Object(i["a"])({url:u,method:"post",data:{method:"get.goods.select",goods_id:t}})}function l(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"set.recommend.goods.list",goods_id:t,is_recommend:e}})}function p(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"set.new.goods.list",goods_id:t,is_new:e}})}function f(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"set.hot.goods.list",goods_id:t,is_hot:e}})}function h(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"set.shelves.goods.list",goods_id:t,status:e}})}function m(t){return Object(i["a"])({url:u,method:"post",data:{method:"get.goods.attr.list",goods_id:t}})}function b(t){return Object(i["a"])({url:u,method:"post",data:{method:"get.goods.spec.list",goods_id:t}})}function g(t){return Object(i["a"])({url:u,method:"post",data:Object(n["a"])({method:"get.goods.admin.list"},t)})}function _(t,e){return Object(i["a"])({url:u,method:"post",data:{method:"set.goods.sort",goods_id:t,sort:e}})}function v(t){return Object(i["a"])({url:u,method:"post",data:{method:"copy.goods.item",goods_id:t}})}function y(t){return Object(i["a"])({url:u,method:"post",data:{method:"get.goods.attr.config",goods_id:t}})}function j(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;return Object(i["a"])({url:u,method:"post",data:{method:"get.goods.spec.config",goods_id:t,key_to_array:e}})}function O(t){return Object(i["a"])({url:u,method:"post",data:{method:"get.goods.spec.menu",goods_id:t}})}},ac30:function(t,e,o){}}]);