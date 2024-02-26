<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discrepancy;
use App\Models\Inventory;
use DB;
class NotificationController extends Controller
{
   
public function show(Request $request)
    {
        $notification = auth()->user()->notifications()->findOrFail($request->id);

        $notification->markAsRead();

$items=DB::table('items as i')
            ->join('inventories as in','in.item_id','=','i.id')
            ->join('discrepancies as d','d.item_id','=','in.id')

            ->where('d.stock_id',$notification->data['stock_take_id'])->get();

        return view('notifications.show', ['notification' => $notification,'items'=>$items]);

    }
}
