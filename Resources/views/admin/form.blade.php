@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="active">
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('admin.dashboard.header') }}</a>
        </li>
        <li>
            <a href="{{ route('admin.articles.index') }}">{{ trans('articles::admin.header') }}</a>
        </li>
        <li class="active">
            {{ $header }}
        </li>
    </ol>
@stop

<div class="row">
    @if(count($errors))
        <div class="col-lg-12">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-ban"></i></span></button>
                <span>Unable to save.</span>
            </div>
        </div>
    @endif

    @if(isset($model))
    {!! Form::model($model, [
        'class' => 'form-default', 
        'method' => 'PUT', 
        'files' => true, 
        'route' => [
            'admin.articles.item.update', $model->id
        ]
    ]) !!}
    @else
    {!! Form::open([
        'class' => 'form-default', 
        'files' => true, 
        'route' => 'admin.articles.item.store'
    ]) !!}
    @endif
    <div class="col-lg-12">
        <div class="header-group">
            <div>
                <h1 class="page-header">
                @if(isset($model))
                    @if($model->published == 2)
                    <span class="label label-primary header-label">Pending</span>
                    @elseif($model->published == 3)
                    <span class="label label-danger header-label">Expired</span>
                    @endif
                    {{ $model->title }}
                @else
                {{ trans('articles::admin.form.create.alias') }}
                @endif
                </h1>
            </div>
            <div class="pull-right">
                <div class="btn-group">
                    <button class="btn btn-success">{{ trans('articles::admin.form.button.save') }}</button>
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li><a role="form-submit link" data-next="self">{{ trans('articles::admin.form.button.save_and_edit') }}</a></li>
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="{!! route('admin.articles.index') !!}">
                                @if(isset($model))
                                {{ trans('articles::admin.form.button.close') }}
                                @else
                                {{ trans('articles::admin.form.button.cancel') }}
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-8">
        <div class="module tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#content" aria-controls="content" role="tab" data-toggle="tab">Content</a></li>
            </ul>

            <div class="tab-content">
                <!-- Content //-->
                <div role="tabpanel" class="tab-pane active" id="content">
                    <div class="section">
                        <div class="form-group">
                            {!! Form::label('title', trans('articles::admin.form.label.title')) !!}
                            {!! Form::text('title', null, ['class' => 'form-control']) !!}
                            {!! $errors->first('title', '<ul class="text-danger"><li>:message</li></ul>') !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('articles::admin.form.label.description')) !!}
                            {!! Form::textarea('description', null, ['class' => 'form-control', 'role' => 'wysiwyg']) !!}
                            {!! $errors->first('description', '<ul class="text-danger"><li>:message</li></ul>') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="module">
            @if(isset($model))
            <div class="section">
                <div class="form-group">
                    <div class="btn-group stretch">
                        <a role="button" class="btn btn-default" disabled>
                            <i class="glyphicon glyphicon-eye-open"></i>
                            {{ trans('articles::admin.form.label.preview') }}
                        </a>
                        <a role="button" class="btn btn-default" href="{{ route('articles.show', $model->slug) }}" target="_blank">
                            <i class="glyphicon glyphicon-share-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="section">
                <div class="form-group">
                    {!! Form::label('slug', trans('articles::admin.form.label.slug')) !!}
                    {!! Form::text('slug', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('slug', '<ul class="text-danger"><li>:message</li></ul>') !!}
                </div>

                <div class="form-group">
                    {!! Form::label('publish_up', 'Post Date') !!}
                    <div id="dtpicker.1" class="input-group" role="datepicker" data-role-link="dtpicker.2" data-role-type="min">
                        {!! Form::text('publishing[publish_up]', null, ['class' => 'form-control']) !!}
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('publish_down', 'Expiry Date') !!}
                    <div id="dtpicker.2" class="input-group" role="datepicker" data-role-link="dtpicker.1" data-role-type="max">
                        {!! Form::text('publishing[publish_down]', null, ['class' => 'form-control']) !!}
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>

                @if(isset($model))
                <div class="form-group">
                    <label>{{ trans('articles::admin.form.label.created_at') }}</label>
                    <br />
                    <div role="date-display" data-value="{{ $model->created_at }}">{{ $model->created_at }}</div>
                </div>
                @endif

                @if(isset($model->modified_by) and isset($model->modified_by_name))
                <div class="form-group">
                    <label>{{ trans('articles::admin.form.label.modified_by') }}</label>
                    <br />
                    <a href="#" class="btn btn-simple">
                        <i class=" glyphicon glyphicon-user" aria-hidden="true"></i> {{ $model->modified_by_name }}
                    </a>
                    @if($model->modified_by == auth()->user()->id)
                    {{ trans('articles::admin.form.label.current_user') }}
                    @endif
                </div>
                @endif
            </div>

            <div class="section">
                <div class="form-group">
                    {!! Form::label('published', trans('articles::admin.form.label.access')) !!}
                    <div class="switch mini auto">
                        {!! Form::checkbox('publishing[access]', 1,null, ['class' => 'form-control', 'id' => 'publishing_access']) !!}
                        {!! Form::label('publishing_access') !!}
                        <i class="active">Only logged in users can access</i>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('published', trans('articles::admin.form.label.published')) !!}
                    <div class="switch mini auto">
                        {!! Form::checkbox('published', 1,null, ['class' => 'form-control', 'id' => 'published']) !!}
                        {!! Form::label('published') !!}
                        <i class="active">Enabled</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>


@push('jquery-scripts')
    $('form').on('submit', function() {
        App.set('page_exit_confirmation', false);
        return true;
    });
    $('input,select').on('change', function() {
        App.set('page_exit_confirmation', true);
    });
@endpush
