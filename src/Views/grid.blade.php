<div class="dg-wrapper" {!! $id ? 'id="'. $id .'"' : null !!}>

	@if ($grid->hasFilters())
		{!! Form::open(['role' => 'form', 'method' => 'GET']) !!}

		{!! Form::hidden('f[order_by]', $grid->getFilter('order_by', '')); !!}
		{!! Form::hidden('f[order_dir]', $grid->getFilter('order_dir', 'ASC')); !!}

		@foreach ($grid->getHiddens() as $name => $value)
			{!! Form::hidden($name, $value); !!}
		@endforeach
	@endif

	<table data-dg-type="table" class="table table-striped table-hover table-bordered">
		<thead>
		<!-- Titles -->
		<tr data-dg-type="titles">
			@if ($grid->isItBulkable())
				<th data-dg-col="bulks">@if ( ! $grid->hasFilters() )<input type="checkbox" />@endif</th>
			@endif

			@foreach ($grid->getColumns() as $col)
				@if ($col->isAction() === false)
					<th data-dg-col="{{ $col->getKey() }}">
						@if ($col->isSortable())
							<a href="{{ url(\Route::getCurrentRoute()->getUri()) }}?{{ http_build_query($grid->getSortParams($col->getKey())) }}">{!! $col->getTitle() !!}<i class="glyphicon @if (\Input::get('f.order_by', '') == $col->getKey() && \Input::get('f.order_dir', 'ASC') == 'ASC') glyphicon-sort-by-attributes-alt @elseif (\Input::get('f.order_by', '') == $col->getKey() && \Input::get('f.order_dir', 'ASC') == 'DESC') glyphicon-sort-by-attributes @else glyphicon-sort @endif"></i></a>
						@else
							{{ $col->getTitle() }}
						@endif
					</th>
				@else
					<th data-dg-col="actions"><!-- Actions --></th>
				@endif
			@endforeach
		</tr>
		<!-- END Titles -->

		@if ($grid->hasFilters())
			<!-- Filters -->
			<tr data-dg-type="filters-row">
				@if ($grid->isItBulkable())
					<th data-dg-col="bulks">
						<input type="checkbox" value="1" class="form-control input-sm" data-dg-bulk-select="all" />
					</th>
				@endif

				@foreach ($grid->getColumns() as $col)
					@if ($col->isAction() === false)
						@if ($col->hasFilters())
							<th data-dg-col="{{ $col->getKey() }}">
								@if ( is_array($col->getFilters()) && count($col->getFilters()) > 0 )
									{!!
										Form::select(
											$col->getFilterName(),
											$col->getFilters(true),
											$grid->getFilter($col->getKey()),
											($col->hasFilterMany() ? ['multiple' => 'multiple'] : []) + array('class' => 'form-control input-sm', 'data-dg-type' => "filter", 'placeholder' => $col->getTitle())
										)
									!!}
								@else
									{!!
										Form::text(
											$col->getFilterName(),
											$grid->getFilter($col->getKey()),
											array('class' => 'form-control input-sm', 'data-dg-type' => "filter", 'placeholder' => $col->getTitle())
										)
									!!}
								@endif
							</th>
						@else
							<th data-dg-col="{{ $col->getKey() }}">&nbsp;</th>
						@endif
					@else
						<th data-dg-col="actions">
							<button class="btn btn-success btn-sm" type="submit" title="Search..."><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
							<a href="{{ \Request::url() }}" class="btn btn-danger btn-sm" title="Clear filters"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
						</th>
					@endif
				@endforeach
			</tr>
			<!-- END Filters -->
		@endif
		</thead>

		<tbody>
		@forelse($grid->getRows() as $row)
			<tr data-dg-type="data-row">
				@if ($grid->isItBulkable())
					<th data-dg-col="bulks"><input type="checkbox" value="{{ $row->{$grid->getBulk()} }}" data-dg-bulk-select="row" /></th>
				@endif

				@foreach ($grid->getColumns() as $col)
					<td data-dg-col="{{ $col->getKey() }}">
						@if ($col->hasWrapper())
							{!! $col->wrapper($row->{$col->getKey(true)}, $row) !!}
						@else
							{!! $row->{$col->getKey(true)} !!}
						@endif
					</td>
				@endforeach
			</tr>
		@empty
			<tr data-dg-type="empty-result">
				<td colspan="{{ $grid->getColumnsCount() }}">
					No results found!
				</td>
			</tr>
		@endforelse
		</tbody>
	</table>

	@if ($grid->hasFilters())
		{!! Form::close() !!}
	@endif

	@if ($grid->hasPagination())
		<div class="row-fluid text-center">
			{!! $grid->getPagination()->render() !!}
		</div>
	@endif

</div><!-- /.dg-wrapper -->