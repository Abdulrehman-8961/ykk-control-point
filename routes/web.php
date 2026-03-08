<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/laravel/artisans/{command}', function ($command) {
  $ext = Artisan::call($command);
  echo $ext;
})->middleware('auth');



Route::get('/privacy-policy', 'HomeController@PrivacyPolicy');


Route::get('/contact-us', 'HomeController@ContactUs');
Route::get('/error', 'HomeController@error');
Route::get('/CheckContractStatus', 'HomeController@CheckContractStatus');
Route::get('/CheckSSLStatus', 'HomeController@CheckSSLStatus');


Route::post('/insert-contact-us', 'HomeController@InsertContactUs');
Route::get('/SharinglinkContract', 'ContractController@SharinglinkContract');
Route::get('/error', 'HomeController@Error');

Route::group(['middleware' => ['auth', 'statuscheck']], function () {
  Route::post('/save-notification-emails', 'AdminController@saveNotificationEmails');
  Route::post('/save-emails', 'AdminController@saveEmails');
  Route::get('/get-emails', 'AdminController@getEmails');
  Route::get('/get-notification-emails', 'AdminController@getNotificationEmails');



  Route::get('/home', 'AdminController@index');

  Route::get('/', 'AdminController@index');
  Route::get('dashboards/top-failures/workorders', 'AdminController@byWorkorder');
  Route::get('dashboards/top-failures/itemcategories', 'AdminController@byitemcategory');
  Route::get('dashboards/top-failures/assets', 'AdminController@byassets');
  Route::get('dashboards/top-failures/tests', 'AdminController@bytests');
  Route::get('dashboards/historical-sample-tests', 'AdminController@historical_sample_tests');

  Route::post('/update-system-settings', 'MainController@UpdateSystemSettings');


  Route::get('/get-province', 'MainController@GetProvince');


  Route::get('/taxes', 'MainController@Taxes');
  Route::post('/update-system-settings', 'MainController@UpdateSystemSettings');
  Route::post('/insert-tax', 'MainController@InsertTax');
  Route::post('/update-tax', 'MainController@UpdateTax');
  Route::get('/get-tax-content', 'MainController@getTaxContent');
  Route::get('/get-tax-edit-content', 'MainController@getTaxEditContent');
  Route::get('/delete-tax', 'MainController@DeleteTax');
  Route::get('/undo-delete-tax', 'MainController@UndoDeleteTax');
  Route::get('/get-comments-tax', 'MainController@getCommentsTax');
  Route::get('/get-attachment-tax', 'MainController@getAttachmentTax');
  Route::post('/end-tax', 'MainController@EndTax');

  Route::get('/source', 'MainController@Source');
  Route::get('/source-validate', 'MainController@SourceValidate');
  Route::post('/insert-source', 'MainController@InsertSource');
  Route::post('/update-source', 'MainController@UpdateSource');
  Route::get('/get-source-content', 'MainController@getSourceContent');
  Route::get('/get-source-edit-content', 'MainController@getSourceEditContent');
  Route::get('/delete-source', 'MainController@DeleteSource');
  Route::get('/undo-delete-source', 'MainController@UndoDeleteSource');
  Route::get('/get-comments-source', 'MainController@getCommentsSource');
  Route::get('/get-attachment-source', 'MainController@getAttachmentSource');
  Route::post('/end-source', 'MainController@EndSource');

  Route::get('/gifi', 'MainController@Gifi');
  Route::post('/insert-gifi', 'MainController@InsertGifi');
  Route::post('/update-gifi', 'MainController@UpdateGifi');
  Route::get('/get-gifi-content', 'MainController@getGifiContent');
  Route::get('/get-gifi-edit-content', 'MainController@getGifiEditContent');
  Route::get('/delete-gifi', 'MainController@DeleteGifi');
  Route::get('/undo-delete-gifi', 'MainController@UndoDeleteGifi');
  Route::get('/get-comments-gifi', 'MainController@getCommentsGifi');
  Route::get('/get-attachment-gifi', 'MainController@getAttachmentGifi');
  Route::post('/end-gifi', 'MainController@EndGifi');
  Route::get('/get-sub-account', 'MainController@getSubAccount');
  Route::get('/check-gifi', 'MainController@checkGifi');
  Route::get('/get-gifi-account', 'MainController@getGifiAccounts');
  Route::post('/import-excel-gifi', 'MainController@ImportExcelGIFI');
  Route::get('/export-gifi', 'MainController@ExportGifi');
  Route::get('export-excel-clients2', 'MainController@ExportClientGifi');

  // Assets
  Route::get('/assets', 'MainController@assets');
  Route::post('/insert-assets', 'MainController@insertAssets');
  Route::post('/delete-assets', 'MainController@delete_assets');
  Route::get('/undo-delete-assets', 'MainController@UndoDeleteAssets');
  Route::post('/get-assets', 'MainController@get_assets');
  Route::post('/update-assets', 'MainController@updateAssets');

  Route::post('/check-asset-no', function (\Illuminate\Http\Request $request) {
    $asset_no = $request->input('asset_no');
    $exists = DB::table('assets')->where('asset_no', $asset_no)->where('is_deleted', 0)->exists();
    return response()->json(['exists' => $exists]);
  });

  Route::get('/get-assets-content', 'MainController@getAssetsContent');
  Route::post('/import-assets', 'MainController@ImportAssets');
  Route::get('/export-assets', 'MainController@ExportAssets');
  Route::post('/end-assets', 'MainController@EndAssets');
  Route::get('/get-comments-assets', 'MainController@getCommentsAssets');
  Route::post('/insert-comments-assets', 'MainController@insertCommentsAssets');
  Route::post('/update-comments-assets', 'MainController@update_assets_comment');
  Route::post('/delete-comment', 'MainController@delete_assets_comment');
  Route::get('/undo-delete-assets-comment', 'MainController@UndoDeleteAssetsComment');

  Route::get('/LoadAssetsAttachment', 'MainController@LoadAssetsAttachment');
  Route::post('/uploadAssetsAttachment', 'MainController@uploadAssetsAttachment');
  Route::delete('/revertAssetsAttachment', 'MainController@revertAssetsAttachment');
  Route::post('/insert-attachment-assets', 'MainController@insert_attachment_assets');
  Route::post('/delete-assets-attachment', 'MainController@delete_assets_attachment');
  Route::get('/undo-delete-assets-attachment', 'MainController@UndoDeleteAssetsAttachment');
  // End

  // Itemcodes
  Route::get('/itemcodes', 'MainController@itemcodes');
  Route::get('/get-itemcode-content', 'MainController@getItemcodeContent');
  Route::post('/import-excel-itemcodes', 'MainController@ImportExcelItemcodes');
  Route::get('/export-itemcodes', 'MainController@ExportItemcodes');
  Route::post('/end-itemcode', 'MainController@EndItemcode');
  Route::post('/itemcode-color-img', 'MainController@itemcode_color_img');
  Route::get('/get-comments-itemcode', 'MainController@getCommentsItemcode');
  Route::post('/insert-comments-itemcode', 'MainController@insertCommentsItemcode');
  Route::post('/update-comments-itemcode', 'MainController@update_itemcode_comment');
  Route::post('/delete-comment', 'MainController@delete_itemcode_comment');
  Route::get('/undo-delete-itemcode-comment', 'MainController@UndoDeleteItemcodeComment');

  Route::get('/LoadItemcodeAttachment', 'MainController@LoadItemcodeAttachment');
  Route::post('/uploadItemcodeAttachment', 'MainController@uploadItemcodeAttachment');
  Route::delete('/revertItemcodeAttachment', 'MainController@revertItemcodeAttachment');
  Route::post('/insert-attachment-itemcode', 'MainController@insert_attachment_itemcode');
  Route::post('/delete-itemcode-attachment', 'MainController@delete_itemcode_attachment');
  Route::get('/undo-delete-itemcode-attachment', 'MainController@UndoDeleteItemcodeAttachment');

  Route::get('/check-item-category', 'MainController@checkItemcodeCategory_new');

  // End

  // WorkOrders
  Route::get('/work-orders', 'MainController@work_orders');
  Route::get('/get-workorder-content', 'MainController@getWorkorderContent');
  Route::post('/import-excel-workorders', 'MainController@ImportExcelWorkorders');
  Route::get('/export-workorders', 'MainController@ExportWorkorders');
  Route::post('/end-workorder', 'MainController@EndWorkorder');
  Route::get('/get-comments-workorder', 'MainController@getCommentsWorkorder');
  Route::post('/insert-comments-workorder', 'MainController@insertCommentsWorkorder');
  Route::post('/update-comments-workorder', 'MainController@update_workorder_comment');
  Route::post('/delete-comment', 'MainController@delete_workorder_comment');
  Route::get('/undo-delete-workorder-comment', 'MainController@UndoDeleteWorkorderComment');

  Route::get('/LoadWorkorderAttachment', 'MainController@LoadWorkorderAttachment');
  Route::post('/uploadWorkorderAttachment', 'MainController@uploadWorkorderAttachment');
  Route::delete('/revertWorkorderAttachment', 'MainController@revertWorkorderAttachment');
  Route::post('/insert-attachment-workorder', 'MainController@insert_attachment_workorder');
  Route::post('/delete-workorder-attachment', 'MainController@delete_workorder_attachment');
  Route::get('/undo-delete-workorder-attachment', 'MainController@UndoDeleteWorkorderAttachment');
  // End

  // Item Categories

  // web.php
  Route::post('/check-itemcode-category', 'MainController@checkItemcodeCategory');


  Route::get('/item-categories', 'MainController@item_categories');
  Route::post('/insert-item-categories', 'MainController@insertItemCategories');
  Route::post('/delete-item-category', 'MainController@delete_item_category');
  Route::get('/undo-delete-item-category', 'MainController@UndoDeleteItemCategory');
  Route::post('/fetch-itemcodes', 'MainController@fetchItemcodes');
  Route::get('/fetch-itemcodes-2', 'MainController@fetchItemcodes_');
  Route::post('/get-item-category', 'MainController@get_item_category');
  Route::post('/update-item-categories', 'MainController@updateItemCategories');

  Route::post('/check-item-category', function (\Illuminate\Http\Request $request) {
    $item_category = $request->input('item_category');
    $exists = DB::table('item_categories')->where('item_category', $item_category)->where('is_deleted', 0)->exists();
    return response()->json(['exists' => $exists]);
  });

  Route::get('/get-item-categories-content', 'MainController@getItemCategoriesContent');
  Route::post('/import-item-categories', 'MainController@ImportItemCategories');
  Route::get('/export-item-categories', 'MainController@ExportItemCategories');
  Route::post('/end-item-categories', 'MainController@EndItemCategories');
  Route::get('/get-comments-item-categories', 'MainController@getCommentsItemCategories');
  Route::post('/insert-comments-item-categories', 'MainController@insertCommentsItemCategories');
  Route::post('/update-comments-item-categories', 'MainController@update_ItemCategories_comment');
  Route::post('/delete-comment-item-categories', 'MainController@delete_ItemCategories_comment');
  Route::get('/undo-delete-item-categories-comment', 'MainController@UndoDeleteItemCategoriesComment');

  Route::get('/LoadItemCategoriesAttachment', 'MainController@LoadItemCategoriesAttachment');
  Route::post('/uploadItemCategoriesAttachment', 'MainController@uploadItemCategoriesAttachment');
  Route::delete('/revertItemCategoriesAttachment', 'MainController@revertItemCategoriesAttachment');
  Route::post('/insert-attachment-item-categories', 'MainController@insert_attachment_item_categories');
  Route::post('/delete-item-categories-attachment', 'MainController@delete_item_categories_attachment');
  Route::get('/undo-delete-item-categories-attachment', 'MainController@UndoDeleteItemCategoriesAttachment');

  // End

  // Sample Test
  Route::get('/sample-tests', 'MainController@sample_tests');
  Route::get('/sample-tests-optimzed', function () {
    return view('sample-tests-optimzed');
  });
  Route::get('/sample-tests-ipad', function () {
    return view('sample-tests-optimzed-vertical');
  });
  Route::get('/select2_test', 'MainController@select2_test')->name('select2_test');
  Route::get('/fetch_workorders', 'MainController@fetch_workorders')->name('fetch_workorders');
  Route::get('/fetch_workorders_edit', 'MainController@fetch_workorders_edit');
  Route::get('/fetch_itemcode', 'MainController@fetch_itemcode')->name('fetch.items.code');
  Route::post('/fetch-historical', 'MainController@fetchHistorical');
  Route::post('/fetch-assets', 'MainController@fetch_assets');
  Route::post('/fetch-users', 'MainController@fetch_users');
  Route::post('/fetch-threshold', 'MainController@fetch_threshold');
  Route::post('/insert-sample-test', 'MainController@insert_sample_test');
  Route::post('/insert-sample-test-sample', 'MainController@insert_sample_test_samples');
  Route::post('/update-sample-test', 'MainController@update_sample_test');
  Route::post('/update-sample-test-sample', 'MainController@update_sample_test_samples');
  Route::get('/get-sample-test-content', 'MainController@get_sample_test_content');
  Route::post('/delete-sample-test', 'MainController@delete_sample_test');
  Route::get('/undo-delete-sample-test', 'MainController@undo_delete_sample_test');
  Route::post('/get-sample-test', 'MainController@get_sample_test');

  Route::get('/get-comments-sample-test', 'MainController@get_comments_sample_test');
  Route::post('/insert-comment-sample-test', 'MainController@insert_comment_sample_test');
  Route::post('/update-comment-sample-test', 'MainController@update_comment_sample_test');
  Route::post('/delete-comment-sample-test', 'MainController@delete_comment_sample_test');
  Route::get('/undo-delete-comment-sample-test', 'MainController@undo_delete_comment_sample_test');

  Route::post('/uploadSampleTestAttachment', 'MainController@uploadSampleTestAttachment');
  Route::get('/LoadSampleTestAttachment', 'MainController@LoadSampleTestAttachment');
  Route::delete('/revertSampleTestAttachment', 'MainController@revertSampleTestAttachment');

  Route::post('/insert-attachment-sample-test', 'MainController@insert_attachment_sample_test');
  Route::post('/delete-attachment-sample-test', 'MainController@delete_attachment_sample_test');
  Route::get('/undo-delete-attachment-sample-test', 'MainController@undo_delete_attachment_sample_test');

  Route::post('/import-sample-tests', 'MainController@import_sample_tests');
  Route::get('/export-sample-tests', 'MainController@export_sample_tests');

  Route::post('/import-sample-tests', 'MainController@import_sample_tests')->name('sample-tests.import');

  Route::get('/import-sample-tests/report/{file}', 'MainController@downloadImportReport')->name('sample-tests.import.report');

  // Route::post('/update-test-threshold', 'MainController@update_test_threshold');
  // Route::post('/get-test-threshold', 'MainController@get_test_threshold');



  // End

  // Test Thresholds
  Route::get('/update-thresholds', 'MainController@update_names');
  Route::get('/test-thresholds', 'MainController@test_thresholds');
  Route::post('/insert-test-threshold', 'MainController@insert_test_threshold');
  Route::post('/update-test-threshold', 'MainController@update_test_threshold');
  Route::post('/fetch-test-names', 'MainController@fetch_test_names');
  Route::get('/fetch-item-categories', 'MainController@fetch_item_categories_');
  Route::get('/fetch-test-name', 'MainController@fetch_item_categories_');
  Route::get('/fetch-itemcode-size', 'MainController@fetch_item_code_size');
  Route::get('/fetch-itemcode-chaincode', 'MainController@fetch_item_chain_code');
  Route::get('/fetch-item-code', 'MainController@fetch_item_code');
  Route::post('/update-test-threshold', 'MainController@update_test_threshold');
  Route::post('/delete-test-threshold', 'MainController@delete_test_threshold');
  Route::get('/undo-delete-test-threshold', 'MainController@undo_delete_test_threshold');
  Route::post('/get-test-threshold', 'MainController@get_test_threshold');

  Route::post('/check-item-category', function (\Illuminate\Http\Request $request) {
    $item_category = $request->input('item_category');
    $exists = DB::table('item_categories')->where('item_category', $item_category)->where('is_deleted', 0)->exists();
    return response()->json(['exists' => $exists]);
  });

  Route::get('/get-test-threshold-content', 'MainController@get_test_threshold_content');
  Route::post('/import-test-thresholds', 'MainController@import_test_thresholds');
  Route::get('/export-test-thresholds', 'MainController@export_test_thresholds');
  Route::post('/end-test-threshold', 'MainController@end_test_threshold');
  Route::get('/get-comments-test-threshold', 'MainController@get_comments_test_threshold');
  Route::post('/insert-comment-test-threshold', 'MainController@insert_comment_test_threshold');
  Route::post('/update-comment-test-threshold', 'MainController@update_comment_test_threshold');
  Route::post('/delete-comment-test-threshold', 'MainController@delete_comment_test_threshold');
  Route::get('/undo-delete-comment-test-threshold', 'MainController@undo_delete_comment_test_threshold');

  Route::get('/LoadTestThresholdAttachment', 'MainController@LoadTestThresholdAttachment');
  Route::post('/uploadTestThresholdAttachment', 'MainController@uploadTestThresholdAttachment');
  Route::delete('/revertTestThresholdAttachment', 'MainController@revertTestThresholdAttachment');
  Route::post('/insert-attachment-test-threshold', 'MainController@insert_attachment_test_threshold');
  Route::post('/delete-attachment-test-threshold', 'MainController@delete_attachment_test_threshold');
  Route::get('/undo-delete-attachment-test-threshold', 'MainController@undo_delete_attachment_test_threshold');

  // End

  // Users
  Route::get('/users', 'MainController@Users')->middleware('isadmin');
  Route::post('/insert-user', 'MainController@InsertUser')->middleware('isadmin');
  Route::post('/delete-user', 'MainController@delete_user');
  Route::get('/undo-delete-user', 'MainController@UndoDeleteUser');
  Route::post('/update-user', 'MainController@updateUser');
  Route::post('/reset-user-password', 'MainController@resetPassword');

  Route::post('/check-email', function (\Illuminate\Http\Request $request) {
    $email = $request->input('email');
    $exists = DB::table('users')->where('email', $email)->where('is_deleted', 0)->exists();
    return response()->json(['exists' => $exists]);
  });

  Route::get('/get-user-content', 'MainController@get_users_content')->middleware('isadmin');
  Route::post('/get-user', 'MainController@get_user')->middleware('isadmin');
  Route::post('/import-users', 'MainController@ImportUsers');
  Route::get('/export-users', 'MainController@ExportUsers');

  Route::post('/end-user', 'MainController@EndUser');
  Route::get('/get-comments-user', 'MainController@getCommentsUser');
  Route::post('/insert-comments-user', 'MainController@insertCommentsUser');
  Route::post('/update-comments-user', 'MainController@update_User_comment');
  Route::post('/delete-comment-user', 'MainController@delete_User_comment');
  Route::get('/undo-delete-user-comment', 'MainController@UndoDeleteUserComment');

  Route::get('/LoadUserAttachment', 'MainController@LoadUserAttachment');
  Route::post('/uploadUserAttachment', 'MainController@uploadUserAttachment');
  Route::delete('/revertUserAttachment', 'MainController@revertUserAttachment');
  Route::post('/insert-attachment-user', 'MainController@insert_attachment_user');
  Route::post('/delete-user-attachment', 'MainController@delete_user_attachment');
  Route::get('/undo-delete-user-attachment', 'MainController@UndoDeleteUserAttachment');

  // End

  // Test Definitions
  Route::get('/test-definitions', 'MainController@test_definitions');
  Route::post('/insert-test-definition', 'MainController@insert_test_definition');
  Route::post('/update-test-definition', 'MainController@update_test_definition');
  Route::post('/delete-test-definition', 'MainController@delete_test_definition');
  Route::get('/undo-delete-test-definition', 'MainController@undo_delete_test_definition');
  Route::post('/get-test-definition', 'MainController@get_test_definition');

  Route::post('/check-test-name', function (\Illuminate\Http\Request $request) {
    $test_name = $request->input('test_name');
    $exists = DB::table('test_definitions')->where('test_name', $test_name)->where('is_deleted', 0)->exists();
    return response()->json(['exists' => $exists]);
  });

  Route::get('/get-test-definition-content', 'MainController@get_test_definition_content');
  Route::post('/import-test-definitions', 'MainController@import_test_definitions');
  Route::get('/export-test-definitions', 'MainController@export_test_definitions');
  Route::post('/end-test-definition', 'MainController@end_test_definition');
  Route::get('/get-comments-test-definition', 'MainController@get_comments_test_definition');
  Route::post('/insert-comment-test-definition', 'MainController@insert_comment_test_definition');
  Route::post('/update-comment-test-definition', 'MainController@update_comment_test_definition');
  Route::post('/delete-comment-test-definition', 'MainController@delete_comment_test_definition');
  Route::get('/undo-delete-comment-test-definition', 'MainController@undo_delete_comment_test_definition');

  Route::get('/LoadTestDefinitionAttachment', 'MainController@LoadTestDefinitionAttachment');
  Route::post('/uploadTestDefinitionAttachment', 'MainController@uploadTestDefinitionAttachment');
  Route::delete('/revertTestDefinitionAttachment', 'MainController@revertTestDefinitionAttachment');
  Route::post('/insert-attachment-test-definition', 'MainController@insert_attachment_test_definition');
  Route::post('/delete-attachment-test-definition', 'MainController@delete_attachment_test_definition');
  Route::get('/undo-delete-attachment-test-definition', 'MainController@undo_delete_attachment_test_definition');
  // End

  Route::get('/remittances', 'MainController@Remittance');
  Route::post('/insert-remittance', 'MainController@InsertRemittance');
  Route::post('/update-remittance', 'MainController@updateRemittance');
  Route::post('/pre-insert-remittance', 'MainController@PreInsertRemittance');
  Route::get('/remittances/get-clients', 'MainController@getClientsForRemittance');
  Route::get('/get-remittance-content', 'MainController@getRemittanceContent');
  Route::get('/delete-remittance', 'MainController@DeletRemittance');

  Route::get('/get-comments-journal', 'MainController@getCommentsJournals');
  Route::get('/get-attachment-journal', 'MainController@getAttachmentJournals');

  Route::get('/journals', 'MainController@Journals');
  Route::get('/journal/by-source', 'MainController@JournalsBySource');
  Route::get('/journal/by-period', 'MainController@JournalsByPeriod');
  Route::get('/journal/progress-report', 'MainController@JournalsProgressReport');
  Route::get('/journal/trial-balance', 'MainController@trialBalanceReport');
  Route::get('/journal/by-Accounts', 'MainController@JournalsByAccount');
  Route::get('/journal/load-content', 'MainController@JournalsBySourceContent');
  Route::get('/journal/load-content-by-period', 'MainController@JournalsBySourceContentByPeriod');
  Route::get('/journal/load-content-progress-report', 'MainController@JournalsProgressReportContent');
  Route::get('/journal/load-content-traial-balance', 'MainController@JournalsTrialBalance');
  Route::get('/journal/load-extra-sources', 'MainController@JournalsExtraSources');
  Route::get('/journal/load-content-by-account', 'MainController@JournalsBySourceContentByAccount');
  Route::post('/get-sources', 'MainController@getSources');
  Route::post('/get-accounts', 'MainController@getAccounts');
  Route::post('/get-all-years', 'MainController@getAllYears');
  Route::post('/delete-journl', 'MainController@delete_journal');
  Route::post('/undo-delete-journl', 'MainController@undo_delete_journal');
  Route::get('/journal-reports', 'MainController@JournalReports');
  Route::get('/journal-reports/ExportExcelJournalReport', 'MainController@ExportExcelJournalReport');
  Route::get('/journal-reports/get-content', 'MainController@JournalReportsLoadContent');
  // report export
  Route::get('/journal-reports/export', 'MainController@exportJournalReport')->name('journal-reports.export');
  Route::get('/journals/report/trial-balance/get-content', 'MainController@JournalTrialBalanceLoadContent');
  Route::get('/journals/report/trial-balance', 'MainController@JournalTrialBalance');
  Route::get('/journals/report/financial-statement/get-content', 'MainController@JournalFinancialStatementLoadContent');
  Route::get('/journals/report/financial-statement', 'MainController@JournalFinancialStatement');
  Route::get('/journals/report/remittance-status', 'MainController@JournalReportRemittanceStatus');
  Route::get('/remitance/report/remittance-status', 'MainController@RemitanceReportRemittanceStatus');
  Route::get('/journals/report/remittance-status/get-content', 'MainController@JournalReportRemittanceStatusLoadContent');
  Route::get('/remittance/report/remittance-status/get-content', 'MainController@RemittanceReportRemittanceStatusLoadContent');
  Route::get('/update/remittance-status/get-content', 'MainController@updateRemittanceContent');
  Route::post('/mark-as-paid', 'MainController@markAsPaid');
  Route::get('/get-remit-content', 'MainController@getRemitContent');
  Route::get('update-tax-content', 'MainController@updateRemitContent');
  Route::get('/journals/report/progress', 'MainController@JournalReportProgress');
  Route::get('/journals/report/progress/get-content', 'MainController@JournalReportProgressLoadContent');
  Route::get('/delete-journal', 'MainController@DeleteJournalOnReload');
  Route::post('/delete-journal', 'MainController@DeleteJournal');
  Route::get('/undo-delete-journal-on-relaod', 'MainController@UndoDeleteJournalOnReload');
  Route::post('/undo-delete-journal', 'MainController@UndoDeleteJournal');
  Route::post('/journals/undo', 'MainController@UndoAddedJournals');
  Route::get('/get-journal-content', 'MainController@getJournalContent');
  Route::get('/get-journal-edit-content', 'MainController@getJournalEditContent');
  Route::get('/journals/find/get-client-journal-edit-content/{edit_no}', 'MainController@getClientJournalEditContent');
  Route::get('/journals/find/get-client-journal-edit-content-new/{edit_no}', 'MainController@getClientJournalEditContent_');
  Route::post('/update-journal', 'MainController@UpdateJournal');
  Route::post('/insert-journal', 'MainController@InsertJournal');
  Route::post('/journals/client/get-data', 'MainController@journals_client_data');
  Route::get('/journals/get-tax-rate-by-province', 'MainController@get_tax_rate_by_province');
  Route::get('/journals/get-gifi-client-accounts', 'MainController@get_gifi_client_accounts');
  Route::get('/journals/get-source-code', 'MainController@get_sources');
  Route::get('/export-excel-journals', 'MainController@ExportExcelJournals');
  Route::get('/journal-by-source-export', 'MainController@ExportExcelJournalsBySource');
  Route::get('/journal-by-period-export', 'MainController@ExportExcelJournalsByPeriod');
  Route::get('/journal-trail-balance-export', 'MainController@ExportExcelJournalsTrailBalance');
  Route::get('/journal-by-account-export', 'MainController@ExportExcelJournalsByAccount');
  Route::get('/export-excel-trial-balance', 'MainController@ExportExcelTrialBalance');
  route::get('/export-excel-financial-statement', 'MainController@ExportExcelFinancialStatement');
  Route::post('/import-excel-journals', 'MainController@ImportExcelJournals');
  Route::post('import-excel-standard-journals', 'MainController@ImportExcelStandardJournals');
  Route::get('/journals/clients/gifi', 'MainController@get_client_gifi');
  Route::post('/journals/batch-update', 'MainController@JournalBatchUpdate');
  // i add undo route
  Route::post('/journals/undo-batch-update', 'MainController@UndoJournalBatchUpdate');
  Route::post('/journals/batch-delete', 'MainController@JournalBatchDelete');
  Route::post('/journals/undo-batch-delete', 'MainController@undoJournalBatchDelete');
  Route::post('/journals/fyear/reindex', 'MainController@JournalFYReIndex');
  Route::post('/journals/client/get-fyears', 'MainController@clientGetJournalFyears');
  Route::post('/journals/client/count-fy-journals', 'MainController@clientCountFYJournals');

  Route::get('/clients', 'MainController@Clients')->middleware('isadmin');
  Route::get('/add-clients', 'MainController@AddClients')->middleware('isadmin');
  Route::post('/insert-clients', 'MainController@InsertClients')->middleware('isadmin');
  Route::get('/edit-clients', 'MainController@EditClients')->middleware('isadmin');
  Route::post('/update-clients', 'MainController@UpdateClients')->middleware('isadmin');
  Route::get('/delete-clients', 'MainController@DeleteClients')->middleware('isadmin');
  Route::get('/undo-delete-clients', 'MainController@UndoDeleteClients')->middleware('isadmin');
  Route::get('/show-clients', 'MainController@ShowClients')->middleware('isadmin');
  Route::post('/add-client-gifi-account', 'MainController@InsertClientAccount')->middleware("isadmin");
  Route::post('/edit-client-gifi-account', 'MainController@EditClientAccount')->middleware('isadmin');
  Route::post('/DeleteClientGifi', 'MainController@DeleteClientGifi')->middleware('isadmin');
  Route::post('/UndoDeleteClientGifi', 'MainController@UndoDeleteClientGifi')->middleware('isadmin');
  Route::get('/check-client-gifi', 'MainController@checkClientGifi');
  Route::get('/export-excel-clients', 'MainController@ExportExcelClients')->middleware('isadmin');
  Route::get('/export-pdf-clients', 'MainController@ExportPdfClients')->middleware('isadmin');
  Route::get('/export-print-clients', 'MainController@ExportPrintClients')->middleware('isadmin');
  Route::get('/get-client-gifi-accounts', 'MainController@getClientsAccounts');

  Route::get('/get-email-contract-clients', 'MainController@getEmailContractClients');
  Route::get('/get-comments-client', 'MainController@getCommentsClients');
  Route::get('/get-attachment-client', 'MainController@getAttachmentClients');

  Route::get('/get-email-clients', 'MainController@getEmailClients');
  Route::get('/get-fiscal-year-details', 'MainController@getFiscalYearDetails');

  Route::get('/get-comments-users', 'MainController@getCommentsUsers');
  Route::get('/get-attachment-users', 'MainController@getAttachmentUsers');

  Route::get('/get-clients-content', 'MainController@getClientsContent');
  Route::post('/insert-close-year', 'MainController@insertCloseYear');
  Route::get('/get-close-year-content', 'MainController@getCloseYearContent');
  Route::get('/get-clients-edit-content', 'MainController@getClientsEditContent');
  Route::get('/cleints-get-clients-account', 'MainController@getClientsAccount2');

  Route::get('/get-users-content', 'MainController@getUsersContent');
  Route::get('/get-vendor-content', 'MainController@getVendorContent');
  Route::get('/get-distributor-content', 'MainController@getDistributorContent');
  Route::get('/get-operating-content', 'MainController@getOperatingContent');

  Route::get('/get-comments-vendors', 'MainController@getCommentsVendors');
  Route::get('/get-attachment-vendors', 'MainController@getAttachmentVendors');


  Route::get('/get-comments-distributors', 'MainController@getCommentsDistributors');
  Route::get('/get-attachment-distributors', 'MainController@getAttachmentDistributors');

  Route::get('/get-comments-domains', 'MainController@getCommentsDomains');
  Route::get('/get-attachment-domains', 'MainController@getAttachmentDomains');

  Route::get('/get-comments-operating-systems', 'MainController@getCommentsOperatingSystems');
  Route::get('/get-attachment-operating-systems', 'MainController@getAttachmentOperatingSystems');


  Route::get('/get-comments-sla', 'MainController@getCommentsSla');
  Route::get('/get-attachment-sla', 'MainController@getAttachmentSla');

  Route::get('/get-comments-network-zone', 'MainController@getCommentsNetworkZone');
  Route::get('/get-attachment-network-zone', 'MainController@getAttachmentNetworkZone');


  Route::get('/usersOld', 'MainController@UsersOld')->middleware('isadmin');
  Route::get('/add-users', 'MainController@AddUsers')->middleware('isadmin');
  Route::post('/insert-users', 'MainController@InsertUsers')->middleware('isadmin');
  Route::get('/edit-users', 'MainController@EditUsers')->middleware('isadmin');
  Route::post('/update-users', 'MainController@UpdateUsers')->middleware('isadmin');
  Route::get('/delete-users', 'MainController@DeleteUsers')->middleware('isadmin');
  Route::get('/show-users', 'MainController@ShowUsers')->middleware('isadmin');
  Route::get('/export-excel-users', 'MainController@ExportExcelUsers')->middleware('isadmin');
  Route::get('/export-pdf-users', 'MainController@ExportPdfUsers')->middleware('isadmin');
  Route::get('/export-print-users', 'MainController@ExportPrintUsers')->middleware('isadmin');
  Route::get('/show-users-clients', 'MainController@ShowUsersClients')->middleware('isadmin');
  Route::get('/undo-delete-users/{id}', 'MainController@UndoDeleteUsers')->middleware('isadmin');


  Route::get('/vendors', 'MainController@Vendors')->middleware('isadmin');
  Route::get('/add-vendors', 'MainController@AddVendors')->middleware('isadmin');
  Route::post('/insert-vendors', 'MainController@InsertVendors')->middleware('isadmin');
  Route::get('/edit-vendors', 'MainController@EditVendors')->middleware('isadmin');
  Route::post('/update-vendors', 'MainController@UpdateVendors')->middleware('isadmin');
  Route::get('/delete-vendors', 'MainController@DeleteVendors')->middleware('isadmin');
  Route::get('/export-excel-vendors', 'MainController@ExportExcelVendors')->middleware('isadmin');
  Route::get('/export-pdf-vendors', 'MainController@ExportPdfVendors')->middleware('isadmin');
  Route::get('/export-print-vendors', 'MainController@ExportPrintVendors')->middleware('isadmin');


  Route::get('/sla', 'MainController@Sla')->middleware('isadmin');
  Route::get('/add-sla', 'MainController@AddSla')->middleware('isadmin');
  Route::post('/insert-sla', 'MainController@InsertSla')->middleware('isadmin');
  Route::get('/edit-sla', 'MainController@EditSla')->middleware('isadmin');
  Route::post('/update-sla', 'MainController@UpdateSla')->middleware('isadmin');
  Route::get('/delete-sla', 'MainController@DeleteSla')->middleware('isadmin');

  Route::get('/network-zone', 'MainController@NetworkZone')->middleware('isadmin');
  Route::get('/add-network-zone', 'MainController@AddNetworkZone')->middleware('isadmin');
  Route::post('/insert-network-zone', 'MainController@InsertNetworkZone')->middleware('isadmin');
  Route::get('/edit-network-zone', 'MainController@EditNetworkZone')->middleware('isadmin');
  Route::post('/update-network-zone', 'MainController@UpdateNetworkZone')->middleware('isadmin');
  Route::get('/delete-network-zone', 'MainController@DeleteNetworkZone')->middleware('isadmin');


  Route::get('/network', 'MainController@Network');
  Route::get('/add-network', 'MainController@AddNetwork');
  Route::post('/insert-network', 'MainController@InsertNetwork');
  Route::get('/edit-network', 'MainController@EditNetwork')->middleware('isadmin');
  Route::post('/update-network', 'MainController@UpdateNetwork');
  Route::get('/delete-network', 'MainController@DeleteNetwork')->middleware('isadmin');

  Route::get('/export-excel-network', 'MainController@ExportExcelNetwork');
  Route::get('/getVlanId', 'MainController@getVlanId')->middleware('isadmin');
  Route::get('/getVlanIdInfo', 'MainController@getVlanIdInfo')->middleware('isadmin');

  Route::get('/distributors', 'MainController@Distributors')->middleware('isadmin');
  Route::get('/add-distributors', 'MainController@AddDistributors')->middleware('isadmin');
  Route::post('/insert-distributors', 'MainController@InsertDistributors')->middleware('isadmin');
  Route::get('/edit-distributors', 'MainController@EditDistributors')->middleware('isadmin');
  Route::post('/update-distributors', 'MainController@UpdateDistributors')->middleware('isadmin');
  Route::get('/delete-distributors', 'MainController@DeleteDistributors')->middleware('isadmin');
  Route::get('/export-excel-distributors', 'MainController@ExportExcelDistributors')->middleware('isadmin');
  Route::get('/export-pdf-distributors', 'MainController@ExportPdfDistributors')->middleware('isadmin');
  Route::get('/export-print-distributors', 'MainController@ExportPrintDistributors')->middleware('isadmin');


  Route::get('/sites', 'MainController@Sites');
  Route::get('/add-sites', 'MainController@AddSites');
  Route::get('/show-sites', 'MainController@ShowSites');
  Route::post('/insert-sites', 'MainController@InsertSites');
  Route::get('/edit-sites', 'MainController@EditSites')->middleware('isadmin');
  Route::post('/update-sites', 'MainController@UpdateSites');
  Route::get('/delete-sites', 'MainController@DeleteSites')->middleware('isadmin');
  Route::get('/export-excel-site', 'MainController@ExportExcelSites');
  Route::get('/export-pdf-sites', 'MainController@ExportPdfSites');
  Route::get('/export-print-sites', 'MainController@ExportPrintSites');


  Route::get('/get-comments-sites', 'MainController@getCommentsSites');
  Route::get('/get-attachment-sites', 'MainController@getAttachmentSites');

  Route::get('/get-network-content', 'MainController@getNetworkContent');
  Route::get('/get-network-zone-content', 'MainController@getNetworkZoneContent');
  Route::get('/get-sla-content', 'MainController@getSlaContent');
  Route::get('/get-domain-content', 'MainController@getDomainContent');

  Route::get('/get-site-content', 'MainController@getSiteContent');

  Route::get('/operating-systems', 'MainController@OperatingSystems')->middleware('isadmin');
  Route::get('/add-operating-systems', 'MainController@AddOperatingSystems')->middleware('isadmin');
  Route::get('/show-operating-systems', 'MainController@ShowOperatingSystems')->middleware('isadmin');
  Route::post('/insert-operating-systems', 'MainController@InsertOperatingSystems')->middleware('isadmin');
  Route::get('/edit-operating-systems', 'MainController@EditOperatingSystems')->middleware('isadmin');
  Route::post('/update-operating-systems', 'MainController@UpdateOperatingSystems')->middleware('isadmin');
  Route::get('/delete-operating-systems', 'MainController@DeleteOperatingSystems')->middleware('isadmin');
  Route::get('/export-excel-operating-systems', 'MainController@ExportExcelOperatingSystems')->middleware('isadmin');
  Route::get('/export-pdf-operating-systems', 'MainController@ExportPdfOperatingSystems')->middleware('isadmin');
  Route::get('/export-print-operating-systems', 'MainController@ExportPrintOperatingSystems')->middleware('isadmin');



  Route::get('/domains', 'MainController@Domains')->middleware('isadmin');
  Route::get('/add-domains', 'MainController@AddDomains')->middleware('isadmin');
  Route::get('/show-domains', 'MainController@ShowDomains')->middleware('isadmin');
  Route::post('/insert-domains', 'MainController@InsertDomains')->middleware('isadmin');
  Route::get('/edit-domains', 'MainController@EditDomains')->middleware('isadmin');
  Route::post('/update-domains', 'MainController@UpdateDomains')->middleware('isadmin');
  Route::get('/delete-domains', 'MainController@DeleteDomains')->middleware('isadmin');
  Route::get('/export-excel-domains', 'MainController@ExportExcelDomains')->middleware('isadmin');







  Route::post('/end-clients', 'MainController@EndClients');
  Route::post('/end-users', 'MainController@EndUsers');
  Route::post('/end-domains', 'MainController@EndDomains');
  Route::post('/end-contract', 'AssetsController@EndContract');
  Route::post('/update-user-profile', 'MainController@UpdateUserProfile');

  Route::get('/add-contract/{type}', 'ContractController@AddContract');
  Route::get('/show-contract', 'ContractController@ShowContracts');

  Route::get('/show-contract-details', 'ContractController@ShowContractDetails');

  Route::post('/insert-contract', 'ContractController@InsertContract');
  Route::get('/edit-contract', 'ContractController@EditContract');
  Route::get('/renew-contract', 'ContractController@RenewContract');
  Route::post('/renew-contract', 'ContractController@RenewContractUpdate');
  Route::post('/update-contract', 'ContractController@UpdateContract');
  Route::get('/delete-contract', 'ContractController@DeleteContract');
  Route::get('/export-excel-contract', 'ContractController@ExportExcelContract');
  Route::get('/export-excel-expiring-contract', 'ContractController@ExportExpiringExcelContract');
  Route::get('/export-excel-ssl', 'SSLController@ExportExcelSSL');

  Route::get('/export-excel-physical', 'AssetsController@ExportExcelPhysical');
  Route::get('/pdf-contract', 'ContractController@ExportPdfContract');
  Route::get('/print-contract', 'ContractController@ExportPrintContract');
  Route::get('/expiring-30-days', 'ContractController@Expiring30Days');

  Route::get('/getSiteByClientId', 'AssetsController@getSiteByClientId');
  Route::get('/getDomainByClientId', 'AssetsController@getDomainByClientId');




  Route::get('/swap-virtual-rows', 'AssetsController@SwapVirtualRows');
  Route::get('/swap-physical-rows', 'AssetsController@SwapPhysicalRows');



  Route::get('/physical/{page_type?}', 'AssetsController@Physical');

  Route::get('/generate-contract-sharable-link', 'ContractController@GenerateContractSharableLink');
  Route::get('/remove-active-contract-links', 'ContractController@RemoveActiveContractLinks');

  Route::get('/add-ssl-certificate', 'SSLController@AddSSLCertificate');
  Route::get('/ssl-certificate', 'SSLController@SSLCertificate');
  Route::get('/edit-ssl-certificate', 'SSLController@EditSSLCertificate');
  Route::get('/renew-ssl-certificate', 'SSLController@RenewSSLCertificate');
  Route::post('/end-ssl-certificate', 'SSLController@EndSSLCertificate');
  Route::get('/delete-ssl-certificate', 'SSLController@DeleteSSLCertificate');
  Route::post('/insert-ssl-certificate', 'SSLController@InsertSSLCertificate');
  Route::post('/update-ssl-certificate', 'SSLController@UpdateSSLCertificate');
  Route::post('/renew-ssl-certificate', 'SSLController@RenewSSLCertificateUpdate');
  Route::get('/show-ssl-certificate', 'SSLController@ShowSSLCertificate');
  Route::get('/update-contract-email', 'MainController@UpdateContractEmail');
  Route::get('/check-unique-network', 'MainController@CheckUniqueNetwork');


  Route::get('/notifications', 'MainController@Notifications');
  Route::get('/get-contract-notification', 'ContractController@GetContractNotifications');
  Route::get('/get-ssl-notification', 'ContractController@GetSSLNotifications');

  Route::get('/settings', 'MainController@Settings');

  Route::post('/update-settings', 'MainController@UpdateSettings');
  Route::get('/change-contract-columns', 'MainController@changeContractColumns');
  Route::get('/change-expiring-columns', 'MainController@changeExpiringColumns');


  Route::get('/LoadContractAttachment', 'ContractController@LoadContractAttachment');
  Route::post('/uploadContractAttachment', 'ContractController@uploadContractAttachment');
  Route::delete('/revertContractAttachment', 'ContractController@RevertContractAttachment');


  Route::get('/LoadSSLAttachment', 'SSLController@LoadSSLAttachment');
  Route::post('/uploadSSLAttachment', 'SSLController@uploadSSLAttachment');
  Route::delete('/revertSSLAttachment', 'SSLController@RevertSSLAttachment');
  Route::get('/get-ip-hostname', 'SSLController@getIpHostname');

  Route::get('/get-email-contracts', 'ContractController@getEmailContracts');
  Route::get('/get-attachment-contracts', 'ContractController@getAttachmentContracts');
  Route::get('/get-comments-contracts', 'ContractController@getCommentsContracts');
  Route::get('/get-details-contracts', 'ContractController@getContractDetails');

  Route::get('/get-comments-ssl', 'SSLController@getCommentsSSL');
  Route::get('/get-attachment-ssl', 'SSLController@getAttachmentSSL');
  Route::get('/get-host-ssl', 'SSLController@getHostSSL');
  Route::get('/get-ip-ssl', 'SSLController@getIpSSL');
  Route::get('/get-san-ssl', 'SSLController@getSanSSL');
  Route::get('/get-email-ssl', 'SSLController@getEmailSSL');

  Route::get('/getVendorOfContract', 'ContractController@getVendorOfContract');
  Route::get('/getDistributorOfContract', 'ContractController@getDistributorOfContract');

  Route::get('/getVendorOfSSL', 'ContractController@getVendorOfSSL');

  Route::get('/get-ssl-content', 'SSLController@getSslContent');
  Route::get('/export-excel-virtual', 'AssetsController@exportExcelVirtual');

  Route::get('/getVendorOfPhysical', 'AssetsController@getVendorOfPhysical');
  Route::get('/get-physical-content', 'AssetsController@getPhysicalContent');
  Route::get('/get-virtual-content', 'AssetsController@getVirtualContent');
  Route::get('/print-ssl-certificate', 'SSLController@PrintSSLCertificate');
  Route::get('/print-network', 'MainController@PrintNetwork');
  Route::get('/print-site', 'MainController@PrintSite');

  Route::get('/print-physical', 'AssetsController@PrintPhysical');

  Route::get('/print-virtual', 'AssetsController@PrintVirtual');

  // new route
  Route::post('/reports/testing-summary', 'ReportController@testingSummary');
  Route::post('reports/testing-summary-by-item-category', 'ReportController@testingSummaryItemCategory');
  Route::post('/reports/check-work-order', 'ReportController@checkExistance');
  Route::post('/reports/work-order', 'ReportController@workOrder');
});




