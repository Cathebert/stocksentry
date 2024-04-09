<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClerkController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ConsumptionController;
use App\Http\Controllers\StockTakeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LabManagerReportController;
use App\Http\Controllers\LabManagerTableReportController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\LabInventoryController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\SectionHeadController;
use App\Http\Controllers\Section\SectionInventoryController;
use App\Http\Controllers\Section\SectionBinCardController;
use App\Http\Controllers\Section\SectionConsumptionController;
use App\Http\Controllers\Section\SectionStockTakeController;
use App\Http\Controllers\Section\SectionReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ItemDisposalController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\HelpController;

use App\Http\Controllers\ColdRoom\ColdRoomController;
use App\Http\Controllers\ColdRoom\ColdRoomBinCardController;
use App\Http\Controllers\ColdRoom\ColdRoomStockTakeController;
use App\Http\Controllers\ColdRoom\ColdRoomStockDisposalController;
use App\Http\Controllers\ColdRoom\ColdRoomAdjustmentController;
use App\Http\Controllers\ColdRoom\ColdRoomIssueController;
use App\Http\Controllers\DisposalReportController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ExpiredItemController;
use App\Http\Controllers\SystemMailController;
use App\Http\Controllers\ScheduleReportController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/',[HomeController::class,'welcome'])->name('provider.login');
  Route::get('/',[LoginController::class,'LoginForm'])->name('user.login');


