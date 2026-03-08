<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'insert-contract',
        'update-contract',
        'vendor_check_data',
        'uploadItemcodeAttachment',
        'revertItemcodeAttachment',
        'uploadItemCategoriesAttachment',
        'revertItemCategoriesAttachment',
        'uploadUserAttachment',
        'revertUserAttachment',
        'uploadWorkorderAttachment',
        'revertWorkorderAttachment',
        'uploadAssetsAttachment',
        'revertAssetsAttachment',
        'uploadTestDefinitionAttachment',
        'revertTestDefinitionAttachment',
        'uploadSSLAttachment',
        'revertSSLAttachment',
        'uploadNetworkAttachment',
        'update-tax',
        'update-source',
        'update-gifi',
        'update-clients',
        'pre-insert-remittance',
        'fetch-itemcodes',
        'get-item-category',
        'get-user',
        'get-assets',
        'get-test-definition',
        'fetch-test-names',
        'fetch-item-categories',
        'uploadTestThresholdAttachment',
        'revertTestThresholdAttachment',
        'get-test-threshold',
        'fetch_workorders',
        'fetch-assets',
        'fetch-threshold',
        'insert-sample-test',
        'update-sample-test',
        'insert-sample-test-sample',
        'update-sample-test-sample',
        'uploadSampleTestAttachment',
        'revertSampleTestAttachment',
        'get-sample-test',
        'fetch-users',
        'select2_test'
    ];
}
