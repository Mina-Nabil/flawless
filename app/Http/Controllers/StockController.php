<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockType;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{

    protected $data;

    public function stock()
    {
        $this->initStockDataArr();
        return view("stock.table", $this->data);
    }

    public function transactions()
    {
        $this->initTransactionsArr();
        return view("stock.table", $this->data);
    }

    public function transaction($code)
    {
        $this->initTransactionsArr($code);
        return view("stock.table", $this->data);
    }

    public function entry()
    {
        $this->data['title']    =   "New Transaction";
        $this->data['items'] = StockItem::active()->get();
        $this->data['formTitle'] = "Add New Title";

        return view("stock.entry", $this->data);
    }

    public function insertEntry(Request $request)
    {
        $request->validate([
            "stock_ids"  =>  "required|array",
            "stock_ids.*"    =>  "exists:stock_items,id",
            "amount"    =>  "required|array"
        ]);

        $insertArr = [];
        foreach ($request->stock_ids as $key => $id) {
            array_push($insertArr, [
                "stock_id"  =>  $id,
                "amount"    =>  $request->amount[$key]
            ]);
        }
        $sessionID = $request->session_id;

        StockItem::newTransaction($insertArr, $sessionID ? "100" . $sessionID : null);

        if ($sessionID) {
            return redirect()->back();
        } else {
            return redirect()->action([self::class, 'transactions']);
        }
    }

    private function initStockDataArr()
    {
        $this->data['items'] = StockItem::active()->with('type')->get();
        $this->data['title'] = "Current Stock";
        $this->data['subTitle'] = "Show current inventory";
        $this->data['cols'] = ['Name', 'Unit', 'Type', 'Count'];
        $this->data['atts'] = [
            'STCK_NAME',
            'STCK_UNIT',
            ['foreign' => ['rel' => 'type', 'att' => 'STTP_NAME']],
            ['number'   =>  ['att'  =>  'STCK_CUNT']]
        ];
    }

    private function initTransactionsArr($code = null)
    {
        if ($code) {
            $this->data['items'] = StockItem::loadTransaction($code);
        } else {
            $this->data['items'] = StockItem::loadTransactions();
        }
        $this->data['title'] = "Transactions";
        $this->data['subTitle'] = "Show stock transactions";
        $this->data['cols'] = ['#', 'User', 'Items', 'Total'];
        $this->data['atts'] = [
            ['attUrl' => ['url' => "stock/transaction", "shownAtt" => 'STTR_CODE', "urlAtt" => 'STTR_CODE']],
            'DASH_USNM',
            'STCK_NAME',
            ['number'   =>  ['att'  =>  'STTR_AMNT']]
        ];
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
            'unit' => 'required',
            'type_id' => 'required|exists:stock_types,id',
        ]);

        StockItem::insertNew($request->name, $request->unit, $request->type_id, $request->is_session == 'on' ? 1 : 0);

        return redirect("stockitems/home");
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'unit' => 'required',
            'type_id' => 'required|exists:stock_types,id',
        ]);

        /** @var StockItem */
        $item = StockItem::findOrFail($request->id);
        $item->updateItem($request->name,  $request->unit, $request->type_id, $request->is_session == 'on' ? 1 : 0);

        return redirect("stockitems/home");
    }

    public function toggleItem($itemID)
    {
        /** @var StockItem */
        $stockItem = StockItem::findOrFail($itemID);
        $stockItem->toggleState();

        return redirect()->action([self::class, "items"]);
    }

    //init page data
    private function initItemsDataArr($code = null)
    {
        $this->data['items'] = StockItem::with('type')->get();
        $this->data['types'] = StockType::all();
        $this->data['title'] = "Stock Items";
        $this->data['subTitle'] = "Manage and add Stock items";
        $this->data['formTitle'] = "Add Item";
        $this->data['cols'] = ['Name', 'Unit', 'Type', 'Active', 'Edit'];
        $this->data['atts'] = [
            'STCK_NAME',
            'STCK_UNIT',
            ['foreign' => ['rel' => 'type', 'att' => 'STTP_NAME']],
            [
                'toggle' => [
                    "att"   =>  "STCK_ACTV",
                    "url"   =>  "stockitems/toggle/",
                    "states" => [
                        "1" => "Active",
                        "0" => "Disabled",
                    ],
                    "actions" => [
                        "1" => "disable the stock item",
                        "0" => "Activate the stock item",
                    ],
                    "classes" => [
                        "1" => "label-success",
                        "0" => "label-danger",
                    ],
                ]
            ],
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
