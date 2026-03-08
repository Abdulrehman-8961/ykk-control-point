<style>
    iframe#sampleTestIframe {
        width: 1340px;
        height: 800px;
        border-radius: 10px;
        border: none;
    }
</style>
<div class="tab-content mt-3" id="myTabContent">
    <div class="tab-pane fade show active" id="formTabPane" role="tabpanel" aria-labelledby="form-tab" style="text-align: center;">
        <iframe id="sampleTestIframe" src="{{ url('/sample-tests') }}" frameborder="0"
            style="width:800px; height:1340px; border-radius:10px; overflow:auto;">
        </iframe>
    </div>
</div>

<script>
    const iframe = document.getElementById("sampleTestIframe");
    iframe.onload = function() {
        const css = `
        .new-header-icon-div img {
        width: 20px !important;
        }
        
        .header-new-text {
                    font-size: 11pt !important;
                        line-height: 14px !important;
                }

                .header-new-subtext {
                line-height: 13px !important;
                }

                .sample-data-div .col-sm-2 {
                    flex: 0 0 auto;
                    width: 30% !important;
                    /* same as col-sm-3 */
                    max-width: 30% !important;
                }

                .bubble-div.col-lg-4 {
                    flex: 0 0 40.333333% !important;
                    max-width: 40.333333% !important;
                }

                #showData.col-lg-8 {
                    flex: 0 0 59.666667% !important;
                    max-width: 59.666667% !important;
                }

                .col-md-4.bubble-header {
                    flex: 0 0 40.333333% !important;
                    max-width: 40.333333% !important;
                }

                .col-md-8.detail-header {
                    flex: 0 0 59.666667% !important;
                    max-width: 59.666667% !important;
                }

                .bubble-item-title {
                    font-size: 10pt !important;
                }

                .titillium-web-light.bubble-item-title {
                    font-size: 7pt !important;
                }
                .rounded-circle-div {
                    width: 30px !important;
                    height: 30px !important;
                }

                .bubble-status-active {
                    font-size: 7pt !important;
                }
                .final-sample-value {
                    font-size: 7pt !important;
                }

                .final-sample-number {
    font-size: 6pt !important;
}

                .search-col.col-sm-4 {
                    -ms-flex: 0 0 40.333333% !important;
                    flex: 0 0 40.333333% !important;
                    max-width: 40.333333% !important;
                }
                .col-lg-8.pr-sm-4 {
                    -ms-flex: 0 0 59.666667% !important;
                    flex: 0 0 59.666667% !important;
                    max-width: 59.666667% !important;
                }

                .search-col .col-sm-1 {
                    flex: 0 0 14.333333% !important;
                    max-width: 14.333333% !important;
                }

                .header-image {
                    width: 30px !important;
                    height: 30px !important;
                }

                .header-new-subtext {
                    font-size: 10pt !important;
                }
                    
                .qc-test-result .col-sm-2 {
                    -ms-flex: 0 0 28.666667% !important;
                    flex: 0 0 28.666667% !important;
                    max-width: 28.666667% !important;
                }

                .bubble-header-text {
                    padding-left: 10px !important;
                }
                    
                .content-div {
                    height: 90vh !important;
                }
                    
                .new-header-icon-div a {
                    padding-left: 6px !important;
                    margin-left: 0px !important;
                    margin-right: 0px !important;
                    padding-right: 6px !important;
                    padding-top: 5px !important;
                    margin-top: 2px !important;
                    padding-bottom: 5px !important;
                }`;
        const style = document.createElement("style");
        style.innerHTML = css;
        iframe.contentDocument.head.appendChild(style);
    };
</script>
