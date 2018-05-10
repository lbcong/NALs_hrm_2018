@extends('admin.template')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Add team
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="/team">Teams</a></li>
                <li class="active">Add team</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- SELECT2 EXAMPLE -->
            <div class="box box-default">
                <div class="box-body">
                    {!! Form::open(
                        ['url' =>route('teams.store'),
                        'method'=>'Post',
                        'id'=>'form_add_team'
                    ]) !!}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-md-3">
                        </div>
                        <!-- /.col -->
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Team name</label>
                            {{ Form::text('team_name', old('team_name'),
                              ['class' => 'form-control width80',
                              'id' => 'team_name_id',
                              'autofocus' => true,
                              'placeholder'=>'Team name'
                              ])
                          }}
                            <!-- /.input group -->
                                <label style="color: red; ">{{$errors->first('team_name')}}</label>
                            </div>
                            <div class="form-group">
                                <label>PO name</label><br/>
                                <select class="form-control select2 width80" name="id_po" onchange="choosePO()"
                                        id="id_po">
                                    <option {{ !empty(old('id_po'))?'':'selected="selected"' }} value="0" id="po_0">
                                        {{  trans('employee.drop_box.placeholder-default') }}
                                    </option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                                {{ (string)$employee->id===old('id_po')?'selected="selected"':'' }}
                                                id="po_{{$employee->id}}">
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label style="color: red; ">{{$errors->first('id_po')}}</label>
                            </div>
                            <div class="form-group">
                                <label>Member</label><br/>
                                <select class="form-control select2 width80" name="" id="member">
                                    <option {{ !empty(old('members'))?'':'selected="selected"' }} value=""
                                            id="member_0">
                                        {{  trans('employee.drop_box.placeholder-default') }}
                                    </option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" id="member_{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                {{--<input type="hidden" name="members[]" value="42"/>--}}
                                {{--<input type="hidden" name="members[]" value="42"/>--}}
                                <button type="button" class="btn btn-default buttonAdd">
                                    <a onclick="addFunction()"><i
                                                class="fa fa-user-plus"></i> {{ trans('common.button.add')}}</a>
                                </button>
                                <label style="color: red; ">{{$errors->first('members')}}</label>
                            </div>
                            <div class="form-group" id="listChoose" style="display: none;">
                            </div>
                            <div class="form-group">
                                <ul class="contextMenuTeam" id="contextMenuTeam">

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px; padding-bottom: 20px; ">
                        <div class="col-md-6" style="display: inline; ">
                            <div style="float: right;">
                                <input id="btn_reset_form_team" type="button" value="{{ trans('common.button.reset')}}"
                                       class="btn btn-info pull-left">
                            </div>
                        </div>
                        <div class="col-md-1" style="display: inline;">
                            <div style="float: right;">
                                <button type="submit"
                                        class="btn btn-info pull-left">{{trans('common.button.save')}}</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <script type="text/javascript"
                            src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
                    <script type="text/javascript">
                        $listEmployeeID = new Array();
                        $listEmployeeName = new Array();
                        $listEmployeeTeam = new Array();
                        $listEmployeeRole = new Array();
                        $idPO = document.getElementById("id_po").value;
                    </script>
                    <script>
                        $(function () {
                            $("#btn_reset_form_team").bind("click", function () {
                              var select_po = $('#id_po');
                              select_po.val('').change();
                              var select_members = $("#member");
                              select_members.val('').change();
                              $("#team_name").val('');

                              for ($i = 0; $i < $listEmployeeID.length; $i++) {
                                  $('#member_'+$listEmployeeID[$i]).prop('disabled', false);
                                  $('#member').select2();

                                  $('#po_'+$listEmployeeID[$i]).prop('disabled', false);
                                  $('#id_po').select2();
                              }

                              $listEmployeeID = new Array();
                              $listEmployeeName = new Array();
                              $listEmployeeTeam = new Array();
                              $listEmployeeRole = new Array();
                              document.getElementById("contextMenuTeam").innerHTML = "";
                              document.getElementById("listChoose").innerHTML = "";
                            });
                        });
                    </script>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            var members = {!! json_encode(old('members')) !!} ;
                            if (null != members) {
                                listAdd = "";
                                for (i = 0; i < members.length; i++) {
                                    @foreach($employees as $employee)
                                      if({{$employee->id}} == members[i]){
                                        $teamEdit = '{{isset($employee->team)?$employee->team->name:'---' }}';
                                        $roleEdit = '{{isset($employee->role)?$employee->role->name:'---' }}';
                                      }
                                    @endforeach
                                    listAdd += "<li  id=\"show_" + members[i] + "\"><a class=\"btn-employee-remove\"><i class=\"fa fa-remove\"  onclick=\"removeEmployee(" + members[i] + ",\'" + $("#member_" + members[i]).text() + "\')\"></i><label>ID:" + members[i] + "</label>&emsp;<label id=\"teamEdit_"+members[i]+"\">"+$teamEdit+"</label>&emsp;<label id=\"roleEdit_"+members[i]+"\">"+$roleEdit+ "</label>&emsp;<label id=\"nameEdit_"+members[i]+"\">"+$("#member_" + members[i]).text() + "</label></a></li>";
                                }
                                listChoose="";
                                for (i = 0; i < members.length; i++) {
                                    listChoose +=   "<input type=\"text\" name=\"members[]\" id=\"employee\" value=\"" + members[i] + "\" class=\"form-control width80 input_" + members[i] + "\">";
                                }
                                document.getElementById("contextMenuTeam").innerHTML = listAdd;
                                document.getElementById("listChoose").innerHTML = listChoose;
                            }
                        });
                    </script>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            var members = {!! json_encode(old('members')) !!} ;
                            if(members != null){
                              if (null != members) {
                                for (i = 0; i < members.length; i++) {                                    
                                  $('#member_'+members[i]).prop('disabled', true);
                                  $('#member').select2();

                                  $('#po_'+members[i]).prop('disabled', true);
                                  $('#id_po').select2();

                                  $listEmployeeID[i] = members[i];
                                  $listEmployeeName[i] = $("#nameEdit_" + members[i]).text();
                                  $listEmployeeTeam[i] = $("#teamEdit_" + members[i]).text();
                                  $listEmployeeRole[i] = $("#roleEdit_" + members[i]).text();
                                }
                                $idSave = document.getElementById("id_po").value;
                                $('#member_'+$idSave).prop('disabled', true);
                                $('#member').select2();
                              }
                            }
                        });
                    </script>
                    <script type="text/javascript">
                        function addFunction() {
                            $id = document.getElementById("member").value;
                            if ($id != 0) {
                              $listEmployeeID[$listEmployeeID.length] = document.getElementById("member").value;
                              $listEmployeeName[$listEmployeeName.length] = $("#member_" + $id).text();
                              @foreach($employees as $employee)
                                if({{$employee->id}} == $listEmployeeID[$listEmployeeID.length - 1]){
                                  $listEmployeeTeam[$listEmployeeTeam.length] = '{{isset($employee->team)?$employee->team->name:'---' }}';
                                  $listEmployeeRole[$listEmployeeRole.length] = '{{isset($employee->role)?$employee->role->name:'---' }}';
                                }
                              @endforeach
                              $listAdd = "";

                              for ($i = 0; $i < $listEmployeeID.length; $i++) {
                                  $listAdd += "<li  id=\"show_" + $listEmployeeID[$i] + "\"><a class=\"btn-employee-remove\"><i class=\"fa fa-remove\"  onclick=\"removeEmployee(" + $listEmployeeID[$i] + ",\'" + $listEmployeeName[$i] + "\')\"></i><label>ID:" + $listEmployeeID[$i]+"&emsp;" + "</label><label>"+$listEmployeeTeam[$i]+"&emsp;"+$listEmployeeRole[$i]+"&emsp;" + $listEmployeeName[$i]+ "</label></a></li>";
                              }
                              $listChoose = "";
                              for ($i = 0; $i < $listEmployeeID.length; $i++) {
                                  $listChoose += "<input type=\"text\" name=\"members[]\" id=\"employee\" value=\"" + $listEmployeeID[$i] + "\" class=\"form-control width80 input_" + $listEmployeeID[$i] + "\">";
                              }
                              document.getElementById("contextMenuTeam").innerHTML = $listAdd;
                              document.getElementById("listChoose").innerHTML = $listChoose;
                              $('#member_'+$id).prop('disabled', true);
                              $('#member').select2();

                              $('#po_'+$id).prop('disabled', true);
                              $('#id_po').select2();
                              
                            }
                        }
                    </script>
                    <script type="text/javascript">
                        function removeEmployee($id, $name) {
                          $('li').remove('#show_' + $id);
                          $('input').remove('.input_' + $id);
                          $listEmployeeID.splice($listEmployeeID.indexOf("" + $id), 1);
                          $listEmployeeName.splice($listEmployeeName.indexOf($name), 1);

                          $('#member_'+$id).prop('disabled', false);
                          $('#member').select2();

                          $('#po_'+$id).prop('disabled', false);
                          $('#id_po').select2();

                        }
                    </script>
                    <script type="text/javascript">
                        function choosePO() {
                          if ($idPO != 0) {
                            $('#member_'+$idPO).prop('disabled', false);
                            $('#member').select2();
                          }
                          $idPO = document.getElementById("id_po").value;
                          $('#member_'+$idPO).prop('disabled', true);
                          $('#member').select2();
                        }
                    </script>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <!-- /.content -->
    </div>
    <style type="text/css">
        .width80 {
            width: 80%;
        }

        .buttonAdd {
            margin-left: 10px;
        }
    </style>
    <style type="text/css">
        ul.contextMenuTeam * {
            transition: color .4s, background .4s;
        }

        ul.contextMenuTeam li {
            min-width: 100px;
            max-width: 500px;
            overflow: hidden;
            white-space: nowrap;
            margin-left: 0px;
            padding: 6px 6px;
            background-color: #fff;
            border-bottom: 1px solid #ecf0f1;
        }

        ul.contextMenuTeam li a {
            color: #333;
            text-decoration: none;
        }

        ul.contextMenuTeam li:hover {
            background-color: #ecf0f1;
        }

        ul.contextMenuTeam li:first-child {
            border-radius: 5px 5px 0 0;
        }

        ul.contextMenuTeam li:last-child {
            border-bottom: 0;
            border-radius: 0 0 5px 5px
        }

        ul.contextMenuTeam li:last-child a {
            width: 26%;
        }

        ul.contextMenuTeam li:last-child:hover a {
            color: #2c3e50
        }

        ul.contextMenuTeam li:last-child:hover a:hover {
            color: #2980b9
        }
    </style>
@endsection