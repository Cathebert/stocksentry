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
//reports cold
use App\Http\Controllers\ColdRoomReportController;

use App\Http\Controllers\DisposalReportController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ExpiredItemController;
use App\Http\Controllers\SystemMailController;
use App\Http\Controllers\ScheduleReportController;

use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\BackupController;
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

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('config:clear');
    echo 'DONE'; //Return anything

    });
Route::get('/schedule', function () {
    $exitCode = Artisan::call('schedule:run');

    echo "Scheduled";
    });
  Route::get('/',[LoginController::class,'LoginForm'])->name('user.login');


//Auth::routes();
Route::post('/login',[LoginController::class,'adminLogin'])->name('login');
Route::get('/resetEmail',[LoginController::class,'resetPasswordEmail'])->name('password.request');
Route::post('PasswordReset',[LoginController::class,'passwordReset'])->name('password.email');
Route::get('/reset-password/{token}',[LoginController::class,'confirmPassword'])->name('password.reset');
Route::post('/update-password',[LoginController::class,'updatePassword'])->name('password.update');
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
Route::get('/edit_inventory_modal',[InventoryController::class,'editInventoryModal'])->name('inventory.edit_modal');
Route::post('/edit_inventory_save',[InventoryController::class,'saveInventory'])->name('inventory.save_edit');


//_______________________________Contract Management________________________________________//
Route::get('/contract-show',[ContractController::class,'add'])->name('contract.add');
Route::get('/contract-load',[ContractController::class,'load'])->name('contract.load');
Route::get('/contract-add',[ContractController::class,'showModal'])->name('contract.show_modal');
Route::post('/contract-save',[ContractController::class,'saveContract'])->name('contract.save');
Route::post('/sub-type',[ContractController::class,'saveSubscriptionType'])->name('sub.type');
Route::get('/view-contract',[ContractController::class,'viewContract'])->name('contract.view');
Route::get('/edit-contract',[ContractController::class,'editContract'])->name('contract.edit');
Route::post('/save_edited-contract',[ContractController::class,'saveEditContract'])->name('contract.save_edit');
Route::get('/update-contract',[ContractController::class,'updateContract'])->name('contract.update');
Route::post('/keepup-contract',[ContractController::class,'keepupContract'])->name('contract.keepup');
Route::get('/filter-contract',[ContractController::class,'filterContract'])->name('contract.filter');
Route::post('/contract-delete',[ContractController::class,'deleteContract'])->name('contract.delete');
Route::get('/contract-download',[ContractController::class,'downloadContract'])->name('contract.download');
Route::get('/contract-excel',[ContractController::class,'downloadContractExcel'])->name('contract.excel');

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
Route::post('/requisition_remove',[RequisitionController::class,'removeRequisition'])->name('remove.requisition');

//system mAILS
Route::get('system_mails',[SystemMailController::class,'show'])->name('system_mails');
Route::get('load_system',[SystemMailController::class,'load'])->name('mail.load');

//_____________________schedule reports______________________//
Route::get('scheduled_reports',[ScheduleReportController::class,'show'])->name('scheduled.show');


Route::get('scheduled_load',[ScheduleReportController::class,'load'])->name('scheduled.load');
Route::post('save_scheduled',[ScheduleReportController::class,'save'])->name('scheduled.save');
Route::post('deactivate_schedule',[ScheduleReportController::class,'deactivate'])->name('scheduled.deactivate');
Route::post('delete_schedule',[ScheduleReportController::class,'delete'])->name('scheduled.delete');
//___________________Backup________________________________________//
Route::get('back_ups',[BackupController::class,'showBackUp'])->name('backup.show');
Route::post('schedule_backup',[BackupController::class,'scheduleBackUp'])->name('backup.schedule');
Route::get('back_ups_load',[BackupController::class,'loadSheduledBackups'])->name('scheduled_backups.load');
Route::post('deactivate_backup',[BackupController::class,'deactivate'])->name('backup.deactivate');
Route::post('delete_backup',[BackupController::class,'delete'])->name('backup.delete');

Route::get('view_generated',[BackupController::class,'viewBackUps'])->name('backups.showmodal');
Route::get('load_generated',[BackupController::class,'loadBackups'])->name('backups.load_created');
Route::post('generate_backup',[BackupController::class,'generateBackup'])->name('backups.generate');
Route::get('backup_download/{name}',[BackupController::class,'downloadBackupFile'])->name('sy_backups.download');
Route::get('download_backedup',[BackupController::class,'createDownload'])->name('backups.download');
Route::post('backup_delete',[BackupController::class,'deleteBackup'])->name('delete.backedup');
Route::post('backup_clear',[BackupController::class,'clearAllBackup'])->name('backups.clear_all');
Route::post('backup_restore',[BackupController::class,'restoreBackup'])->name('backups.restore');


