<div class="page-header-modular animate__animated animate__fadeInDown">
    <div class="header-centered-content">
        <a href="{{ $backRoute }}" class="back-btn-premium-abs">
            <i class="fas fa-chevron-left"></i>
        </a>

        <div class="header-identity text-center">
            <h1 class="page-title-premium-centered">Application for Leave</h1>
            <p class="text-[0.65rem] font-bold text-blue-100/70 tracking-tight">Department of Education - Schools Division Office</p>
        </div>

        <div class="flex flex-col items-center mt-10">
            <div class="header-metadata-compact" style="margin-bottom: 20px !important; display: flex; justify-content: center; width: 100%;">
                <!-- Enlarged Tracking Number -->
                <div class="meta-capsule-compact !px-8 !py-3 !border-white/30" style="background: rgba(255, 255, 255, 0.15); width: 280px !important; justify-content: center; display: inline-flex !important;">
                    <i class="fas fa-hashtag text-blue-100 text-lg mr-3"></i>
                    <span style="font-family: 'Monaco', 'Consolas', monospace; color: #ffffff; font-weight: 900; font-size: 1.1rem; letter-spacing: 0.1em;">
                        {{ $application->tracking_number ?? '---' }}
                    </span>
                </div>
            </div>

            <div class="header-actions-compact" style="display: flex; justify-content: center; width: 100%;">
                <a href="{{ route('user.leave.form6', ['id' => $application->id, 'format' => 'pdf']) }}" target="_blank" class="btn-action-mini !bg-white !text-slate-800 hover:!bg-white/90" style="width: 280px !important; justify-content: center; display: inline-flex !important; padding: 12px 20px !important; font-size: 0.7rem !important;">
                    <i class="fas fa-file-pdf text-red-500 text-lg mr-2"></i>
                    <span class="font-bold">PREVIEW FORM 6</span>
                </a>
            </div>
        </div>




    </div>
</div>
