import Vue from 'vue';
import draggable from 'vuedraggable';
import VueFormGenerator from 'vue-form-generator';

import RowBlock from './components/row.vue';
import ColumnBlock from './components/column.vue';
import RegularBlock from './components/regular.vue';

require('./custom-fields')

Vue.component('RowBlock',RowBlock);
Vue.component('ColumnBlock',ColumnBlock);
Vue.component('RegularBlock',RegularBlock);



export default function()
{

    window.editBlockScreen = new Vue({
        el:'#editBlockScreenApp',
        data:{
            modal:false,
            item:{},
            block:{},
            model:{},
            onEdit:false,
            template_i18n:template_i18n,
            options:{},
            tmp_block:{},
            show:false

        },
        mounted(){
            var me = this;
            this.modal = $('#editBlockScreen');
            this.$nextTick(function () {
                // me.modal.modal({
                //     show:false
                // });
                me.modal.on('hidden.bs.modal',function(){
                    me.show = false;
                })
            })
        },
        components: {
            "vue-form-generator": VueFormGenerator.component,
        },
        methods:{
            openEdit(item,block){
                var me = this;
                console.log(me);
                this.item = item;
                this.tmp_block = Object.assign({},block);
                _.forEach(this.tmp_block.settings,function(item){
                    if(typeof item.conditions === 'undefined') return true;

                    item.visible = function () {
                        var status = true;
                        _.forEach(item.conditions,function (value,key) {
                            if(me.model[key] != value && !value.includes(me.model[key])){
                                status = false;
                            }
                        })
                        return status;
                    }
                });

                this.block = block;
                this.model = JSON.parse(JSON.stringify(this.item.model || {}));
                this.modal.modal('show');

                this.show = true;

                manageBlocksScreen.message.content = '';
            },
            saveModal(){
                this.item.model = JSON.parse(JSON.stringify(this.model || {}));
                this.onEdit = false;
                this.hideModal();
            },
            hideModal(){
                this.modal.modal('toggle');
            }

        }
    })

    window.manageBlocksScreen = new Vue({
        el:'#booking-core-template-detail',
        data:{
            items:current_template_items,
            blocks:[],
            title:current_template_title,
            message:{
                content:'',
                type:false
            },
            onSaving:false,
            s:''
        },
        mounted(){
            this.reloadBlocks();
        },
        computed:{
            filteredBlocks:function(){
                const res = {};
                if(!this.s) return this.blocks;

                Object.entries(this.blocks).forEach(([groupId,group])=>{
                    res[groupId] = Object.assign({},group);
                    res[groupId].items = group.items.filter((item)=>{
                        return item.name.toLowerCase().includes(this.s.toLowerCase());
                    }) || [];
                })
                return res;
            }
        },
        methods:{
            deleteBlock(index){
                console.log(index);
                //console.log(this.items);
                this.items.splice(index,1);

            },
            syncSliderLinksBeforeSave(){
                this.items.forEach(function (item) {
                    if (item.type !== 'form_search_all_service' || !item.model) {
                        return;
                    }
                    var model = item.model;
                    var lines = String(model.slider_links || '').split(/\r\n|\r|\n/);
                    var slides = model.list_slider;
                    if (!Array.isArray(slides)) {
                        return;
                    }
                    slides.forEach(function (slide, index) {
                        if (!slide || typeof slide !== 'object') {
                            return;
                        }
                        var bulkLine = (lines[index] || '').trim();
                        var perSlide = String(slide.link_url || slide.url || slide.link || '').trim();
                        if (bulkLine) {
                            slide.link_url = bulkLine;
                        } else if (perSlide) {
                            slide.link_url = perSlide;
                        }
                    });
                    model.slider_links = slides.map(function (slide) {
                        return slide && slide.link_url ? String(slide.link_url).trim() : '';
                    }).join('\n');
                });
            },
            flushPendingBlockEdits(){
                if(window.editBlockScreen && window.editBlockScreen.show && window.editBlockScreen.item){
                    window.editBlockScreen.item.model = JSON.parse(JSON.stringify(window.editBlockScreen.model || {}));
                }
            },
            saveTemplate(){
                var me = this;
                if(!this.title){
                    return false;
                }

                this.flushPendingBlockEdits();
                this.syncSliderLinksBeforeSave();
                this.onSaving = true;

                $.ajax({
                    url:bookingCore.admin_url+'/module/template/store',
                    dataType:'json',
                    type:'post',
                    data:{
                        id:template_id,
                        content:JSON.stringify(this.items),
                        title:this.title,
                        lang:current_menu_lang
                    },
                    success:function (res) {
                        me.onSaving = false;
                        if(res.message)
                        {
                            me.message.content = res.message;
                            me.message.type = res.status;
                        }
                        if(res.url){
                            window.location.href = res.url;
                        } else if (res.status) {
                            window.location.reload();
                        }
                    },
                    error:function (e) {
                        me.onSaving = false;

                        if(e.responseJSON.message){
                            me.message.content = e.responseJSON.message;
                            me.message.type = false;
                        }else{

                            me.message.content = 'Can not save menu';
                            me.message.type = false;
                        }

                    }
                })
            },
            reloadBlocks(){
                var me = this;

                $.ajax({
                    url:bookingCore.admin_url+'/module/template/getBlocks',
                    dataType:'json',
                    type:'get',
                    success:function (res) {
                        if(res.status){
                            me.blocks = res.data
                        }
                    },
                    error:function (e) {
                        console.log(e);

                    }
                })
            },
            addBlock(block,toItem = false){

                /*if(toItem == false && block.id!='row'){
                    var blockRow = this.searchBlockById('row');
                    var blockColumn = this.searchBlockById('column');

                    var row = this.addBlock(blockRow);

                    if(block.id!='column'){
                        toItem = this.addBlock(blockColumn,row);
                    }else{
                        toItem = row;
                    }

                }*/

                var item = this.getBlockParams(block);

                if(toItem){
                    toItem.children.push(item);
                }else{
                    this.items.push(item);
                }

                return item;

            },
            getBlockParams(block){
                let res =  {
                    type:block.id,
                    name:block.name,
                    model:block.model,
                    component:block.component,
                    open:true
                }

                if(block.is_container){
                    res.is_container = true;
                    res.children = [];
                }

                return res;
            },
            searchBlockById(id){
               /* for(var i =0 ; i < this.blocks.length ; i++ ){
                    if(this.blocks[i].id == id ){
                        return this.blocks[i];
                    }
                }*/
                for (var key in this.blocks) {
                    var block = this.blocks[key];
                    for(var i =0 ; i < block.items.length ; i++ ) {
                        if (block.items[i].id == id) {
                            return block.items[i];
                        }
                    }
                }
            }
        },
        components:{
            RowBlock,
            ColumnBlock,
            RegularBlock,
            draggable:draggable
        },
    });
}



