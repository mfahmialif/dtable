<?php

namespace App\Http\Services;

class DataTable
{
    public static function results($columns, $data, $request)
    {
        $limit = $request->length;
        $start = $request->start;

        $search = $request->search['value'];
        $order = $columns[$request->order[0]['column']];
        $dir = $request->order[0]['dir'];
        $data = $data->where(function ($query) use ($search, $columns) {
            foreach ($columns as $col) {
                $query->orWhere($col, 'LIKE', "%$search");
            }
        });

        $data = $data->addSelect('*');
        foreach ($columns as $col) {
            $data = $data->addSelect(DataTable::makeAlias($col));
        }
        $data = $data->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        return $data;
    }

    public static function resultsFiltered($columns, $data, $request)
    {
        $search = $request->search['value'];

        $data = $data->where(function ($query) use ($search, $columns) {
            foreach ($columns as $col) {
                $query->orWhere($col, 'LIKE', "%$search");
            }
        });
        $data = $data->count();
        return $data;
    }

    public static function responseDataTable($results, $resultFiltered, $total)
    {
        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => intval($total),
            "recordsFiltered" => intval($resultFiltered),
            "data" => $results,
        ]);
    }

    public static function makeAlias($col)
    {
        $name = str_replace(".", "_", $col);
        return "$col as $name";
    }

    public static function makeName($col)
    {
        $name = str_replace(".", "_", $col);
        return $name;
    }

    public static function addAction($item, $columns, $tableName)
    {
        $action = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

        $action .= DataTable::addEdit($item, $columns);
        $action .= DataTable::addDelete($item, $tableName);
        $action .= '</div>
                </div>
            ';
        return $action;
    }

    /**
     *  $edit = DataTable::addEdit($item, $columns);
     *  $delete = DataTable::addDelete($item, 'users');
     *  $item->action = DataTable::addActionCustom("$edit $delete");
     */
    public static function addActionCustom($element)
    {
        $action = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

        $action .= $element;
        $action .= '</div>
                </div>
            ';
        return $action;
    }

    public static function addEdit($item, $columns)
    {
        $edit = '<button type="button" class="dropdown-item"
        data-toggle="modal" data-target="#modal_edit" ';

        foreach ($columns as $col) {
            $edit .= 'data-' . DataTable::makeName($col) . '="' . $item[DataTable::makeName($col)] . '" ';
        }
        $edit .= '>Edit</button>';

        return $edit;
    }

    /**
     *  $user = new User();
     *  $tableName = $user->getTable();
     */
    public static function addDelete($item, $tableName)
    {
        $id = $item[$tableName . '_id'];
        $delete = '<form action="" method="POST" onsubmit="deleteData(event)">
        ' . method_field('delete') . csrf_field() . '
            <input type="hidden" name="id" value="' . $id . '">
            <button type="submit" class="dropdown-item text-danger">
                Hapus
            </button>
        </form>';

        return $delete;
    }

}