//Auth::routes();
Route::post('/login',[LoginController::class,'adminLogin'])->name('login');
Route::post('/logout',[LoginController::class,'logout'])->name('logout');
Route::group(['prefix' => 'Admin', 'middleware'=>'admin'], function () {

Route::get('/register',[RegisterController::class,'showStudentRegistrationForm'])->name('student.register');
Route::get('/home', [HomeController::class, 'index'])->name('admin.home');
Route::post('/save-stock',[InventoryController::class, 'saveReceivedInventory'])->name('stock.receive');


Route::get('/stats',[InventoryController::class, 'Stats'])->name('stats');
Route::get('/fetch',[InventoryController::class, 'Fetch'])->name('items.fetch');
Route::get('/requisition',[InventoryController::class, 'showRequisition'])->name('issue.requisition');
Route::get('/issue',[InventoryController::class, 'showIssue'])->name('issue.issue');
Route::get('/received-issued',[InventoryController::class, 'showReceivedIssued'])->name('issue.received_issued');

Route::get('/issues',[InventoryController::class, 'getIssues'])->name('issue.getItems');
Route::get('/get-selected',[InventoryController::class, 'getSelectedItems'])->name('inventory.getSelectedItems');
Route::get('/check-quantity',[InventoryController::class, 'checkQuantity'])->name('inventory.check_quantity');
Route::post('/save-issued',[InventoryController::class, 'saveIssued'])->name('inventory.save-issued');
Route::get('/item-details',[InventoryController::class, 'getItemDetails'])->name('inventory.item_details');
Route::get('/get-received-issues',[InventoryController::class, 'getReceivedIssues'])->name('received.issues');
Route::get('/approve-issue',[InventoryController::class, 'approveIssue'])->name('issue.approve');
Route::get('/show-approvals',[InventoryController::class,'showApprovals'])->name('issue.showapprovals');
Route::post('/save-approved-issue',[InventoryController::class, 'saveApprovedIssue'])->name('issue.approve_save');
Route::post('/issue-void',[InventoryController::class, 'voidIssue'])->name('issue.void_issue');
Route::get('/issue-approved',[InventoryController::class, 'IssuesApproved'])->name('issue.approved');
Route::get('/show_approved_issue',[InventoryController::class,'showApprovedIssue'])->name('issue.showapproved');
Route::post('/issue-accept',[InventoryController::class, 'AcceptIssue'])->name('issue.accept');
Route::get('/search_issue_by_number',[InventoryController::class,'searchIssueByNumber'])->name('issue.search_by_number');
Route::get('/search_issue_by_date',[InventoryController::class,'searchIssueByDateRange'])->name('issue.search_by_date_range');
Route::get('/search_issue_by_lab_sent',[InventoryController::class,'searchIssueByLabSent'])->name('issue.search_by_lab_sent');
Route::get('/show_update',[InventoryController::class, 'showConsumptionModal'])->name('inventory.show_update');
Route::get('/inventory',[InventoryController::class, 'showInventory'])->name('inventory.bincard');
Route::get('/stock-forecasting',[InventoryController::class, 'showForecasting'])->name('inventory.showstock-forecasting');
Route::get('/all-inventory',[InventoryController::class, 'showAllInventory'])->name('inventory.all');
Route::get('/inventory-all',[InventoryController::class, 'loadAllInventory'])->name('inventory.load_all');
Route::get('/update-consumption',[InventoryController::class, 'showConsumptionForm'])->name('inventory.showupdate-consumption');
Route::get('/issue-view-details',[InventoryController::class, 'viewIssue'])->name('issue.view');
Route::get('/inventory-bincard',[InventoryController::class, 'binCard'])->name('bincard');
Route::get('/bincard-search',[InventoryController::class,'binCardFilter'])->name('bincard.search');
Route::get('/stock-take',[InventoryController::class, 'showStockTake'])->name('inventory.showstocktake');
Route::get('/stock-adjustment',[InventoryController::class, 'showStockAdjustment'])->name('inventory.showstock-adjustment');
Route::get('/stock-disposal',[InventoryController::class, 'showStockDisposal'])->name('inventory.show-disposal');
Route::get('/get-more',[InventoryController::class, 'showMore'])->name('inventory.more');
Route::get('/show-lab-inventory',[InventoryController::class, 'showLabInventoyForm'])->name('inventory.lab_inventory');
Route::get('/inventory-bylocation',[InventoryController::class, 'showInventoryByLocation'])->name('inventory.bylocation');
Route::get('/disposal-show',[InventoryController::class,'showDisposalModal'])->name('inventory.disposal_show');
Route::get('/disposal-list',[InventoryController::class,'disposalList'])->name('inventory.disposal_list');
Route::get('/disposal-selected',[InventoryController::class,'selectedForDisposal'])->name('inventory.selected_dispose');
Route::post('/run-disposal',[InventoryController::class,'runItemDisposal'])->name('inventory.run-disposal');
//_______________________________Contract Management________________________________________//
Route::get('/contract-show',[ContractController::class,'add'])->name('contract.add');
Route::get('/contract-load',[ContractController::class,'load'])->name('contract.load');
Route::get('/contract-add',[ContractController::class,'showModal'])->name('contract.show_modal');
Route::post('/contract-save',[ContractController::class,'saveContract'])->name('contract.save');
Route::post('/sub-type',[ContractController::class,'saveSubscriptionType'])->name('sub.type');
//____________________________________Admin Stock History___________________________________//
Route::get('/stock_view',[StockTakeController::class,'stockViewHistory'])->name('stock.view_history');
Route::get('/stocktake_items',[StockTakeController::class,'itemsLoadSelected'])->name('stock.item_location');
Route::get('/download_selected_item',[StockTakeController::class,'downloadItemsSelected'])->name('stock.download_item_selected');
Route::get('/download_items/{id}',[StockTakeController::class,'download'])->name('stock_download');
//------------------------------------Requisitions----------------------------------------//
Route::get('/view-approved',[RequisitionController::class, 'viewApprovedRequest'])->name('requests.view-approved');
Route::post('/save-approve',[RequisitionController::class, 'updateApproved'])->name('requisition.save-approved');
Route::get('/filter',[RequisitionController::class, 'searchRequisition'])->name('requisition.filter');
Route::post('/mark',[RequisitionController::class,'markToConsolidate'])->name('requisition.mark');
Route::post('/remove',[RequisitionController::class,'removeToConsolidate'])->name('requisition.remove');
Route::get('system_mails',[SystemMailController::class,'show'])->name('system_mails');
Route::get('load_system',[SystemMailController::class,'load'])->name('mail.load');

//_____________________schedule reports______________________//
Route::get('scheduled_reports',[ScheduleReportController::class,'show'])->name('scheduled.show');
Route::get('scheduled_load',[ScheduleReportController::class,'load'])->name('scheduled.load');
Route::post('save_scheduled',[ScheduleReportController::class,'save'])->name('scheduled.save');
Route::post('deactivate_schedule',[ScheduleReportController::class,'deactivate'])->name('scheduled.deactivate');


        //__________________________________Stats______________________________________//
Route::get('/inventory-health',[StatsController::class,'showInventoryHealth'])->name('stats.pie');
Route::get('/lab-inventory-health',[StatsController::class,'showLabInventoryHealth'])->name('labstats.pie');
Route::get('/inventory-top_used',[StatsController::class,'showTopUsedItems'])->name('stats.area');
Route::get('/inventory-health-modal',[StatsController::class, 'showHealthModal']) ->name('stats.detail_modal');  
Route::get('/inventory-health-table',[StatsController::class, 'showHealthTable'])->name('stats.table');
Route::get('/consumption-dashboard',[StatsController::class,'loadConsumptionTable'])->name('dashboard.consumption');
Route::get('/stock-level-dashboard',[StatsController::class,'loadStockLevel'])->name('dashboard.stock-level');
Route::get('/requisition-dashboard',[StatsController::class,'loadRequisition'])->name('dashboard.requisition');
Route::get('/orders-dashboard',[StatsController::class,'loadOrders'])->name('dashboard.orders');
Route::get('/lab-percentage',[StatsController::class,'labUsagePercentage'])->name('dashboard.percentage');
Route::get('/period-report',[StatsController::class, 'reportPeriod'])->name('dashboard.period');


//_________________________________________Forecasting _______________________________//
Route::get('/forecasted',[ForecastController::class,'showForecasted'])->name('inventory.getforecast');
Route::get('/forecast-load',[ForecastController::class,'loadForecastItem'])->name('forecast.load');
Route::get('/forecast-forecast',[ForecastController::class,'generateForecast'])->name('forecast.generate');
Route::post('/forecast-order',[ForecastController::class,'forecastOrder'])->name('forecast.order');
Route::get('/view-order',[ForecastController::class,'viewOrders'])->name('view.orders');
Route::get('/orders-new',[ForecastController::class ,'loadNewOrders'])->name('orders.load-new');
Route::get('/received-orders',[ForecastController::class,'viewReceivedOrders'])->name('received.orders');
Route::get('/order-details',[ForecastController::class,'showOrderDetails'])->name('order.show');
Route::get('/load-order',[ForecastController::class,'loadOrderDetails'])->name('order.load');
Route::post('/consolidate',[ForecastController::class,'consolidateOrder'])->name('order.consolidate');
Route::get('/download_order/{name}',[ForecastController::class,'downloadOrder'])->name('download_order');
Route::get('/forecast-filter',[ForecastController::class,'loadFilterForecastItemByLocation'])->name('forecast.filter-load');

Route::post('/mark-as-received',[ForecastController::class,'markAsReceived'])->name('order.mark-received');
Route::post('/add-consolidate',[ForecastController::class,'markForConsolidation'])->name('order.mark-consolidate');
Route::get('/order-consolidated-list',[ForecastController::class,'consolidateOrders'])->name('order.view_marked');
Route::get('/show-orders-marked',[ForecastController::class,'showOrdersMarked'])->name('order.show_marked');
Route::get('/order_data',[ForecastController::class,'orderGetData'])->name('orders.get_details');
Route::post('/export-orders',[ForecastController::class,'exportOrderList'])->name('order.export');
Route::get('/orders-received',[ForecastController::class,'receivedOrders'])->name('orders.received');
Route::get('/orders-consolidated',[ForecastController::class,'showOrderHistory'])->name('orders.consolidated');
Route::get('/orders-load-consolidated',[ForecastController::class,'loadOrderHistory'])->name('consolidated.order_history');

Route::get('/approve_order',[ForecastController::class,'loadOrders'])->name('forecast.load_approve');
Route::get('/load_pending',[ForecastController::class,'loadPendingOrders'])->name('order.load_pending_approval');
Route::post('/pending_approve',[ForecastController::class,'approvePendingOrder'])->name('forecast.mark_approved');

          
 //____________________________admin Reports________________________________//
Route::get('/reports',[ReportController::class,'show'])->name('reports.show');
Route::get('/expired-report',[ExpiredItemController::class, 'showExpiredItemsReport'])->name('report.expired');
Route::get('loadbylab',[ExpiredItemController::class,'loadExpiredByLab'])->name('report.expiredbylab');
Route::get('/expired-by-period',[ExpiredItemController::class,'loadExpiredItemByPeriod'])->name('report.expiredbyperiod');
Route::get('/expired-by-range', [ExpiredItemController::class,'loadExpiredItemByRange'])->name('report.expiredbyrange');
Route::get('/load_expired_item',[ExpiredItemController::class,'loadExpiredItemsReport'])->name('report.expired_table');
Route::get('/download_expired',[ExpiredItemController::class,'downloadExpired'])->name('report.expired_download');
Route::get('/get_expired_file/{name}',[ExpiredItemController::class,'getExpiredFile'])->name('report.get_expired_download');
Route::get('/expired_excel_download/{name}',[ExpiredItemController::class,'getExpiredExcelFile'])->name('report.expired_download-excel');
Route::get('/expiry-report',[ReportController::class, 'showExpiryReport'])->name('report.expiry');
Route::get('/expiry-table',[ReportController::class, 'loadExpiryTable'])->name('report.expiry_table');
Route::post('/schedule-report/{type}',[ReportController::class, 'scheduleReport'])->name('report.schedule_report');
Route::get('/download-report',[ReportController::class, 'downloadReport'])->name('report.download');
Route::get('/expiry_download/',[ReportController::class,'expiryDownload'])->name('report.expiry_download');
Route::get('/expiry_download-excel/{name}',[ReportController::class,'expiryDownloadExcel'])->name('report.expiry_download-excel');
Route::get('/issue-report',[ReportController::class, 'showIssueReport'])->name('issue.report');
Route::get('/issue-table',[ReportController::class, 'loadIssueTable'])->name('report.issue_table');
Route::get('/consumption-report',[ReportController::class,'showConsumptionReport'])->name('report.consumption');
Route::get('/consumption-load',[ReportController::class,'loadConsumptionTable'])->name('report.consumption_table');
Route::get('/consumption-more',[ReportController::class,'consumptionMoreDetails'])->name('report.loadmore');
Route::get('/consumption-filter',[ReportController::class,'filterConsumption'])->name('report.consumption-filter');
Route::get('/change-frequency',[ReportController::class,'changeFrequency'])->name('change_frequency');
Route::get('/download-consumption-report',[ReportController::class, 'downloadConsumptionReport'])->name('report.consumptiondownload');
Route::get('/red/{name}',[ReportController::class,'download'])->name('redirect_download');
Route::get('/consumed-excel/{name}',[ReportController::class,'excelDownload'])->name('report.consumed_download-excel');
Route::get('/stock_level_download/{name}',[ReportController::class,'stockLevelDownload'])->name('report.stock_level_download');
//disposal
Route::get('/disposal',[DisposalReportController::class,'showDisposal'])->name('report.stock-disposal');
Route::get('/load_disposal',[DisposalReportController::class,'loadDisposal'])->name('report.disposed_table');
Route::get('/loaddisposebylab',[DisposalReportController::class,'loadByLab'])->name('report.disposedbylab');
Route::get('/loaddisposebyrange',[DisposalReportController::class,'loadByRange'])->name('report.disposedbyrange');
//_______requsition
 Route::get('/requisition-report',[ReportController::class,'showRequisitionReport'])->name('requisition.report');
 Route::get('/load-requisition-list',[ReportController::class,'loadRequisitionReport'])->name('report.load-requisition');
 Route::get('/get-requisition-data',[ReportController::class,'getRequestedData'])->name('report.requisition-more-data');
 Route::get('/supplier_order_report',[ReportController::class,'showSupplierOrderReport'])->name('report.supplier-order');
 Route::get('/load-by-location',[ReportController::class,'loadByLocation'])->name('report.load_by_location');
 Route::get('/load-by-period',[ReportController::class,'loadByPeriod'])->name('report.load_by_period');
 //________________supplier orders________/
 Route::get('/load-supplierorders',[ReportController::class,'loadOrdersToSupplier'])->name('report.supplier_order');
 Route::get('/order_by-period',[ReportController::class,'loadOrderByPeriod'])->name('report.supplier_order_by_period');
 Route::get('/order_by-location',[ReportController::class,'loadOrderByLocation'])->name('report.supplier_order_by_location');
 Route::get('/order_by-supplier',[ReportController::class,'loadOrderBySupplier'])->name('report.supplier_order_by_supplier');
 Route::get('/load-more_supplier_orders',[ReportController::class,'getOrderDetails'])->name('report.getorderdetails');
 
 //_______________stock leveL________/
 Route::get('/stock_level',[ReportController::class,'stockLevelReport'])->name('report.stock-level');
 Route::get('/stock-load-leve',[ReportController::class,'loadStockLevelReport'])->name('report.load_stock_level');
 Route::get('/stock-level-details',[ReportController::class,'loadStockLevelDetails'])->name('report.stock_level_details');
 //_____out of stock  __________//
 Route::get('/out_of_stock',[ReportController::class,'showOutOfStockReport'])->name('reports.item_out_of_stock');
 Route::get('/load_out_of_stock',[ReportController::class,'loadOutOfStock'])->name('report.load_out_of_stock');
 Route::get('load-stout_out_details',[ReportController::class,'loadOutOfStockDetails'])->name('report.stock_out_details');

      //-------------------Items --------------------------------------//
Route::post('/add',[ItemController::class, 'addInventory'])->name('inventory.add');
Route::get('/show',[ItemController::class, 'showItems'])->name('item.show');
Route::get('/receive',[ItemController::class, 'receiveInventory'])->name('admin.receive_stock');
Route::get('/all-received',[ItemController::class, 'AllreceivedInventory'])->name('admin.all-received');
Route::get('/create',[ItemController::class, 'createNew'])->name('admin.create');
Route::get('/Items',[ItemController::class, 'showItemsOnTable'])->name('inventory.getItems');
Route::get('/TodaysItems',[ItemController::class, 'showTodaysItemsOnTable'])->name('inventory.getTodaysItems');

Route::get('/edit',[ItemController::class, 'editItem'])->name('inventory.edit');
Route::post('/update',[ItemController::class, 'updateItem'])->name('inventory.update');
Route::post('/delete',[ItemController::class, 'deleteItem'])->name('inventory.delete');
Route::get('/search',[ItemController::class, 'searchItem'])->name('items.search');
Route::get('/add-item',[ItemController::class, 'ItemDetailsUpdate'])->name('item.selected');
Route::get('/load-added',[ItemController::class, 'loadReceivedTable'])->name('item.getadded');
Route::post('/add_temp',[ItemController::class,'addTemporary'])->name('item.add_temp');
Route::get('/total',[ItemController::class, 'getTotalCost'])->name('item.total');
Route::post('/save-received',[ItemController::class, 'saveReceivedItems'])->name('items.save');
Route::get('/print',[ItemController::class,'printTest' ])->name('item.print');
Route::get('/download/{id}/{action}/{type}',[ItemController::class, 'downloadDocument'])->name('download');
Route::get('/received',[ItemController::class, 'loadReceivedItems'])->name('item.recieved');
Route::get('/received-details',[ItemController::class, 'receivedDetails'])->name('received.details');
Route::get('/generate_pdf/{id}',[ItemController::class, 'generatePDF'])->name('received.generatepdf');
Route::get('/generate_print/{id}',[ItemController::class, 'generatePrint'])->name('received.generateprint');
Route::get('/filtered-received',[ItemController::class, 'getReceivedFiltered'])->name('items.filtered');
Route::post('/deactivate-item',[ItemController::class, 'deactivateItem'])->name('item.deactivate');
Route::post('/upload-csv-item-list',[ItemController::class, 'uploadCVSItemList'])->name('item.uploadcsv-itemlist');
Route::get('/export-items',[ItemController::class,'exportItemList'])->name('items.export_items');
Route::get('/item-search',[ItemController::class, 'searchFilterItem'])->name('item.filter-search');
Route::get('/item_list_download/{name}',[ItemController::class,'downloadItems'])->name('download_item_file');
Route::post('/item_edit',[ItemController::class,'deleteItemDetails'])->name('item.item_delete');
Route::post('/remove_all',[ItemController::class,'deleteAllEntries'])->name('item.item_delete_all');
Route::get('/received-status',[ItemController::class ,'receivedItemCheckList'])->name('admin.received-status');
Route::get('/received_checklist',[ItemController::class,'receivedCheckListLoad'])->name('received.status');
Route::get('/received_checklist_details',[ItemController::class,'checklistDetails'])->name('received.status_view_details');
Route::get('/received_checklist_print/{id}/{type}',[ItemController::class,'printReceivedChecklist'])->name('received.checklistprint');


//------------------------Requisitions--------------------------------->
Route::get('/requistions',[RequisitionController::class,'show'])->name('requisition.getRequests');
Route::get('/approved-requisitions', [RequisitionController::class,'showApproved'])->name('requests.approved');
Route::get('/load-approved',[RequisitionController::class, 'loadApproved'])->name('requests.load_approved');
 Route::get('/get-labsections',[RequisitionController::class,'getLabSections'])->name('lab.selected-sections');


//-----------------------Supplier---------------------------------------->
Route::get('/supplier',[SupplierController::class,'addSupplier'])->name('supplier.add');
Route::post('/create-supplier',[SupplierController::class, 'createSupplier'])->name('supplier.create');

Route::get('/suppliers',[SupplierController::class,'view'])->name('supplier.view');  
Route::get('/load_suppliers',[SupplierController::class,'loadAllSuppliers'])->name('supplier.load');
Route::post('/supplier-delete', [SupplierController::class, 'destroy'])->name('supplier.destroy');
Route::get('/supplier_edit', [SupplierController::class, 'editSupplier'])->name('supplier.edit');
Route::post('/supplier_update', [SupplierController::class, 'update'])->name('supplier.update');

//--------------------------Laboratory--------------------------------------->
Route::get('/laboratory',[LaboratoryController::class,'addLaboratory'])->name('lab.add');
Route::post('/createLab',[LaboratoryController::class, 'createLaboratory'])->name('lab.create');
Route::get('/list',[LaboratoryController::class,'labList'])->name('lab.list');
Route::get('/load',[LaboratoryController::class,'loadLabList'])->name('lab.load');
Route::get('/lab-edit',[LaboratoryController::class,'editLabDetails'])->name('lad.edit');
Route::post('/lab-update',[LaboratoryController::class,'updateLabDetails'])->name('lab.update');
Route::post('/lab-delete',[LaboratoryController::class,'deleteLab'])->name('lab.delete');
Route::get('/labDetails/{id}/{slug}',[LaboratoryController::class, 'showMoreLabData'])->name('lab.more');
Route::get('/labsections',[LaboratoryController::class,'getSections'])->name('lab.sections');
//__________________________________USER______________________________//
Route::get('/user',[UserController::class,'addUser'])->name('user.add'); 
Route::post('/moderator',[UserController::class, 'createModerator'])->name('user.create');
Route::get('/receiver',[UserController::class, 'getReceiver'])->name('user.receiver');
Route::get('/check-email',[UserController::class,'checkEmailExist'])->name('checkEmailExists');

//
Route::get('/users',[UserController::class,'view'])->name('user.view');
Route::get('/load_user',[UserController::class,'loadUsers'])->name('user.load');
Route::get('/edit_user',[UserController::class,'editUser'])->name('user.edit');
Route::post('/user_delete', [UserController::class, 'destroy'])->name('user.destroy');
Route::post('/user_update', [UserController::class, 'update'])->name('user.update');
Route::post('/user_reset',[UserController::class,'resetPassword'])->name('user.reset');
});
Route::group(['prefix'=>'General','middleware'=>'auth'],function(){
Route::get('/profile',[UserController::class,'profileView'])->name('userprofile');
Route::get('/password',[UserController::class,'password'])->name('password');
Route::get('/signature',[UserController::class,'signature'])->name('signature');
Route::post('change-password',[UserController::class,'changePassword'])->name('changepassword');
Route::post('/profile/update',[UserController::class,'profileUpdate'])->name('profile.update');

Route::post('/update-signature',[UserController::class,'updateSignature'])->name('update_signature');


Route::get('/request',[GeneralController::class, 'showRequest'])->name('moderator.request');
Route::get('/lab_issue',[GeneralController::class,'showIssue'])->name('lab.issue');
Route::get('/lab_receive_issued',[GeneralController::class,'showLabReceived'])->name('lab.received_issued');

Route::get('/inventories',[GeneralController::class, 'showLabInventory'])->name('general.inventory');
//_______________________________________Adjustments_______________________________________________//
Route::get('/consume',[ConsumptionController::class, 'show'])->name('inventory.load');
Route::get('/adjust',[ConsumptionController::class,'inventoryAdjustment'])->name('inventory.adjust');
Route::post('/update_selected',[ConsumptionController::class, 'updateSelected'])->name('inventory.selected_update');
Route::post('/adjust_selected',[ConsumptionController::class, 'adjustSelected'])->name('inventory.selected_adjust');
Route::get('/view_adjusted',[ConsumptionController::class,'viewAdjustments'])->name('inventory.view_adjusted');
Route::get('/order_item_list',[RequisitionController::class,'orderItemList'])->name('requests.showOrderItems');
Route::get('/load-adjusted',[ConsumptionController::class,'loadAdjustedItems'])->name('inventory.load_adjusted');
Route::post('/approve-adjusted',[ConsumptionController::class,'approveAdjusted'])->name('inventory.adjusted_approve');
Route::post('/cancel-adjusted',[ConsumptionController::class,'cancelAdjusted'])->name('inventory.cancel_adjusted');
Route::post('/update_all',[ConsumptionController::class, 'updateMany'])->name('inventory.update_all');
Route::get('/search-adjustment',[ConsumptionController::class, 'searchItemAdjustment'])->name('inventory.search_adjustment');
//___________________________________________Item Disposals_____________________________________________//
Route::post('/run-disposal',[ItemDisposalController::class,'runItemDisposal'])->name('inventory.run-disposal');
Route::get('/disposal-list',[ItemDisposalController::class,'showDisposalList'])->name('disposal.list');
Route::get('/disposal-load',[ItemDisposalController::class,'loadDisposedItems'])->name('disposal.load');
Route::get('/disposal_view',[ItemDisposalController::class,'viewDisposal'])->name('disposal.viewmodal');
Route::get('/disposed-load-items',[ItemDisposalController::class,'getDisposedItem'])->name('disposal.load_data');
Route::post('/disposal-approve',[ItemDisposalController::class,'approveDisposedItem'])->name('disposal.approve');


//_________________________stock Taking _______________________________________________________//
Route::get('/stock_taking',[StockTakeController::class,'show'])->name('stock.take');
Route::post('/stock-upload',[StockTakeController::class, 'uploadCVS'])->name('stock.upload');
Route::get('/export',[StockTakeController::class, 'export'])->name('stock.export');
Route::get('/stock_inventory',[StockTakeController::class, 'loadStockInventory'])->name('inventory.stock');
Route::post('/stock-save',[StockTakeController::class, 'saveMany'])->name('stock.saveall');
Route::post('/save-selected',[StockTakeController::class, 'saveSelected'])->name('inventory.selected_save');
Route::get('/stock_history',[StockTakeController::class,'stockTakenLoad'])->name('stock.history_load');
Route::get('/stock_view_details',[StockTakeController::class,'stockViewDetails'])->name('stock.view_details');
Route::get('/stock_load_data',[StockTakeController::class,'loadStockTakenDetails'])->name('stock.load_data');
Route::post('/stocktake_approve_taken',[StockTakeController::class,'approveStockTaken'])->name('stock.approve_stock_taken');
Route::post('/stocktake_cancel',[StockTakeController::class,'cancelStockTaken'])->name('stock.cancel_stock_taken');



//___________________________inventory__________________________________________________________//

Route::get('/inventory_search',[InventoryController::class, 'searchItem'])->name('inventory.search');
//____________________________REPORTS__________________________________________________________//

 
 //__________________________logging_____________________________________________/
Route::get('/log',[ActivityLogController::class,'logActivity'])->name('logs');
Route::post('/delete-log',[ActivityLogController::class,'deleteLog'])->name('delete-log');

//_________________________________help_______________________________________/

Route::get('/help',[HelpController::class,'help'])->name('help');
Route::get('/lab_help',[HelpController::class,'labHelp'])->name('lab_help');

//_____________________________________________Notifications___________________________//
Route::get('/notifications', [NotificationController::class, 'show'])->name('notifications.show');
Route::get('/markasread',[NotificationController::class,'markAsRead'])->name('notifications.markasread');
//---------------------------- Requisitions -------------------------------------//
Route::get('/create',[RequisitionController::class, 'create'])->name('requests.create');
Route::get('/get-selected',[RequisitionController::class, 'getSelectedItems'])->name('requests.getSelectedItems');
Route::post('/store',[RequisitionController::class, 'store'])->name('requests.store');
Route::get('/requests-pending',[RequisitionController::class, 'pendingRequests'])->name('requests.pending');
Route::get('/request-load',[RequisitionController::class, 'loadRequests'])->name('requests.show_pending');
Route::post('/approve-request',[RequisitionController::class, 'update'])->name('requests.approve');
Route::post('/void-request',[RequisitionController::class, 'destroy'])->name('requests.void');
Route::get('/view-request',[RequisitionController::class, 'viewRequest'])->name('requests.view');
Route::get('/list',[RequisitionController::class, 'viewList'])->name('requests.view_list');
Route::get('/load-request-list',[RequisitionController::class, 'loadRequsitionList'])->name('requests.load-list');
Route::get('/export-requests',[RequisitionController::class,'exportRequisitionList'])->name('requisition.export');
Route::get('/consolidate-list',[RequisitionController::class,'consolidateOrders'])->name('requisition.view_marked');
Route::get('/show-requests',[RequisitionController::class,'showRequisitions'])->name('requisition.show_marked');
Route::get('/requistion_data',[RequisitionController::class,'requisitionGetData'])->name('requisition.get_details');
Route::get('/consolidation-history',[RequisitionController::class,'showConsolidationHistory'])->name('consolidate.history');
Route::get('/consolidate-load-history',[RequisitionController::class,'loadHistory'])->name('consolidated.load_history');
Route::get('/consolidated_document/{id}',[RequisitionController::class,'downloadConsolidatedDocument'])->name('consolidated.document');
Route::post('/requisition_remove',[RequisitionController::class,'removeRequisition'])->name('requisition.remove');


});

