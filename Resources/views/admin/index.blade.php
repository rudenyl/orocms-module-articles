@extends('admin::layouts.master')
@section('title'){{ trans('articles::admin.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('admin.menu.dashboard') }}</a>
        </li>
        <li class="active">
            {{ trans('articles::admin.header') }}
        </li>
    </ol>
@stop

@push('header')
<script src="{{ asset( theme('admin.assets', 'js/bootstrap-group-select/bootstrap-group-select.min.js') ) }}"></script>
@endpush

@section('content')
    <div class="header-group">
        <div>
            <h1 class="page-header">
                {{ trans('articles::admin.header') }}
            </h1>
        </div>
        <div class="pull-right">
            <a href="{{ route('admin.articles.item.create') }}" class="btn btn-success">
                <i class="glyphicon glyphicon-plus"></i>
                Create new article...
            </a>
        </div>
    </div>


    <div id="content-wrapper" class="list-container module section">
        <div id="toolbar">
            <div class="btn-group">
                <div id="list:type"
                    data-id="visibility"
                    data-toggle="group-select"
                    data-type="dropdown"
                    data-allow-cookie="true"
                    data-bt-role="filters"
                    data-primary-class="btn btn-default">

                    <ul role="group-select-data" class="dropdown-menu">
                        <li class="active">Display All</li>
                        <li role="separator"></li>
                        <li data-filters-published="1"><i class="bullet-status status-success"></i> {{ trans('articles::admin.list.dropdown.live') }}</li>
                        <li data-filters-published="2"><i class="bullet-status status-primary"></i> {{ trans('articles::admin.list.dropdown.pending') }}</li>
                        <li data-filters-published="3"><i class="bullet-status status-danger"></i> {{ trans('articles::admin.list.dropdown.expired') }}</li>
                        <li role="separator"></li>
                        <li data-filters-published="0"><i class="bullet-status"></i> {{ trans('articles::admin.list.dropdown.disabled') }}</li>
                        <li role="separator"></li>
                        <li data-filters-deleted_only="1"><i class="bullet-status status-inactive"></i> {{ trans('articles::admin.list.dropdown.deleted') }}</li>
                    </ul>
                </div>

                <div class="btn-group">
                    <button type="button" 
                        class="btn btn-default dropdown-toggle" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                        data-bt-role="toggle"
                        disabled>
                        <i class="fa-fw glyphicon glyphicon-cog"></i><span class="caret"></span>
                    </button>

                    <ul class="dropdown-menu" data-toggle="group-select-link" data-link="visibility">
                        <li data-value="display all|live|pending|expired|un-published"><a href="#"
                            data-bt-action="remove"
                            data-url="{!! route('admin.articles.item.destroy') !!}"
                            data-method="DELETE">
                            {{ trans('articles::admin.list.dropdown.delete_all') }}</a></li>
                        <li data-value="display all|live|pending|expired|un-published" 
                            role="separator" class="divider"></li>
                        <li data-value="display all|live|pending|expired|un-published"><a href="#"
                            role="ajax bt-action" 
                            data-url="{!! route('admin.articles.item.patch', csrf_token()) !!}"
                            data-params-published="1"
                            data-method="PATCH">
                            Set Active</a></li>
                        <li data-value="display all|live|pending|expired|un-published"><a href="#"
                            role="ajax bt-action" 
                            data-url="{!! route('admin.articles.item.patch') !!}"
                            data-params-published="0"
                            data-method="PATCH">
                            Un-publish</a></li>

                        <li data-value="deleted">
                            <a href="#"
                                role="ajax bt-action" 
                                data-url="{!! route('admin.articles.item.patch') !!}"
                                data-params-restore="1"
                                data-method="PATCH">
                                {{ trans('articles::admin.list.dropdown.restore') }}</a>
                        </li>
                        <li data-value="deleted" role="separator" class="divider"></li>
                        <li data-value="deleted">
                            <a href="#"
                                role="confirm bt-action"
                                data-params-force_delete="1"
                                data-title="{{ trans('articles::admin.confirm.force_delete.title') }}" 
                                data-message="{{ trans('articles::admin.confirm.force_delete.message') }}"
                                data-url="{!! route('admin.articles.item.destroy') !!}"
                                data-method="DELETE">
                                {{ trans('articles::admin.list.dropdown.purge') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <table id="table-articles"
                class="table table-no-bordered break-word extended"
                
                data-sort-order="desc"
                data-url="{!! route('admin.articles.index') !!}"
                data-data-field="data"
                data-side-pagination="server"
                data-pagination="true"
                data-columns-hidden="['publish_up', 'publish_down']"
                data-page-list="[10, 20, 50, 100]"

                data-toolbar="#toolbar">
            <thead>
            <tr>
                <th data-field="state" data-checkbox="true"></th>
                <th data-field="title" data-sortable="true" data-switchable="false" data-uri="/admin/articles/item/{id}/edit" data-formatter="Formatter.title">
                    {{ trans('articles::admin.list.header.title') }}
                </th>
                <th data-field="slug" data-visible="false" data-formatter="Formatter.slug" data-sortable="true">
                    {{ trans('articles::admin.list.header.slug') }}
                </th>
                <th data-field="created_at" data-align="center" data-visible="false" data-formatter="Formatter.date" data-sortable="true">
                    {{ trans('articles::admin.list.header.created_at') }}
                </th>
                <th data-field="publish_up" data-align="center" data-formatter="Formatter.date" data-sortable="true">
                    {{ trans('articles::admin.list.header.post_date') }}
                </th>
                <th data-field="publish_down" data-align="center" data-formatter="Formatter.date" data-sortable="true">
                    {{ trans('articles::admin.list.header.expiry_date') }}
                </th>
                <th data-field="domain" data-visible="false" data-switchable="false" data-sortable="true">
                    {{ trans('articles::admin.list.header.domain') }}
                </th>
                <th data-field="published" data-align="center" data-card-view-only="true" data-formatter="Formatter.get_status">
                    {{ trans('articles::admin.list.header.published') }}
                </th>
            </tr>
            </thead>
        </table>
    </div>

    <script>
    var Formatter = Formatter || {
        // title
        title: function(value, row) {
            var statuses = {
                    1: 'success',
                    2: 'primary',
                    3: 'danger'
                },
                text = BT.formatter.linkable(value, row, this.uri);

            var status_type = statuses[row.published || 0];
            if (row.deleted_at) {
                status_type = 'inactive';
                text = '<a class="link" role="link" data-toggle="popover" data-trigger="click">{value}</a>'.replace(/{value}/, value);
            }

            if (status_type != undefined) 
                text = ['<span class="bullet-status status-{type}"></span>'
                    .replace(/{type}/, status_type), text].join('');

            return text;
        },
        // slug
        slug: function(value, row) {
            if (row.published == 1) 
                return '<a class="link" href="/articles/{slug}" target="_blank">{slug} <i class="glyphicon glyphicon-share-alt"></i></a>'
                    .replace(/{slug}/g, value);

            return value;
        },
        // format publishing
        get_status: function(value, row) {
            var statuses = {
                0: {
                    class: 'inactive',
                    label: '{{ trans('articles::admin.list.status.disabled') }}'
                },
                1: {
                    class: 'success',
                    label: '{{ trans('articles::admin.list.status.live') }}'
                },
                2: {
                    class: 'primary',
                    label: '{{ trans('articles::admin.list.status.pending') }}'
                },
                3: {
                    class: 'danger',
                    label: '{{ trans('articles::admin.list.status.expired') }}'
                },
            };

            var status = statuses[value];
            if (status == undefined) return '?';

            return '<span class="label label-{type}">{title}</span>'
                .replace(/{type}/, status.class)
                .replace(/{title}/, status.label);
        },
        date: function(value, row) {
            var d = moment(value),
                now = new Date(),
                is_today = d.isSame(now, "day");

            if (d.isValid() === false) return '';
            switch (this.field) {
                case 'publish_down':
                    return is_today ? d.fromNow() : d.format('D/M/YYYY');
                default:
                    return is_today ? d.calendar(now) : d.fromNow();
            }
        },
    };
    </script>
@stop

@push('jquery-scripts')
    // load bootstrap table
    BT.init('#table-articles', {
        notification: {
            target: $('#content-wrapper')
        }
    }, function(table) {
        // add popover render callback
        BT.field_callbacks.add('title', 'popover', function(row) {
            var tmpl = '<span class="popup-view" data-pos="relative"><p style="color:red;">This item has been marked as deleted. To <strong>undelete</strong> this record, click the <strong>Restore</strong> button.</p><i>Deleted At:</i><b>{deleted_at}</b><button class="edit btn btn-sm btn-primary" role="ajax" data-url="/admin/modules/articles/item/{id}" data-params-restore="1" data-method="PATCH">Restore</button>&nbsp;<button role="confirm" data-url="/admin/modules/articles/item/{id}" data-title="{{ trans('articles::admin.confirm.force_delete.title') }}" data-method="DELETE" data-message="{{ trans('articles::admin.confirm.force_delete.message') }}" data-params-force_delete="1" class="btn btn-sm btn-default">Remove Permanently</button></a></span>';
            // parse
            for (var k in row) tmpl = tmpl.replace(new RegExp('{'+k+'}', 'g'), row[k]);
            return tmpl;
        });

        //
        // event listener
        //
        $('#content-wrapper')
            // popover
            .on('click', '.popover button, [role*="bt-action"]', function() {
                var el = $(this);
                $('.alert').remove(), $('.popover').popover('hide');

                // events
                el.off('railed.beforeSend railed.onError railed.onComplete')
                .on('railed.beforeSend', function(r,s) {
                    Preloader.create('.list-container', 'centered', false,true);
                })
                .on('railed.onComplete', function(e, result) {
                    Preloader.clear(function() {
                        // check response
                        var success = result.success || result.status,
                            message = result.statusText || (result.message || 'No message returned.');

                        BT.notify(message, BT.options.notification.target, success===true?'info':'warning', 'insertBefore', success===true);
                        BT.call_method('refresh');
                    });
                });
            });

        // update data pagination
        this.option('sidePagination', 'server');
    });

    // list option
    $('[id="list:type"]')
        .on('select.bs.group-select', function(e, value) {
            var gs = $(this).data('bs.groupSelect'),
                filters = gs.options.filters || {};

            // update data
            var filter = $.extend(filters[value], {
                page: 1 // reset
            });
            $(this).data('filters', filter);

            BT.call_method('refresh', {query: filter});
        });
@endpush