//___________________Backup________________________________________//

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
Route::get('/view_order-details',[ForecastController::class,'viewOrderDetailsApproval'])->name('orders.view');
Route::get('/order-details_recieve',[ForecastController::class,'showOrderDetailReceived'])->name('order.show_received');
Route::get('/orders-details_r',[ForecastController::class,'loadOrdersDetailsReceived'])->name('order.load_received');
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
Route::post('/deny_pending_approve',[ForecastController::class,'declinePendingOrder'])->name('forecast.mark_denied');
Route::post('/orderpurchase_save',[ForecastController::class,'savePurchaseNumber'])->name('order.savepurchase');
Route::post('/order_mark_received',[ForecastController::class,'markReceivedAll'])->name('order.mark_all_received');
Route::post('/order_mark_no_number',[ForecastController::class,'markReceivedWithoutPurchaseNumber'])->name('order.mark_without_number');



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
Route::get('/issue-filter',[ReportController::class,'issueFilter'])->name('report.issue_filter');
Route::get('/issue-table',[ReportController::class, 'loadIssueTable'])->name('report.issue_table');
Route::get('/consumption-report',[ReportController::class,'showConsumptionReport'])->name('report.consumption');
Route::get('/consumption-load',[ReportController::class,'loadConsumptionTable'])->name('report.consumption_table');
Route::get('/consumption-more',[ReportController::class,'consumptionMoreDetails'])->name('report.loadmore');
Route::get('/consumption-filter',[ReportController::class,'filterConsumption'])->name('report.consumption-filter');
Route::get('/change-frequency',[ReportController::class,'changeFrequency'])->name('change_frequency');
Route::get('/lab_selected_consumption',[ReportController::class,'labSelectedConsumption'])->name('report.lab_consumed_selected');

Route::get('/download-consumption-report',[ReportController::class, 'downloadConsumptionReport'])->name('report.consumptiondownload');
Route::get('/red/{name}',[ReportController::class,'download'])->name('redirect_download');
Route::get('/consumed-excel/{name}',[ReportController::class,'excelDownload'])->name('report.consumed_download-excel');
Route::get('/stock_level_download/{name}',[ReportController::class,'stockLevelDownload'])->name('report.stock_level_download');
//disposal
Route::get('/disposal',[DisposalReportController::class,'showDisposal'])->name('report.stock-disposal');
Route::get('/load_disposal',[DisposalReportController::class,'loadDisposal'])->name('report.disposed_table');
Route::get('/loaddisposebylab',[DisposalReportController::class,'loadByLab'])->name('report.disposedbylab');
Route::get('/loaddisposebyrange',[DisposalReportController::class,'loadByRange'])->name('report.disposedbyrange');

//_______requition
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

//-----------------------------------Variance report---------------//
 Route::get('/varianceReport',[ReportController::class,'showVarianceReport'])->name('reports.variance');
 Route::get('/showVarianceReport',[ReportController::class,'loadVariance'])->name('report.variance_table');
 Route::get('/showVarianceDetails',[ReportController::class,'VarianceDetails'])->name('report.variance_details');
 Route::get('/showVarianceByLab',[ReportController::class,'VarianceByLab'])->name('report.variance_lab');
 Route::get('/loadVarianceByLab',[ReportController::class,'loadVarianceByLab'])->name('report.load_variance_lab');


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
Route::get('/deleted_items',[ItemController::class,'deletedItems'])->name('item.deleted');
Route::get('/load_deleted_items',[ItemController::class,'loadDeletedItems'])->name('items.load_deleted');
Route::post('/restore',[ItemController::class, 'restoreItem'])->name('item.restore');



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
Route::get('/supplier/{type}',[SupplierController::class,'downloadSupplier'])->name('supplier.download');
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
Route::get('/user_download',[UserController::class,'downloadUser'])->name('user.download');
Route::get('/user_filter',[UserController::class, 'filterUsers'])->name('user.filter');
Route::get('/get_user_pdf/{name}',[UserController::class,'getUserPDFDownload'])->name('user.get_user_pdf');
Route::get('/get_user_excel/{name}',[UserController::class,'getUserExcelFile'])->name('user.get_user_excel');
Route::get('/get_deleted',[UserController::class,'showDeletedUsers'])->name('user.deleted');
Route::get('/load_deleted_users',[UserController::class,'loadDeletedUsers'])->name('user.load_deleted');
Route::post('/restore_user',[UserController::class,'restoreUser'])->name('user.restore');


