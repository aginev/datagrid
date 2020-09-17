<div class="dg-wrapper" {!! $id ? 'id="'. $id .'"' : null !!}>

	@if ($grid->hasFilters())
		<form action="" role="form" method="get">

		<input type="hidden" name="f[order_by]" value="{{ $grid->getFilter('order_by', '') }}" />
		<input type="hidden" name="f[order_dir]" value="{{ $grid->getFilter('order_dir', '') }}" />

		@foreach ($grid->getHiddens() as $name => $value)
			<input type="hidden" name="{{ $name }}" value="{{ $value }}" />
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
							<a
								href="{{ \Aginev\Datagrid\Datagrid::getCurrentRouteLink($grid->getSortParams($col->getKey())) }}"
								title="{{ __('Order by :title', ['title' => $col->getTitle()]) }}"
							>
								{!! $col->getTitle() !!}

								@if (request()->input('f.order_by', '') == $col->getKey() && request()->input('f.order_dir', 'ASC') == 'ASC')
									{!! config('datagrid.icons.asc') !!}
								@elseif (request()->input('f.order_by', '') == $col->getKey() && request()->input('f.order_dir', 'ASC') == 'DESC')
									{!! config('datagrid.icons.desc') !!}
								@else
									{!! config('datagrid.icons.sort', '') !!}
								@endif
							</a>
						@else
							{!! $col->getTitle() !!}
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
									<select
										name="{{ $col->getFilterName() }}"
										class="form-control input-sm"
										data-dg-type="filter"
										{{ $col->hasFilterMany() ? 'multiple' : null }}
									>
										@foreach($col->getFilters(true) as $value => $title)
											<option
												value="{{ $value }}"
												{{ $grid->getFilter($col->getKey()) == $value ? 'selected' : null }}
											>{{ $title }}</option>
										@endforeach
									</select>
								@else
									<input
										type="text"
										name="{{ $col->getFilterName() }}"
										value="{{ $grid->getFilter($col->getKey()) }}"
										class="form-control input-sm"
										data-dg-type="filter"
										placeholder="{{ $col->getTitle() }}"
									>
								@endif
							</th>
						@else
							<th data-dg-col="{{ $col->getKey() }}">&nbsp;</th>
						@endif
					@else
						<th data-dg-col="actions">
							<button
								class="btn btn-success btn-sm"
								type="submit"
								title="{{ __('Search') }}"
							>{!! config('datagrid.icons.search') !!}</button>
							<a href="{{ request()->url() }}"
								 class="btn btn-danger btn-sm"
								 title="{{ __('Clear') }}"
							>{!! config('datagrid.icons.clear') !!}</a>
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
					<th data-dg-col="bulks">
						<input
							type="checkbox"
							value="{{ $row->{$grid->getBulk()} }}"
							data-dg-bulk-select="row"
						/>
					</th>
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
		</form>
	@endif

	@if ($grid->hasPagination())
		<div class="row-fluid text-center">
			{!! $grid->getPagination()->render() !!}
		</div>
	@endif

</div><!-- /.dg-wrapper -->
