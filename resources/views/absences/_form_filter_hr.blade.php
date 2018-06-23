    <div class="form-group col-md-4">
        <label>{{trans('common.month.month')}}</label>
        <select name="month_absence" id="month-absence" class="form-control">
            <option {{ !empty(request()->get('month_absence'))?'':'selected="selected"' }} value="">
                {{  trans('vendor.drop_box.placeholder-default') }}
            </option>
            @foreach($month_absences as $key=>$value)
                <option value="{{ $key }}" {{ (string)$key===request()->get('month_absence')?'selected="selected"':'' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        <label id="lb_error_filter" style="color: red; ">{{$errors->first('month_absence')}}</label>
    </div>
    <div class="form-group col-md-4">
        <label>{{trans('common.year.year')}}</label>
        <select name="year_absence" id="year-absence" class="form-control">
            <option {{ !empty(request()->get('year_absence'))?'':'selected="selected"' }} value="">
                {{  trans('vendor.drop_box.placeholder-default') }}
            </option>
            @foreach($year_absences as $key=>$value)
                <option value="{{ $key }}" {{ (string)$key===request()->get('year_absence')?'selected="selected"':'' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-2">
        <br>
        <button style="margin-top: 5px !important" type="submit" id="btn_filter_hr"
                class="form-control btn btn-primary ">
            <i class="glyphicon glyphicon-filter"></i> {{trans('common.button.filter')}}
        </button>
    </div>