//_____________________Store settings______________________________//
Route::get('/config_settings',[UserSettingController::class,'show'])->name('settings.setting');
Route::post('/notification_approvals',[UserSettingController::class,'approve'])->name('setting.approvals');
Route::get('/load-setting',[UserSettingController::class,'load'])->name('setting.load');
Route::post('/remove-user',[UserSettingController::class,'remove'])->name('setting.remove');

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
Route::get('/show-adjustment',[ConsumptionController::class, 'showItemAdjustmentModal'])->name('inventory.adjust_show');

Route::get('/adjust-list',[ConsumptionController::class,'adjustmentList'])->name('inventory.adjustment_list');
Route::get('/adjustment-selected',[ConsumptionController::class,'selectedForAdjustment'])->name('inventory.selected_adjustment');
Route::get('/adjustment_load_selected',[ConsumptionController::class, 'loadSelectedAdjusted'])->name('inventory.adjusted_item');
Route::get('/show_adjusted',[ConsumptionController::class, 'viewSelectedAdjustment'])->name('inventory.show_adjusted');
Route::post('/approve_adjustedbulk',[ConsumptionController::class, 'approveBulkAdjustment'])->name('inventory.adjust_bulk');
Route::post('/cancel_adjustedbulk',[ConsumptionController::class, 'cancelBulkAdjusted'])->name('inventory.cancel_bulk');

//___________________________________________Item Disposals_____________________________________________//
Route::post('/run-disposal',[ItemDisposalController::class,'runItemDisposal'])->name('inventory.run-disposal');
Route::get('/disposal-list',[ItemDisposalController::class,'showDisposalList'])->name('disposal.list');
Route::get('/disposal-load',[ItemDisposalController::class,'loadDisposedItems'])->name('disposal.load');
Route::get('/disposal_view',[ItemDisposalController::class,'viewDisposal'])->name('disposal.viewmodal');
Route::get('/disposed-load-items',[ItemDisposalController::class,'getDisposedItem'])->name('disposal.load_data');
Route::post('/disposal-approve',[ItemDisposalController::class,'approveDisposedItem'])->name('disposal.approve');
Route::post('/disposal-cancel',[ItemDisposalController::class,'cancelDisposedItem'])->name('disposal.cancel');

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
Route::get('/user_help',[HelpController::class,'userHelp'])->name('user_help');
Route::get('/cold_help',[HelpController::class,'coldHelp'])->name('cold_help');

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
Route::get('/lab_load_user',[UserController::class,'loadLabUsers'])->name('lab_user.load');

//

Route::get('/lab_edit_user',[UserController::class,'labEditUser'])->name('lab_user.edit');
Route::post('/lab_user_delete', [UserController::class, 'labUserDelete'])->name('lab_user.destroy');
Route::post('/lab_user_update', [UserController::class, 'labUserUpdate'])->name('lab_user.update');
Route::post('/lab_user_reset',[UserController::class,'labUserResetPassword'])->name('lab_user.reset');
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
Route::get('/item_details',[LabManagerReportController::class, 'itemDetails'])->name('dashboard.item_details');
Route::get('/top_consumed',[LabManagerReportController::class, 'getTopConsumed'])->name('dashboard.top_consumed');
Route::get('/latest_orders',[LabManagerReportController::class, 'getLatestOrders'])->name('dashboard.latestOrders');

