<style>
    iframe#sampleTestIframe {
        width: 1340px;
        height: 800px;
        border-radius: 10px;
        border: none;
    }
</style>
<div class="tab-content mt-3" id="myTabContent">
    <div class="tab-pane fade show active" id="formTabPane" role="tabpanel" aria-labelledby="form-tab">
        <iframe id="sampleTestIframe" src="{{ url('/sample-tests') }}" frameborder="0"
            style="width:1340px; height:800px; border-radius:10px; overflow:auto;">
        </iframe>
    </div>
</div>

<script>
    const iframe = document.getElementById("sampleTestIframe");
    iframe.onload = function() {
        const css = `.header-new-text {
                    font-size: 13pt !important;
                }

                .sample-data-div .col-sm-2 {
                    flex: 0 0 auto;
                    width: 25% !important;
                    /* same as col-sm-3 */
                    max-width: 25% !important;
                }

                .bubble-div.col-lg-4 {
                    flex: 0 0 36.333333% !important;
                    max-width: 36.333333% !important;
                }

                #showData.col-lg-8 {
                    flex: 0 0 63.666667% !important;
                    max-width: 63.666667% !important;
                }

                .col-md-4.bubble-header {
                    flex: 0 0 36.333333% !important;
                    max-width: 36.333333% !important;
                }

                .col-md-8.detail-header {
                    flex: 0 0 63.666667% !important;
                    max-width: 63.666667% !important;
                }

                .bubble-item-title {
                    font-size: 11pt !important;
                }

                .titillium-web-light.bubble-item-title {
                    font-size: 8pt !important;
                }

                .bubble-status-active {
                    font-size: 8pt !important;
                }

                .search-col.col-sm-4 {
                    -ms-flex: 0 0 33.333333% !important;
                    flex: 0 0 36.333333% !important;
                    max-width: 36.333333% !important;
                }
                .col-lg-8.pr-sm-4 {
                    -ms-flex: 0 0 63.666667% !important;
                    flex: 0 0 63.666667% !important;
                    max-width: 63.666667% !important;
                }

                .search-col .col-sm-1 {
                    flex: 0 0 12.333333% !important;
                    max-width: 12.333333% !important;
                }
                    
                .qc-test-result .col-sm-2 {
                    -ms-flex: 0 0 18.666667% !important;
                    flex: 0 0 18.666667% !important;
                    max-width: 18.666667% !important;
                }`;
        const style = document.createElement("style");
        style.innerHTML = css;
        iframe.contentDocument.head.appendChild(style);
    };
</script>
