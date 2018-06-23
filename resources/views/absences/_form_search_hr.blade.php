<div id="demo" class="collapse" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <input id="number_record_per_page" type="hidden" name="number_record_per_page"
               value="{{ isset($param['number_record_per_page'])?$param['number_record_per_page']:config('settings.paginate') }}"/>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{  trans('common.title_form.form_search') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <div class="input-group margin">
                            <div class="input-group-btn">
                                <button type="button"
                                        class="btn width-100">{{trans('employee.profile_info.id')}}</button>
                            </div>
                            {{ Form::text('id', request()->get('id'),
                                ['class' => 'form-control',
                                'id' => 'employeeId',
                                'autofocus' => false,
                                ])
                            }}
                        </div>
                        <div class="input-group margin">
                            <div class="input-group-btn">
                                <button type="button"
                                        class="btn width-100">{{trans('employee.profile_info.long_name')}}</button>
                            </div>
                            {{--<input type="text" name="name" id="nameEmployee" class="form-control">--}}
                            {{ Form::text('name', request()->get('name'),
                                ['class' => 'form-control',
                                'id' => 'nameEmployee',
                                'autofocus' => false,
                                ])
                            }}
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <div class="input-group margin">
                            <div class="input-group-btn">
                                <button type="button"
                                        class="btn width-100">{{trans('employee.profile_info.email')}}</button>
                            </div>
                            {{--<input type="text" name="email" id="emailEmployee" class="form-control">--}}
                            {{ Form::text('email', request()->get('email'),
                                ['class' => 'form-control',
                                'id' => 'emailEmployee',
                                'autofocus' => false,
                                ])
                            }}
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer center">
                <button id="btn_reset_form_search_hr" type="button" class="btn btn-default"><span
                            class="fa fa-refresh"></span>
                    {{trans('common.button.reset')}}
                </button>
                <button type="submit" id="btn_form_search_hr" class="btn btn-primary"><span
                            class="fa fa-search"></span>
                    {{trans('common.button.search')}}
                </button>
            </div>
        </div>

    </div>
</div>