//___________________________LAB REPORT TABLES ________________________________________//
Route::get('/lab_show-report',[LabManagerTableReportController::class, 'showLabReport'])->name('lab_manager_reports.show');
Route::get('/expiry-report',[LabManagerTableReportController::class, 'showExpiryReport'])->name('lab_manager_report.expiry');
Route::get('/expired-report',[LabManagerTableReportController::class, 'showExpiredReport'])->name('lab_manager_report.expired');
Route::get('/expired-table',[LabManagerTableReportController::class, 'loadExpired'])->name('lab_manager_report.expired_table');
Route::get('/load_by_range',[LabManagerTableReportController::class, 'loadExpiredItemByRange'])->name('lab_manager_report.expiredbyrange');
Route::get('/expiry-table',[LabManagerTableReportController::class, 'loadExpiryTable'])->name('lab_manager_report.expiry_table');
Route::post('/schedule-report/{type}',[LabManagerTableReportController::class, 'scheduleReport'])->name('lab_manager_report.schedule_report');
Route::get('/download-report',[LabManagerTableReportController::class, 'downloadReport'])->name('lab_manager_report.download');
Route::get('/download_expired',[LabManagerTableReportController::class,'downloadExpired'])->name('lab_manager_report.expired_download');
Route::get('/download_about_expire',[LabManagerTableReportController::class,'downloadAboutToExpire'])->name('lab_manager_report.about_expiry_download');
Route::get('/stock_level_lab_download/{name}',[LabManagerTableReportController::class,'stockLevelDownload'])->name('lab_manager_report.stock_level_download');
Route::get('/out_of_stock_lab_download/{name}',[LabManagerTableReportController::class,'downloadOutOfStock'])->name('lab_manager_report.out_of_stock_download');
Route::get('/lab_show_disposal',[LabManagerTableReportController::class,'showDisposal'])->name('lab_manager_report.show-disposed');
Route::get('/loaddisposebyperiod',[LabManagerTableReportController::class,'filterByPeriod'])->name('lab_manager_report.disposedbyrange');
Route::get('/lab_load_disposal',[LabManagerTableReportController::class,'loadDisposal'])->name('lab_manager_report.disposed_table');
Route::get('/lab_download_disposed',[LabManagerTableReportController::class,'labDownloadDisposed'])->name('lab_manager_report.downloaddisposed');
 Route::get('show_stock_take',[LabManagerTableReportController::class,'showStockVariance'])->name('lab_manager_report.variance');



//disposal

Route::get('/issue-report',[LabManagerTableReportController::class, 'showIssueReport'])->name('lab_manager_issue.report');
Route::get('/issue-table',[LabManagerTableReportController::class, 'loadIssueTable'])->name('lab_manager_report.issue_table');
Route::get('/lab_issue_download/{name}',[LabManagerTableReportController::class,'labIssueDownload'])->name('lab_manager_report_issue.download');
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

//system mAILS
Route::get('labsystem_mails',[SystemMailController::class,'showlabMails'])->name('lab.system_mails');
Route::get('load_system',[SystemMailController::class,'loadLabMail'])->name('lab.mail.load');

//schedule
Route::get('labscheduled_reports',[ScheduleReportController::class,'labshow'])->name('labscheduled.show');
Route::get('labscheduled_load',[ScheduleReportController::class,'labload'])->name('labscheduled.load');

Route::get('/lab_settings',[UserSettingController::class,'labSetting'])->name('labsetting.setting');

            });
//____________________________Section Head________________________________________________//
Route::group(['prefix'=>'SectionHead','middleware'=>'section_head'],function(){
Route::get('SectionHome',[SectionHeadController::class,'home'])->name('section.home');

});

