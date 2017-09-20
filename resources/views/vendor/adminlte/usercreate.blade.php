@extends('adminlte::layouts.app')

@section('htmlheader_title')
	{{ trans('adminlte_lang::message.home') }}
@endsection


@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">

				<!-- Default box -->
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Create User</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
								<i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
								<i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">

						<form action="{{ url('/users') }}" method="post">

							<input type="hidden" name="_token" value="{{ csrf_token() }}">

							<div class="form-group has-feedback">
								<input type="text" class="form-control" placeholder="{{ trans('adminlte_lang::message.fullname') }}" name="name"
									   required />
								<span class="glyphicon glyphicon-user form-control-feedback"></span>
							</div>
							<div class="form-group has-feedback">
								<input type="email" class="form-control" placeholder="{{ trans('adminlte_lang::message.email') }}" name="email"
									   required />
								<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
							</div>

							<div class="form-group has-feedback">
								<input type="password" class="form-control" placeholder="{{ trans('adminlte_lang::message.password') }}" name="password"
									   required />
								<span class="glyphicon glyphicon-lock form-control-feedback"></span>
							</div>

							<div class="form-group has-feedback">
								<input type="password" class="form-control" placeholder="{{ trans('adminlte_lang::message.retypepassword') }}" name="password_confirmation"/>
								<span class="glyphicon glyphicon-log-in form-control-feedback"></span>
							</div>

							<div class="form-group has-feedback">
								<select class="form-control select2" name="role"
										data-placeholder="Select a User Role">
									<option></option>
									<?php foreach ($roles as $role): ?>
									<option value="<?php echo  $role->name ?>"><?php echo  $role->name ?></option>
									<?php endforeach; ?>

								</select>
								<span class="glyphicon glyphicon-log-in form-control-feedback"></span>
							</div>

							<div class="form-group has-feedback">
								<select class="form-control select2" name="permission"
										data-placeholder="Select a User Permission">
									<option></option>
                                    <?php foreach ($permissions as $permission): ?>
									<option value="<?php echo  $permission->name ?>"><?php echo  $permission->name ?></option>
                                    <?php endforeach; ?>

								</select>
								<span class="glyphicon glyphicon-log-in form-control-feedback"></span>
							</div>

							<div class="form-group has-feedback">
								<label>
									Activated
									<input type="radio" name="activated" class="minimal" value="1"
										   required/>
								</label>
								<label>
									Not Activated
									<input type="radio" name="activated" class="minimal" value="0"
										   required/>
								</label>
							</div>

							<div class="box-footer">
								<button type="submit" class="btn btn-primary pull-right">Save User</button>
							</div>

						</form>

					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->

			</div>
		</div>
	</div>

@endsection

@section('page_specific_scripts')
	<!-- iCheck 1.0.1 -->
	<script src="{{ asset('/plugins/icheck.min.js') }}" type="text/javascript"></script>
@endsection

@section('page_specific_inline_scripts')
	<script>
        $(document).ready(function() {
            //Initialize Select2 Elements
            $(".select2").select2();
            //initialize icheck box
            $(function () {

                $('input.minimal').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                });
            });
        } );

	</script>
@endsection