Route::group(['prefix' => 'LabManager', 'middleware'=>'moderator'], function () {
Route::get('/Home',[ProviderController::class,'index'])->name('moderator.home');
Route::get('/register',[RegisterController::class,'showProviderRegistrationForm'])->name('moderator.register');
Route::get('/project',[ProjectController::class, 'create'])->name('moderator.projects');

//__________________________________LabAdmin Inventory___________________________________________//
Route::get('/lab-bincard',[LabInventoryController::class, 'showLabBinCard'])->name('lab.bincard_inventory');
Route::get('/lab_consumption',[LabInventoryController::class, 'showLabConsumption'])->name('lab.showupdate-consumption');
Route::get('/lab-stocktake',[LabInventoryController::class, 'showLabStockTake'])->name('lab.showstocktake');
Route::get('/lab-stock-forecasting',[LabInventoryController::class,'showLabStockForecasting'])->name('lab.showstock-forecasting');
Route::get('/lab-adjustments',[LabInventoryController::class,'showLabAdjustment'])->name('lab.showstock-adjustment');
Route::get('/lab-disposal',[LabInventoryController::class,'showLabDisposal'])->name('lab.show-disposal');
Route::get('/lab_item_list',[LabInventoryController::class,'itemList'])->name('lab_item.list');
Route::get('/load_item_list',[LabInventoryController::class,'loadItemList'])->name('lab_item.load');
Route::get('/new-receipt',[LabInventoryController::class, 'receiveInventory'])->name('lab.new_receipt');
Route::get('/all_receipts',[LabInventoryController::class, 'allReceivedInventory'])->name('lab.all_received');
Route::get('/lab-received-checklist',[LabInventoryController::class,'checkReceived'])->name('lab.received-checklist');

//__________________________________sections______________________________________________________________//
Route::get('/show-sections',[ProviderController::class, 'showSections'])->name('sections.show');
//________________________________________supplier__________________________________________//
Route::get('/lab-supplier',[SupplierController::class,'addLabSupplier'])->name('lab_supplier.add');
Route::get('/lab-supplier-view',[SupplierController::class,'labSupplierView'])->name('lab_supplier.view');

//_____________________________LabAdmin Items___________________________//
Route::get('/lab_item_create',[LabInventoryController::class,'labcreateNewItem'])->name('lab.item_create');
//_____________________________________lab user__________________________________________________//
Route::get('/lab-user',[UserController::class,'addLabUser'])->name('lab-user.add'); 
Route::get('/lab-view-user',[UserController::class,'showLabUsers'])->name('lab-user.view');
Route::put('/lab_update',[UserController::class,'labUpdateUser'])->name('lab_user.update');
Route::post('/lab_delete_user',[UserController::class,'labUserDelete'])->name('lab_user.destroy');
//________________________profile________________________________________________________//
Route::get('/profile',[UserController::class,'labProfileView'])->name('lab.userprofile');
Route::get('/password',[UserController::class,'labPassword'])->name('lab.password');
Route::get('/signature',[UserController::class,'labSignature'])->name('lab.signature');
Route::post('/initialize',[UserController::class,'InitializeUser'])->name('initializeUserDetails');
//_________________________________suppier_list_____________________________________//
Route::get('/lab-suppliers',[SupplierController::class,'labViewSupplier'])->name('lab_supplier.show');

//___________________________________________stock_take___________________________________//
Route::get('/lab-selected_stock_location',[LabInventoryController::class,'getSelectedStockLocation'])->name('stock.getselected_location');
Route::get('/lab_view_stock',[StockTakeController::class,'labViewStockTaken'])->name('lab_stock.view_history');
//_______________________________________consumptions___________________________________________//
Route::get('/consumption_details',[ConsumptionController::class,'consumptionDetails'])->name('consumption.view_details');
Route::get('/lab_consumption_history',[ConsumptionController::class,'labConsumptionHistory'])->name('lab_consumption.history');
Route::get('/lab_consumption_history_load',[ConsumptionController::class,'loadLabConsumptionHistory'])->name('consumption.history_load');
Route::get('/lab_load_consumption_data',[ConsumptionController::class,'loadConsumptionData'])->name('consumption.update_table_data');


 //_____________________________________LabManager Reports Dashboard_________________________________________________//
Route::get('/lab-consumption-report',[LabManagerReportController::class,'labConsumptionReport'])->name('dashboard.lab_manager_consumption');
Route::get('/inventory-health-modal',[LabManagerReportController::class, 'showHealthModal']) ->name('stats.detail_modal');  
Route::get('/inventory-health-table',[LabManagerReportController::class, 'showHealthTable'])->name('stats.table');
Route::get('/consumption-dashboard',[LabManagerReportController::class,'loadConsumptionTable'])->name('dashboard.consumption');
Route::get('/stock-level-dashboard',[LabManagerReportController::class,'loadStockLevel'])->name('dashboard.lab_manager_stock-level');
Route::get('/requisition-dashboard',[LabManagerReportController::class,'loadRequisition'])->name('dashboard.lab_manager_requisition');
Route::get('/orders-dashboard',[LabManagerReportController::class,'loadOrders'])->name('dashboard.lab_manager_orders');
Route::get('/lab-percentage',[LabManagerReportController::class,'labUsagePercentage'])->name('dashboard.percentage');
Route::get('/period-report',[LabManagerReportController::class, 'reportPeriod'])->name('dashboard.lab_manager_period');
Route::get('/compare-report',[LabManagerReportController::class,'compareReport'])->name('dashboard.lab_manager_compare'); 

//___________________________LAB REPORT TABLES ________________________________________//
Route::get('/lab_show-report',[LabManagerTableReportController::class, 'showLabReport'])->name('lab_manager_reports.show');
Route::get('/expiry-report',[LabManagerTableReportController::class, 'showExpiryReport'])->name('lab_manager_report.expiry');
Route::get('/expiry-table',[LabManagerTableReportController::class, 'loadExpiryTable'])->name('lab_manager_report.expiry_table');
Route::post('/schedule-report/{type}',[LabManagerTableReportController::class, 'scheduleReport'])->name('lab_manager_report.schedule_report');
Route::get('/download-report',[LabManagerTableReportController::class, 'downloadReport'])->name('lab_manager_report.download');
Route::get('/issue-report',[LabManagerTableReportController::class, 'showIssueReport'])->name('lab_manager_issue.report');
Route::get('/issue-table',[LabManagerTableReportController::class, 'loadIssueTable'])->name('lab_manager_report.issue_table');
//__consum---------
Route::get('/consumption-report',[LabManagerTableReportController::class,'showConsumptionReport'])->name('lab_manager_report.consumption');
Route::get('/consumption-load',[LabManagerTableReportController::class,'loadConsumptionTable'])->name('lab_manager_report.consumption_table');
Route::get('/consumption-more',[LabManagerTableReportController::class,'consumptionMoreDetails'])->name('lab_manager_report.loadmore');
Route::get('/consumption-filter',[LabManagerTableReportController::class,'filterConsumption'])->name('lab_manager_report.consumption-filter');
//Route::get('/change-frequency',[LabManagerTableReportController::class,'changeFrequency'])->name('change_frequency');
Route::get('/download-consumption-report',[LabManagerTableReportController::class, 'downloadConsumptionReport'])->name('lab_manager_report.consumptiondownload');
Route::get('/red/{name}',[LabManagerTableReportController::class,'download'])->name('lab_manager_redirect_download');
 //_______requisition
 Route::get('/requisition-report',[LabManagerTableReportController::class,'showRequisitionReport'])->name('lab_manager_requisition.report');
 Route::get('/load-requisition-list',[LabManagerTableReportController::class,'loadRequisitionReport'])->name('lab_manager_report.load-requisition');
 Route::get('/get-requisition-data',[LabManagerTableReportController::class,'getRequestedData'])->name('lab_manager_report.requisition-more-data');
 Route::get('/supplier_order_report',[LabManagerTableReportController::class,'showSupplierOrderReport'])->name('lab_manager_report.supplier-order');
 Route::get('/load-by-location',[LabManagerTableReportController::class,'loadByLocation'])->name('lab_manager_report.load_by_location');
 Route::get('/load-by-period',[LabManagerTableReportController::class,'loadByPeriod'])->name('lab_manager_report.load_by_period');
 //________________supplier orders________/
 Route::get('/load-supplierorders',[LabManagerTableReportController::class,'loadOrdersToSupplier'])->name('lab_manager_report.supplier_order');
 Route::get('/order_by-period',[LabManagerTableReportController::class,'loadOrderByPeriod'])->name('lab_manager_report.supplier_order_by_period');
 Route::get('/order_by-location',[LabManagerTableReportController::class,'loadOrderByLocation'])->name('lab_manager_report.supplier_order_by_location');
 Route::get('/order_by-supplier',[LabManagerTableReportController::class,'loadOrderBySupplier'])->name('lab_manager_report.supplier_order_by_supplier');
 Route::get('/load-more_supplier_orders',[LabManagerTableReportController::class,'getOrderDetails'])->name('lab_manager_report.getorderdetails');
 
 //_______________stock leveL________/
 Route::get('/stock_level',[LabManagerTableReportController::class,'stockLevelReport'])->name('lab_manager_report.stock-level');
 Route::get('/stock-load-level',[LabManagerTableReportController::class,'loadStockLevelReport'])->name('lab_manager_report.load_stock_level');
 Route::get('/stock-level-details',[LabManagerTableReportController::class,'loadStockLevelDetails'])->name('lab_manager_report.stock_level_details');
 //_____out of stock  __________//
 Route::get('/out_of_stock',[LabManagerTableReportController::class,'showOutOfStockReport'])->name('lab_manager_reports.item_out_of_stock');
 Route::get('/load_out_of_stock',[LabManagerTableReportController::class,'loadOutOfStock'])->name('lab_manager_report.load_out_of_stock');
 Route::get('load-stout_out_details',[LabManagerTableReportController::class,'loadOutOfStockDetails'])->name('lab_manager_report.stock_out_details');


            });
