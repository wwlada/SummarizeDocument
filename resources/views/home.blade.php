<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Document Summarizer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
<div id="blackhole-bg"></div>

<header class="top-nav">
    <div class="brand">
        <div class="brand-mark">
            <span>AI</span>
        </div>
        <div class="brand-text">
            <div class="brand-title">Doc Insight</div>
            <div class="brand-subtitle">Upload → OCR → OpenAI summary</div>
        </div>
    </div>

    <div class="nav-pill">
        <div class="nav-dot"></div>
        <span>Secure processing · Private by design</span>
    </div>
</header>

<div class="scroll-wrap">
<div class="page">
    <div id="loadingOverlay" class="overlay hidden" aria-hidden="true">
        <video
            id="loadingVideo"
            preload="none"
            autoplay
            loop
            muted
            playsinline
        >
            <source src="{{ asset('bot/loadingBot.webm') }}" type="video/webm">
        </video>
    </div>

    @if ($messages = session('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach ($messages as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main class="main">
        <!-- Upload & form -->
        <section class="panel" aria-label="Upload document to summarize">
            <div class="panel-header">
                <div>
                    <h1 class="panel-title">Upload a document to summarize</h1>
                    <p class="panel-subtitle">
                        Drop in a PDF or text file and we'll run OCR + AI to tell you what it's about.
                    </p>
                </div>
                <div class="chip">
                    <div class="chip-dot"></div>
                    <span>Up to 2 MB</span>
                </div>
            </div>

            <form id="document-upload-form"
                  data-summarize-url="{{ route('summarizeDocument') }}"
                  method="POST"
                  enctype="multipart/form-data">

                <!-- Dropzone -->
                <label class="dropzone"
                       for="document"
                       tabindex="0">
                    <div class="dropzone-top">
                        <div class="dropzone-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M8 3.5h6.1L19 8.4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 7 20V5A1.5 1.5 0 0 1 8.5 3.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"/>
                                <path d="M14 3.5V8h4.5"
                                      stroke="currentColor"
                                      stroke-width="1.4"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M9.5 12h5M9.5 15h3.5"
                                      stroke="currentColor"
                                      stroke-width="1.4"
                                      stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="dropzone-text-main">
                                Drop your file here, or <strong>browse</strong>
                            </div>
                            <div class="dropzone-text-sub">
                                PDF, DOCX, TXT and common image formats for OCR (JPG, PNG).
                            </div>
                            <div class="dropzone-chip-row">
                                <span class="tag tag--primary">OCR enabled</span>
                                <span class="tag">OpenAI-powered summary</span>
                                <span class="tag">No data is shared publicly</span>
                            </div>
                        </div>
                    </div>

                    <div class="dropzone-actions">
                        <button type="button" class="btn btn-primary" aria-hidden="true">
                            <span class="icon">📁</span>
                            <span>Choose file</span>
                        </button>
                        <span class="file-info">
                            <span>or drag &amp; drop from your desktop</span>
                        </span>
                    </div>

                    <input type="file"
                           id="document"
                           name="document"
                           accept=".pdf,.txt,.doc,.docx,.rtf,.md,.jpg,.jpeg,.png,.tiff,.bmp"/>
                </label>

                <div style="margin-top: 10px;">
                    <div class="file-pill">
                        <span class="file-badge"></span>
                        <span class="file-name" id="file-name-label">No file selected yet</span>
                    </div>
                </div>


                <!-- Custom instruction -->
                <div class="prompt-field">
                    <label class="prompt-label" for="user_prompt">
                        Custom instructions
                        <span class="prompt-optional">(optional)</span>
                    </label>
                    <textarea
                        id="user_prompt"
                        name="user_prompt"
                        class="prompt-textarea"
                        placeholder="e.g. Extract all dates and names, or: translate the summary to English."
                        rows="3"
                        maxlength="500"
                    ></textarea>
                </div>

                <!-- Footer -->
                <div class="panel-footer">
                    <button type="button"
                            id="summarize-button"
                            class="btn btn-primary">
                        <span class="icon">⚡</span>
                        <span>Summarize document</span>
                    </button>

                    <div class="status-text">
                        <span class="status-dot"></span>
                        <span>
                            <strong>Tip:</strong> larger documents may take a few seconds to one minute while OCR runs.
                        </span>
                    </div>
                </div>
            </form>
        </section>

        <!-- Summary / Preview / History -->
        <section class="panel" aria-label="Summary and insights">
            <div>
                <div class="secondary-heading">Summary preview</div>
                <div class="summary-card">
                    <div class="summary-header-row">
                        <div>
                            <div class="summary-title" id="summary-title">
                                No document processed yet
                            </div>
                            <div class="summary-meta">
                                <span class="pill-soft">Language:&nbsp;<span id="languageOutputSpan">---</span></span>
                                <span class="pill-soft pill-soft--ready" id="summary-status">
                                    Waiting for upload
                                </span>
                            </div>
                        </div>
                        <div class="badge-small badge-small--accent">
                            AI summary
                        </div>
                    </div>

                    <div class="summary-body" id="summary-body">
                        <div class="summary-placeholder">
                            <span>
                                Once you upload a file and submit, this panel will show a clean
                                <span class="highlight">AI-generated summary</span> of your document.
                            </span>
                        </div>
                    </div>

                    <div class="summary-footer">
                        <span>Last processed: <span id="summary-updated-at">{{$lastTimeProcessed}}</span></span>
                        <span>Average response: <span id="avgResponseTimeSpan">{{$avgResponseTime}}</span> seconds.</span>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<footer class="page-footer">
    Built for OCR + OpenAI by wwlada
</footer>
</div>


<script type="module" src="{{ asset('js/label-changer.js') }}"></script>
<script type="module" src="{{ asset('js/summarize-document.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
<script src="{{ asset('js/blackhole.js') }}"></script>
</body>
</html>
