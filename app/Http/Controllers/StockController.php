<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockType;
use Illuminate\Http\Request;

class StockController extends Controller
{

    protected $data;

    public function transactions()
    {
    }

    public function newTransaction()
    {
    }

    ///items
    public function items()
    {
        $this->initItemsDataArr();
        $this->data['formURL'] = "stockitems/insert";
        $this->data['isCancel'] = false;
        return view("settings.stockitems", $this->data);
    }

    public function item($id)
    {
        $this->data['item'] = StockItem::findOrFail($id);
        $this->initItemsDataArr();
        $this->data['formTitle'] = "Manage Item (" . $this->data['item']->STCK_NAME . ')';
        $this->data['formURL'] = "stockitems/update";
        $this->data['isCancel'] = true;
        return view("settings.stockitems", $this->data);
    }

    public function insertItem(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'type_id' => 'required|exists:stock_types,id',
            'is_session' => 'required',
        ]);

        StockItem::insertNew($request->name, $request->type_id, $request->is_session == 'on' ? 1 : 0);

        return redirect("stockitems/home");
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'type_id' => 'required|exists:stock_types,id',
            'is_session' => 'required',
        ]);

        /** @var StockItem */
        $item = StockItem::findOrFail($request->id);
        $item->updateItem($request->name, $request->type_id, $request->is_session == 'on' ? 1 : 0);

        return redirect("stockitems/home");
    }

    //init page data
    private function initItemsDataArr()
    {
        $this->data['items'] = StockItem::with('type')->get();
        $this->data['types'] = StockType::all();
        $this->data['title'] = "Stock Items";
        $this->data['subTitle'] = "Manage and add Stock items";
        $this->data['formTitle'] = "Add Item";
        $this->data['cols'] = ['Name', 'Type', 'Edit'];
        $this->data['atts'] = [
            'STCK_NAME',
            ['foreign' => ['rel' => 'type', 'att' => 'STTP_NAME']],
            [
                'edit' => ['url' => 'stockitems/edit/', 'att' => 'id']
            ]
        ];
        $this->data['homeURL'] = 'stockitems/home';
    }


    //// types
    public function stocktypes()
    {
        $this->initTypeDataArr();
        $this->data['formURL'] = "stocktypes/insert";
        $this->data['isCancel'] = false;
        return view("settings.stocktypes", $this->data);
    }

    public function stocktype($id)
    {
        $this->data['type'] = StockType::findOrFail($id);
        $this->initTypeDataArr();
        $this->data['formTitle'] = "Manage Category (" . $this->data['type']->STTP_NAME . ')';
        $this->data['formURL'] = "stocktypes/update";
        $this->data['isCancel'] = true;
        return view("settings.stocktypes", $this->data);
    }

    public function insertType(Request $request)
    {

        $request->validate([
            'name' => 'required',
        ]);

        StockType::insertNew($request->name);

        return redirect("stocktypes/home");
    }

    public function updateType(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'name' => 'required'
        ]);

        /** @var StockType */
        $type = StockType::findOrFail($request->id);
        $type->updateType($request->name);

        return redirect("stocktypes/home");
    }

    //init page data
    private function initTypeDataArr()
    {
        $this->data['items'] = StockType::all();
        $this->data['title'] = "Stock Categories";
        $this->data['subTitle'] = "Manage and add Stock categories";
        $this->data['formTitle'] = "Add Category";
        $this->data['cols'] = ['Name', 'Edit'];
        $this->data['atts'] = ['STTP_NAME', ['edit' => ['url' => 'stocktypes/edit/', 'att' => 'id']]];
        $this->data['homeURL'] = 'stocktypes/home';
    }
}