//____________________________Section Head________________________________________________//
Route::group(['prefix'=>'SectionHead','middleware'=>'section_head'],function(){
Route::get('SectionHome',[SectionHeadController::class,'home'])->name('section.home');
	
});

Route::group([ 'middleware'=>'user'], function () {

 Route::get('/user',[ClerkController::class,'index'])->name('user.home');  
Route::get('/section-bincard',[SectionInventoryController::class, 'showSectionBinCard'])->name('section.bincard_inventory');
Route::get('/section_consumption',[SectionInventoryController::class, 'showSectionConsumption'])->name('section.showupdate-consumption');
Route::get('/section-stocktake',[SectionInventoryController::class, 'showSectionStockTake'])->name('section.showstocktake');
Route::get('/section-stock-forecasting',[SectionInventoryController::class,'showSectionStockForecasting'])->name('section.showstock-forecasting');
Route::get('/section-adjustments',[SectionInventoryController::class,'showSectionAdjustment'])->name('section.showstock-adjustment');
Route::get('/section-disposal',[SectionInventoryController::class,'showSectionDisposal'])->name('section.show-disposal');
//_______________________________________Section BINCARD_____________________________//
Route::get('/section_load_bincard',[SectionBinCardController::class,'loadSectionBinCard'])->name('section_bincard');

//__________________________________Section Consumption________________________________//
Route::get('section_load_consumption',[SectionConsumptionController::class,'loadSectionConsumption'])->name('section.load-consumption');
//____________________________issues_______________________________________//
Route::get('/section_request',[SectionInventoryController::class, 'showSectionRequest'])->name('section.request');
Route::get('/section_issue',[SectionInventoryController::class,'showSectionIssue'])->name('section.issue');
Route::get('/section_receive_issued',[SectionInventoryController::class,'showSectionReceived'])->name('section.received_issued');

//________________________________Receive_________________________________//
Route::get('/section_new-receipt',[SectionInventoryController::class, 'receiveInventory'])->name('section.new_receipt');
Route::get('/section_all_receipts',[SectionInventoryController::class, 'allReceivedInventory'])->name('section.all_received');
Route::get('/section_load_received',[SectionInventoryController::class,'loadReceivedItems'])->name('section.all_recieved_items');
Route::get('/section_filter',[SectionInventoryController::class,'getSectionReceivedFiltered'])->name('section.items-filtered');
Route::get('/section_search-item',[SectionInventoryController::class,'searchItem'])->name('inventory.section_search');
Route::get('/section-received-checklist',[SectionInventoryController::class,'getReceivedChecklist'])->name('section.received-checklist');

//______________________________StockTake_____________________________________________________//
Route::get('/section_stock',[SectionInventoryController::class, 'loadSectionStockTake'])->name('section_inventory.stocktake');
Route::get('/section-forecast-load',[SectionInventoryController::class,'loadSectionForecastItem'])->name('section-forecast.load');
Route::get('/section_filter_stock',[SectionStockTakeController::class,'filterSectionStockTake'])->name('section.filter_stocktake');


//_____________________________________Stock Adjustment___________________________________________________//
Route::get('/sectionsearch-adjustment',[ConsumptionController::class, 'searchSectionItemAdjustment'])->name('section.search_adjustment');

//__________________________________user dashboard Report______________________________________________//

Route::get('/section-consumption-report',[SectionReportController::class,'labConsumptionReport'])->name('dashboard.section_consumption');

Route::get('/section-consumption-dashboard',[SectionReportController::class,'loadConsumptionTable'])->name('dashboard.section_consumption');
Route::get('/section-stock-level-dashboard',[SectionReportController::class,'loadStockLevel'])->name('dashboard.section_stock-level');
Route::get('/section-requisition-dashboard',[SectionReportController::class,'loadRequisition'])->name('dashboard.section_requisition');
Route::get('/section-orders-dashboard',[SectionReportController::class,'loadOrders'])->name('dashboard.section_orders');
Route::get('/section-percentage',[SectionReportController::class,'labUsagePercentage'])->name('dashboard.percentage');
Route::get('/section-period-report',[SectionReportController::class, 'reportPeriod'])->name('dashboard.section_period');
Route::get('/section-compare-report',[SectionReportController::class,'compareReport'])->name('dashboard.section_compare'); 
            });