Route::group([ 'prefix'=>'LabUser','middleware'=>'user'], function () {

 Route::get('/user',[ClerkController::class,'index'])->name('user.home');
Route::get('/user-bincard',[SectionInventoryController::class, 'showSectionBinCard'])->name('section.bincard_inventory');
Route::get('/user_consumption',[SectionInventoryController::class, 'showSectionConsumption'])->name('section.showupdate-consumption');
Route::get('/user-stocktake',[SectionInventoryController::class, 'showSectionStockTake'])->name('section.showstocktake');
Route::get('/user-stocktakeHistory',[SectionStockTakeController::class,'showUsertockTakeHistory'])->name('section.view_history');
Route::get('/user-stock-forecasting',[SectionInventoryController::class,'showSectionStockForecasting'])->name('section.showstock-forecasting');
Route::get('/user-adjustments',[SectionInventoryController::class,'showSectionAdjustment'])->name('section.showstock-adjustment');
Route::get('/user-disposal',[SectionInventoryController::class,'showSectionDisposal'])->name('section.show-disposal');

//__________________________________________USER PROFILE______________________________//
Route::get('/profile',[UserController::class,'labUserProfileView'])->name('labuser.userprofile');
Route::get('/password',[UserController::class,'labUserPassword'])->name('labuser.password');
Route::get('/signature',[UserController::class,'labUserSignature'])->name('labuser.signature');
//Route::post('/lab_user_update', [UserController::class, 'UserUpdate'])->name('user.update');
//_______________________________________Section BINCARD_____________________________//
Route::get('/user_load_bincard',[SectionBinCardController::class,'loadSectionBinCard'])->name('section_bincard');

//__________________________________Section Consumption________________________________//
Route::get('user_load_consumption',[SectionConsumptionController::class,'loadSectionConsumption'])->name('section.load-consumption');
//____________________________issues_______________________________________//
Route::get('/user_request',[SectionInventoryController::class, 'showSectionRequest'])->name('section.request');
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


//___________________________user REPORT TABLES ________________________________________//
Route::get('/user_show-report',[SectionReportController::class, 'showUserReport'])->name('user_reports.show');
Route::get('/user_expired-report',[SectionReportController::class, 'showUserExpiredReport'])->name('user_report.expired');
Route::get('/user_expiry-report',[SectionReportController::class, 'showUserExpiryReport'])->name('user_report.expiry');
Route::get('/user_consumption-report',[SectionReportController::class,'showUserConsumptionReport'])->name('user_report.consumption');
Route::get('/user_stock_level',[SectionReportController::class,'stockLevelReport'])->name('user_report.stock-level');
Route::get('/user_out_of_stock',[SectionReportController::class,'showOutOfStockReport'])->name('user_reports.item_out_of_stock');
Route::get('/user_show_disposal',[SectionReportController::class,'showDisposal'])->name('user_report.show-disposed');
Route::get('/user_issue-report',[SectionReportController::class, 'showIssueReport'])->name('user_issue.report');
Route::get('/user_requisition-report',[SectionReportController::class,'showRequisitionReport'])->name('user_requisition.report');
Route::get('/user_supplier_order_report',[SectionReportController::class,'showSupplierOrderReport'])->name('user_report.supplier-order');
Route::get('/user_stock_variance_report',[SectionReportController::class, 'showStockVarianceReport'])->name('user_report.variance');

            });

Route::group([ 'prefix'=>'ColdRoom','middleware'=>'coldroom'], function () {

Route::get('/home',[ColdRoomController::class,'index'])->name('cold.home');
Route::get('/coldroom-bincard',[ColdRoomBinCardController::class,'showColdRoomBinCard'])->name('cold.bincard_inventory');
Route::get('/cold_consumption',[ColdRoomConsumptionController::class,'showColdRoomConsumption'])->name('cold.showupdate-consumption');
Route::get('/cold-stocktake',[ColdRoomStockTakeController::class,'showColdRoomStockTake'])->name('cold.showstocktake');
Route::get('/cold-stocktakeHistory',[ColdRoomStockTakeController::class,'showColdRoomStockTakeHistory'])->name('cold_stock.view_history');
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
//______________________________reports___________________________________________________//
Route::get('/room-reports',[ColdRoomController::class,'showReport'])->name('cold_reports.show');
Route::get('/room-expired',[ColdRoomController::class,'showExpired'])->name('cold_report.expired');
Route::get('/room_about_expiry',[ColdRoomController::class,'showAboutToExpire'])->name('cold_report.expiry');
Route::get('/room_consumption',[ColdRoomController::class,'showConsumptionReport'])->name('cold_report.consumption');
Route::get('/room_stock_level',[ColdRoomController::class,'showStockLevelReport'])->name('cold_report.stock-level');
Route::get('/room_out_of_stock',[ColdRoomController::class,'showOutOfStock'])->name('cold_report.item_out_of_stock');
Route::get('/room_item_disposed',[ColdRoomController::class,'showDisposed'])->name('cold_report.show-disposed');
Route::get('/room_issue_report',[ColdRoomController::class,'showIssue'])->name('cold_report.show-issue');
Route::get('/room_stock_level_lab',[ColdRoomController::class,'showLabStockLevel'])->name('cold.stock_level_selected');
Route::get('/room_stock_variance',[ColdRoomController::class, 'showStockVariance'])->name('cold_report.variance');

//_______________________________coldroom expired______________________________//
Route::get('/expired-table',[ColdRoomReportController::class, 'loadColdRoomExpired'])->name('coldroom_report.expired_items');
Route::get('/load_by_filtered',[ColdRoomReportController::class, 'loadExpiredFiltered'])->name('coldroom_report.expiredbyfiltered');
Route::get('/download_expired',[ColdRoomReportController::class,'downloadExpired'])->name('coldroom_report.expired_download');
///_____________________schedule report______________________________//
Route::get('/room_schedule',[ScheduleReportController::class,'Coldshow'])->name('cold_report.scheduled');
//_______________________setting_____________________________________//
Route::get('/cold_settings',[UserSettingController::class,'coldSetting'])->name('coldsetting.setting');
});
