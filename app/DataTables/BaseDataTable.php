<?php

namespace App\DataTables;

use App\Interfaces\WithButton;
use App\Interfaces\WithOptions;
use Carbon\Carbon;
use Illuminate\View\View;
use Yajra\DataTables\Services\DataTable;
use App\Interfaces\WithColumn;

/**
 * Class BaseDataTable
 * @author sheenazien8
 */
abstract class BaseDataTable extends DataTable implements WithColumn
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatbale = datatables()
            ->eloquent($query)
            ->editColumn('created_at', function ($value)
            {
                $date = (new Carbon($value->created_at))->diffForHumans();

                return $date;
            })
            ->addColumn('checkbox', function ($model) {
                return view('partials.table.checkbox', compact('model'));
            });

        if ($this instanceof WithOptions) {
            $datatbale->addColumn('action', function ($row)
            {
                return $this->addActions($row);
            });
        }

        return $datatbale;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $builder_html = $this->builder()
            ->setTableId('customertype-table')
            ->columns($this->getColumns())
            ->minifiedAjax();
        if ($this instanceof WithButton) {
            $builder_html
                ->dom('Bfrtip')
                ->buttons($this->getButton());
        }

        return $builder_html;
    }

    /** @return string|View|null  */
    private function addActions($row)
    {
        if ($this instanceof WithOptions) {
            return view('partials.table.action', ['actions' => $this->addOptionsBuilder($row)]);
        }

        return;
    }
}
