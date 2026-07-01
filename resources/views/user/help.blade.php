@extends('layouts.sdo')

@section('title', 'Need Help?')
@section('page-title', 'Need Help?')

@push('styles')
<style>
    .help-container {
        display: flex;
        justify-content: center;
        align-items: stretch;
        flex-wrap: wrap;
        gap: 32px;
        max-width: 1040px;
        margin: 0 auto;
        min-height: calc(100vh - 200px);
        padding: 60px 24px;
    }

    .help-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05), 0 5px 15px rgba(0, 0, 0, 0.03);
        flex: 1 1 400px;
        max-width: 480px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .help-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    .help-card-header {
        padding: 18px 25px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .help-card-header i {
        color: #64748b;
        font-size: 1.1rem;
    }

    .help-card-header span {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .help-card-content {
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex-grow: 1;
    }

    .help-icon-circle {
        width: 100px;
        height: 100px;
        border-radius: 28px; /* Squircle style */
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 32px;
        transition: transform 0.5s ease;
    }

    .help-card:hover .help-icon-circle {
        transform: scale(1.05) rotate(5deg);
    }

    .help-icon-circle i {
        color: white;
        font-size: 2.8rem;
    }

    /* Blue Theme (Helpdesk) */
    .theme-blue .help-icon-circle {
        background: linear-gradient(135deg, #1b4a9a 0%, #1b4a9a 100%);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.25);
    }

    .theme-blue .btn-help-connect {
        background: #1b4a9a;
        box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
    }
    
    .theme-blue .btn-help-connect:hover {
        background: #123166;
        box-shadow: 0 8px 20px rgba(29, 78, 216, 0.3);
    }

    /* Green Theme (Survey) */
    .theme-green .help-icon-circle {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
    }

    .theme-green .help-icon-circle i {
        color: #ecfdf5;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }

    .theme-green .btn-help-connect {
        background: #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .theme-green .btn-help-connect:hover {
        background: #059669;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .help-title {
        font-size: 1.85rem;
        font-weight: 850;
        color: #0f172a;
        margin-bottom: 20px;
        letter-spacing: -0.03em;
        line-height: 1.2;
    }

    .help-description {
        color: #64748b;
        font-size: 1.05rem;
        line-height: 1.6;
        margin-bottom: 40px;
        max-width: 380px;
        font-weight: 500;
    }

    .btn-help-connect {
        color: white;
        padding: 16px 32px;
        border-radius: 14px;
        font-weight: 750;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s ease;
        border: none;
        width: 100%;
        font-size: 1.1rem;
    }

    .btn-help-connect:hover {
        color: white;
        transform: translateY(-3px);
    }

    /* Animation */
    .animate-up {
        animation: fadeInUp 0.8s cubic-bezier(0.2, 1, 0.3, 1) both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 600px) {
        .help-container {
            padding: 20px;
            gap: 20px;
        }
        .help-card-content {
            padding: 40px 25px;
        }
        .help-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="help-container">
    <!-- ICT Helpdesk Card -->
    <div class="help-card theme-blue animate-up">
        <div class="help-card-header">
            <i class="fas fa-headset"></i>
            <span>Support Center</span>
        </div>
        <div class="help-card-content">
            <div class="help-icon-circle">
                <i class="fas fa-question"></i>
            </div>
            
            <h2 class="help-title">ICT Helpdesk Support</h2>
            
            <p class="help-description">
                If you are experiencing technical difficulties or have questions about the system, 
                our ICT Helpdesk is ready to assist you.
            </p>
            
            <a href="{{ env('ICT_HELPDESK_URL', 'https://ict.sample.helpdesk') }}" class="btn-help-connect" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                Connect with Us
            </a>
        </div>
    </div>

    <!-- Client Satisfaction Survey Card -->
    <div class="help-card theme-green animate-up" style="animation-delay: 0.1s;">
        <div class="help-card-header">
            <i class="fas fa-star"></i>
            <span>Your Voice Matters</span>
        </div>
        <div class="help-card-content">
            <div class="help-icon-circle">
                <i class="fas fa-star"></i>
            </div>
            
            <h2 class="help-title">Client Satisfaction</h2>
            
            <p class="help-description">
                Your feedback is vital for our continuous improvement. Please take a moment to share your experience through our survey.
            </p>
            
            <a href="{{ env('CLIENT_SATISFACTION_SURVEY_URL', 'https://forms.gle/sample-survey') }}" class="btn-help-connect" target="_blank">
                <i class="fas fa-clipboard-check"></i>
                Take the Survey
            </a>
        </div>
    </div>
</div>
@endsection