Route::group([ 'prefix'=>'ColdRoom','middleware'=>'coldroom'], function () {

Route::get('/home',[ColdRoomController::class,'index'])->name('cold.home');
Route::get('/coldroom-bincard',[ColdRoomBinCardController::class,'showColdRoomBinCard'])->name('cold.bincard_inventory');
Route::get('/cold_consumption',[ColdRoomConsumptionController::class,'showColdRoomConsumption'])->name('cold.showupdate-consumption');
Route::get('/cold-stocktake',[ColdRoomStockTakeController::class,'showColdRoomStockTake'])->name('cold.showstocktake');
Route::get('/cold-disposal',[ColdRoomStockDisposalController::class,'showColdRoomDisposal'])->name('cold.show-disposal');
Route::get('/cold-adjustment',[ColdRoomAdjustmentController::class,'showAdjustment'])->name('cold.showstock-adjustment');
Route::get('/cold_issue',[ColdRoomIssueController::class, 'showColdIssue'])->name('cold.issue');
Route::get('/cold_receive_issued',[ColdRoomIssueController::class,'showColdRoomReceived'])->name('cold.received_issued');
Route::get('/cold_new-receipt',[ColdRoomIssueController::class,'showNewReceipt'])->name('cold.new_receipt');
Route::get('/cold-received',[ColdRoomIssueController::class,'showColdAllReceived'])->name('cold.all-received');
Route::get('/cold-received-status',[ColdRoomIssueController::class,'showReceivedCheckList'])->name('cold.received-checklist');
Route::get('/cold_profile',[ColdRoomController::class,'coldProfile'])->name('cold.profile');
Route::get('/cold_signature',[ColdRoomController::class,'coldSignature'])->name('cold.signature');
Route::get('/cold_password',[ColdRoomController::class,'coldPassword'])->name('cold.password');
});