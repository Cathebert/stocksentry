<?php
return[
'received_from_supplier'=>"Received from Supplier",
'order_store'=>"Requested from Store",
'issue_to'=>"Issued out   to :name",
'issue_received_from'=>"Received from :name",

'issue_approved'=>"Issue has been approved ",
'issue_accept'=>"Issue has been accepted in the system",
'stock_taken'=>"Stock take completed Successfully",
'stock_taken_approved'=>"Stock update is in progress.We will notify you when it is done",
'stock_taken_cancelled'=>"Stock taken has been cancelled",
    /* Issue statuses */
    'issues'                         => [
        'approved'           => 1,
        'accepted'           => 2,
        'cancelled'          => 3,
        
        
    ],
     /* Issue statuses */
    'received' => [
        'received_complete'  => "Items received Successfully",
        'received_failed'    => "Failed to receive items",
       
        
    ], 
    'consumed'=>[
        'single_item_consumption'=>"Item consumption updated",
        'multiple_items_consumption'=>"Items consumption has been updated",
        'description'=>'Item consumption updated on :date',
    ], 
    'adjustment'=>[
        'positive_adjustment'=>"Item quantity got increased",
        'negative_adjustment'=>"Item quantity got reduced ",
        'adjustment_success'=>"Adjustment Approved Successfully",
        'adjustment_failed' =>"Failed to adjust quantity",

    ],

    'stocktaken'=>[
        'bin_description'=>"Stock Take was conducted",
        'stocktake_update_complete'=>"System stock has been updated successfully"
    ],
     'order'=>[
        'order_sent'=>"Order Sent to :lab_name",
        'order_received'=>"Order received from Store",
    ],
      'disposal'=>[
        'created' => "Disposal has been saved waiting approval",
        'admin_created'=>"Disposal has been set and approved",
        'approved' =>'Disposal has been approved',
        'bin_description'=>"Item(s) disposed  due to damage ",
        'bin_expiry_description'=>"Item(s) disposed  due to expiry",
        'bin_donation_description'=>"Item(s) disposed  due to donation",
    ]

];
