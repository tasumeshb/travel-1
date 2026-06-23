@extends('admin.layouts.app')
@push('css')
    <link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
@endpush
    @section('content')
    <div class="container-fluid" id="booking-core-template-detail" v-cloak="">
        <div class="d-flex justify-content-between mb20">
            <div class="">
                <h1 class="title-bar">
                    @if(!empty($row->id))
                        {{__("Edit Template:")}} @{{title}}
                    @else
                        {{__('Create new template')}}
                    @endif
                </h1>
            </div>
        </div>
        <div class="alert" v-show="message.content" :class="message.type ? 'alert-success' : 'alert-danger'">@{{message.content}}</div>
        <input type="text" class="form-control" value="{{$row->title ?? ''}}" v-model="title" placeholder="{{__('Template Name')}}">
        <br>
        <br>
        <div class="row">
            <div class="col-md-4 col-xl-4 block-types-menu">
                <div class="">
                    <div class="panel-body">
                        <input type="text" class="form-control" value="" v-model="s" placeholder="{{__('Search for block...')}}">
                        <hr>
                        <div :key="index" v-for="(block,index) in filteredBlocks" class="card" style="margin-bottom: 0px;border-radius: 0px;margin-top:-1px" v-show="block.items.length">
                            <div class="card-header d-flex justify-content-between font-weight-bold"  @click="block.open = block.open ? false : true" :style="{'border-bottom-width':block.open ? 1 : 0 }">@{{block.name}}
                                <div class="cursor-pointer"><i class="fa" :class="{'fa-minus':block.open,'fa-plus':!block.open}"></i></div>
                            </div>
                            <div  v-show="block.open" class="card-body">
                                <div class="list-scrollable" >
                                    <div class="block-panel" v-for="item in block.items">
                                        <div class="block-title">
                                            @{{item.name}}
                                            <div class="title-right">
                                                <span class="menu-add"><i @click="addBlock(item)" class="icon ion-ios-add-circle-outline"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-8 col-xl-8">
                @include('Language::admin.navigation')
                <div class="lang-content-box">
                    <div class="panel">
                        <div class="panel-title">{{__('Template Content')}}</div>
                        <div class="panel-body">
                            <div class="templates-items-zone">
                                <draggable v-model="items">
                                    <component v-on:delete="deleteBlock" :block="searchBlockById(item.type)" :is="item.component" :item="item" v-for="(item,index) in items" :index=index :key="index"></component>
                                </draggable>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <span class="alert-text" v-show="message.content" :class="message.type ? 'success' : 'danger'">@{{message.content}}</span>
                            @if(empty($row->id) and app()->getLocale() != setting_item('site_locale'))
                                {{__('You need to create the template at the Main-language tab first!')}}
                            @else
                                <span class="btn btn-success" id="btn-save-home-template">{{__("Save Template")}}
                                <i class="fa fa-spin fa-spinner" id="btn-save-template-spinner" style="display:none;"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade edit-block-item-modal" id="editBlockScreen" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" v-if="block.id" id="editBlockScreenApp">
                <div class="modal-header">
                    <h5 class="modal-title">@{{block.name}}</h5>
                    <button type="button" @click="hideModal" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" v-if="show">
                    <vue-form-generator :key="block._key_id" :schema="{fields:block.settings}" :model="model" :options="options"></vue-form-generator>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="hideModal" data-dismiss="modal">@{{template_i18n.cancel}}</button>
                    <button type="button" class="btn btn-primary" @click="saveModal">@{{template_i18n.save_changes}}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var current_template_items = {!! json_encode($translation->content_json) !!};
        var current_template_title = '{{$translation->title ?? ''}}';
        var template_id = {{$row->id ?? 0}};
		var current_menu_lang = '{{request()->query('lang',get_main_lang())}}';
    </script>
