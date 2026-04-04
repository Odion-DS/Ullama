@if($this->downloadProgress)
<style>
    .model-download-progress {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        border: 2px solid #6366f1;
        background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
        padding: 24px;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15);
        margin-bottom: 20px;
    }

    .progress-bg-animation {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.03) 0%, rgba(139, 92, 246, 0.03) 50%, rgba(99, 102, 241, 0.03) 100%);
        animation: pulse-bg 2s ease-in-out infinite;
    }

    @keyframes pulse-bg {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .progress-content {
        position: relative;
        z-index: 1;
    }

    .progress-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .progress-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .spinner-container {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        border-radius: 50%;
    }

    .spinner {
        width: 28px;
        height: 28px;
        border: 3px solid #6366f1;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .model-name {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px 0;
    }

    .model-status {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .progress-percent {
        text-align: right;
    }

    .percent-large {
        font-size: 32px;
        font-weight: 700;
        color: #6366f1;
        line-height: 1;
        display: block;
    }

    .bytes-info {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
        display: block;
    }

    .progress-bar-container {
        position: relative;
        height: 14px;
        background: #e2e8f0;
        border-radius: 100px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 100px;
        transition: width 0.5s ease-out;
        position: relative;
        overflow: hidden;
    }

    .progress-bar-shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.4) 50%, transparent 100%);
        animation: shine 2s ease-in-out infinite;
    }

    @keyframes shine {
        to { left: 100%; }
    }
</style>

<div class="model-download-progress" wire:poll.500ms="checkProgress">
    <div class="progress-bg-animation"></div>

    <div class="progress-content">
        <div class="progress-header">
            <div class="progress-info">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
                <div>
                    <h3 class="model-name">{{ $this->downloadProgress['model'] }}</h3>
                    <p class="model-status">{{ $this->downloadProgress['status'] }}</p>
                </div>
            </div>
            <div class="progress-percent">
                <span class="percent-large">{{ $this->downloadProgress['percent'] }}%</span>
                @if(isset($this->downloadProgress['completed']) && isset($this->downloadProgress['total']) && $this->downloadProgress['total'] > 0)
                    <span class="bytes-info">
                        {{ $this->formatBytes($this->downloadProgress['completed']) }} / {{ $this->formatBytes($this->downloadProgress['total']) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $this->downloadProgress['percent'] }}%">
                <div class="progress-bar-shine"></div>
            </div>
        </div>
    </div>
</div>
@endif
