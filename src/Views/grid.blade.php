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
					<th data-dg-col="{{ $col->getKey() }}" {!! $col->getAttributesHtml() !!}>
						@if ($col->isSortable())
							<a href="{{ Datagrid::getCurrentRouteLink($grid->getSortParams($col->getKey())) }}">{!! $col->getTitle() !!}<i class="fa @if (\Request::input('f.order_by', '') == $col->getKey() && \Request::input('f.order_dir', 'ASC') == 'ASC') fa-sort-asc @elseif (\Request::input('f.order_by', '') == $col->getKey() && \Request::input('f.order_dir', 'ASC') == 'DESC') fa-sort-desc @else fa-sort @endif"></i></a>
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
						<input type="checkbox" value="1" data-dg-bulk-select="all" />
					</th>
				@endif

				@foreach ($grid->getColumns() as $col)
					@if ($col->isAction() === false)
						@if ($col->hasFilters())
							<th data-dg-col="{{ $col->getKey() }}" {!! $col->getAttributesHtml() !!}>
								@if ( is_array($col->getFilters()) && count($col->getFilters()) > 0 )
									{!!
										Form::select(
											$col->getFilterName(),
											$col->getFilters(true),
											$grid->getFilter($col->getKey()),
											($col->hasFilterMany() ? ['multiple' => 'multiple'] : []) + array('class' => 'form-control input-sm', 'data-dg-type' => "filter")
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
							<button class="btn btn-success btn-sm" type="submit" title="Search..."><i class="fa fa-search" aria-hidden="true"></i></button>
							<a href="{{ \Request::url() }}" class="btn btn-danger btn-sm" title="Clear filters"><i class="fa fa-remove" aria-hidden="true"></i></a>
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
					<td data-dg-col="{{ $col->getKey() }}" {!! $col->getAttributesHtml() !!}>
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