@endsection
@push('js')
<script>
(function () {
    var storeUrl = @json(route('template.admin.store'));
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

    function syncSliderLinksInItems(items) {
        if (!items || !items.length) return;
        items.forEach(function (item) {
            if (item.type !== 'form_search_all_service' || !item.model) return;
            var model = item.model;
            var lines = String(model.slider_links || '').split(/\r\n|\r|\n/);
            var slides = model.list_slider;
            if (!Array.isArray(slides)) return;
            slides.forEach(function (slide, index) {
                if (!slide || typeof slide !== 'object') return;
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
    }

    function syncListItemsFromModalDom() {
        var editor = window.editBlockScreen;
        if (!editor || !editor.model || !Array.isArray(editor.model.list_item)) return;
        if (!$('#editBlockScreen').hasClass('show')) return;

        $('#editBlockScreen .bravo-template-list-item .list-item').each(function (idx) {
            var row = editor.model.list_item[idx];
            if (!row) return;

            $(this).find('.form-group').each(function () {
                var label = $(this).find('label').first().text().trim().toLowerCase();
                var $input = $(this).find('input[type="text"], textarea').first();
                if (!$input.length) return;
                var val = $input.val();

                if (label === 'title') row.title = val;
                else if (label === 'desc') row.desc = val;
                else if (label.indexOf('title link more') >= 0) row.link_title = val;
                else if (label === 'link more') row.link_more = val;
                else if (label.indexOf('featured text') >= 0) row.featured_text = val;
                else if (label.indexOf('featured icon') >= 0) row.featured_icon = val;
            });
        });
    }

    function flushPendingBlockEdits() {
        var editor = window.editBlockScreen;
        var screen = window.manageBlocksScreen;
        if (!editor || !editor.model) return;

        syncListItemsFromModalDom();

        var merged = JSON.parse(JSON.stringify(editor.model || {}));

        if (editor.item) {
            editor.item.model = merged;
        }

        if (screen && Array.isArray(screen.items)) {
            var idx = typeof editor._editingItemIndex === 'number' ? editor._editingItemIndex : -1;
            if (idx < 0 && editor.item) {
                idx = screen.items.indexOf(editor.item);
            }
            if (idx >= 0 && screen.items[idx]) {
                screen.items[idx].model = merged;
            } else if (editor.block && editor.block.id) {
                screen.items.forEach(function (block) {
                    if (block.type === editor.block.id) {
                        block.model = merged;
                    }
                });
            }
        }
    }

    function patchListItemExpand() {
        if (window._listItemExpandPatched) return;
        $(document).on('click', '.bravo-template-list-item .list-item-header > span > span:first-child', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var $item = $(this).closest('.list-item');
            var $settings = $item.find('.list-item-settings').first();
            var willShow = !$settings.is(':visible');
            $settings.toggle(willShow);
            $item.find('.ion-ios-arrow-dropdown').toggle(willShow);
            $item.find('.ion-ios-arrow-dropright').toggle(!willShow);
        });
        window._listItemExpandPatched = true;
    }

    function showTemplateMessage(screen, message, isSuccess) {
        if (!screen) return;
        screen.message.content = message;
        screen.message.type = !!isSuccess;
    }

    window.__saveHomeTemplate = function () {
        var screen = window.manageBlocksScreen;
        if (!screen) {
            alert('{{ __("Template editor is not ready. Please refresh the page.") }}');
            return;
        }
        if (!screen.title) {
            showTemplateMessage(screen, '{{ __("Template name is required.") }}', false);
            return;
        }

        flushPendingBlockEdits();
        syncSliderLinksInItems(screen.items);

        screen.onSaving = true;
        $('#btn-save-template-spinner').show();

        $.ajax({
            url: storeUrl,
            dataType: 'json',
            type: 'post',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: {
                _token: csrfToken,
                id: template_id,
                content: JSON.stringify(screen.items),
                title: screen.title,
                lang: current_menu_lang
            },
            success: function (res) {
                screen.onSaving = false;
                $('#btn-save-template-spinner').hide();
                if (res.message) {
                    showTemplateMessage(screen, res.message, !!res.status);
                }
                if (res.url) {
                    window.location.href = res.url;
                } else if (res.status) {
                    window.location.reload();
                }
            },
            error: function (e) {
                screen.onSaving = false;
                $('#btn-save-template-spinner').hide();
                var msg = '{{ __("Can not save template") }}';
                if (e.responseJSON && e.responseJSON.message) {
                    msg = e.responseJSON.message;
                } else if (e.status === 403) {
                    msg = '{{ __("Permission denied. Log in as administrator (admin@travelkey.ai).") }}';
                } else if (e.status === 419) {
                    msg = '{{ __("Session expired. Refresh this page and try again.") }}';
                } else if (e.status === 422 && e.responseJSON && e.responseJSON.errors) {
                    msg = Object.values(e.responseJSON.errors).flat().join(' ');
                }
                showTemplateMessage(screen, msg, false);
            }
        });
    };

    function bindSaveButton() {
        $('#btn-save-home-template').off('click.templateSave').on('click.templateSave', function (e) {
            e.preventDefault();
            window.__saveHomeTemplate();
        });
    }

    function patchModalEditor() {
        if (!window.editBlockScreen || window.editBlockScreen._modalEditorPatched) return;
        var editor = window.editBlockScreen;

        var originalOpenEdit = editor.openEdit.bind(editor);
        editor.openEdit = function (item, block) {
            originalOpenEdit(item, block);
            if (window.manageBlocksScreen && Array.isArray(window.manageBlocksScreen.items)) {
                this._editingItemIndex = window.manageBlocksScreen.items.indexOf(item);
            } else {
                this._editingItemIndex = -1;
            }
        };

        var originalSaveModal = editor.saveModal.bind(editor);
        editor.saveModal = function () {
            syncListItemsFromModalDom();
            originalSaveModal();
        };

        editor._modalEditorPatched = true;
    }

    patchListItemExpand();
    bindSaveButton();

    var tries = 0;
    var timer = setInterval(function () {
        bindSaveButton();
        patchListItemExpand();
        patchModalEditor();
        if (++tries > 50) {
            clearInterval(timer);
        }
    }, 200);
})();
</script>
@endpush
@push('css')
    <script>
        var template_i18n = {
            cancel: '{{__('Cancel')}}',
            save_changes: '{{__('Save changes')}}',
            delete_confirm: '{{__('Are you want to delete?')}}',
            add_new: '{{__('Add New')}}',
        };
    </script>
@endpush
