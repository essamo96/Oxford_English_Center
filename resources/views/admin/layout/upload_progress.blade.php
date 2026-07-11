{{-- 
    Background Upload Progress Widget
    This widget is used by Resumable.js to display upload progress 
    across the admin panel in a non-intrusive way.
--}}
<div id="global_upload_widget" class="card shadow-sm bg-body position-fixed" style="bottom: 20px; left: 20px; width: 350px; z-index: 9999; display: none; transition: all 0.3s ease;">
    <div class="card-header min-h-40px px-4 border-bottom-0">
        <h3 class="card-title fs-6 fw-bold text-gray-800 m-0">
            <i class="bi bi-cloud-arrow-up text-primary me-2 fs-4"></i> جاري الرفع...
        </h3>
        <div class="card-toolbar m-0">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger" id="cancel_global_upload" title="إلغاء الرفع">
                <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="d-flex align-items-center mb-2">
            <div class="flex-grow-1">
                <span class="text-gray-800 fw-semibold fs-7 text-truncate d-block" id="upload_file_name" style="max-width: 250px;">filename.pdf</span>
            </div>
            <div class="text-end text-primary fw-bold fs-7" id="upload_percentage">0%</div>
        </div>
        
        <div class="progress h-6px w-100 bg-light-primary mb-2">
            <div class="progress-bar bg-primary" role="progressbar" id="upload_progress_bar" style="width: 0%"></div>
        </div>
        
        <div class="d-flex justify-content-between text-muted fs-8">
            <span id="upload_speed">0 MB/s</span>
            <span id="upload_time_remaining">جارِ الحساب...</span>
        </div>
    </div>
</div>