Route::get('/GetZohoInvoices', 'HomeController@GetZohoInvoices');
Route::get('/GetZohoEstimes', 'HomeController@GetZohoEstimes');
Route::get('/GetZohoSalesOrders', 'HomeController@GetZohoSalesOrders');
Route::get('/GetZohoPOs', 'HomeController@GetZohoPOs');

Route::get('/transfer', 'HomeController@Transfer');


Route::get('/GetZohoInvoicesAuth', 'HomeController@InvoiceAuth');

Route::get('/change-password', 'AdminController@showChangePasswordForm')->middleware('auth');
Route::post('/change-password', 'AdminController@changePassword')->middleware('auth');
Route::post('/forgot-password', 'Auth\CustomForgotPasswordController@sendResetLinkEmail')->name('custom.password.email');
Route::get('/return-to-login', function () {
  return view('auth.passwords.return-to-login');
})->name('password.return-to-login');



// Route::get('/change-password', 'AdminController@changePassword')->middleware('auth');
Route::post('/update-user-default-session', 'MainController@UpdateUserDefaultSession')->middleware('auth');
Route::post('/update-user-password', 'MainController@UpdateUserPassword')->middleware('auth');

Auth::routes(['verify' => false, 'register' => false]);
Route::get('/contract/{type?}', 'ContractController@Contract');
Route::get('/get-contract-content', 'ContractController@getContractContent');
