<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Document Summarizer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?" />

</head>
<body>
<div class="page">

{{--    <script--}}
{{--        src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js"--}}
{{--        type="module"--}}
{{--    ></script>--}}

{{--    <dotlottie-wc--}}
{{--        src="https://lottie.host/592bc43c-308b-42ed-bd34-38af1b8fd51f/giEG3AUJIb.lottie"--}}
{{--        style="width: 300px;height: 300px"--}}
{{--        autoplay--}}
{{--        loop--}}
{{--    ></dotlottie-wc>--}}

    <video
        id="loadingBot"
        preload="none"
        autoplay
        loop
        muted
        playsinline
    >
        <source src="{{ asset('bot/loadingBot.webm') }}" type="video/webm">
    </video>

{{--    <video id="loadingBot" preload="none" autoplay loop muted playsinline>--}}
{{--        <source src="{{ asset('assets/bot/loadingBot.webm') }}" type="video/webm">--}}
{{--    </video>--}}


{{--    function showLoadingBot() {--}}
{{--    const wrap = document.getElementById('loadingBotWrap');--}}
{{--    const vid = document.getElementById('loadingBot');--}}
{{--    wrap.style.display = 'block';--}}
{{--    vid.play();--}}
{{--    }--}}
{{--    function hideLoadingBot() {--}}
{{--    const wrap = document.getElementById('loadingBotWrap');--}}
{{--    const vid = document.getElementById('loadingBot');--}}
{{--    vid.pause();--}}
{{--    wrap.style.display = 'none';--}}
{{--    }--}}







    <!-- Top nav -->
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

    <!-- Main content -->
    <main class="main">
        <!-- LEFT: Upload & form -->
        <section class="panel" aria-label="Upload document to summarize">
            <div class="panel-header">
                <div>
                    <h1 class="panel-title">Upload a document to summarize</h1>
                    <p class="panel-subtitle">
                        Drop in a PDF or text file and we’ll run OCR + AI to tell you what it’s about.
                    </p>
                </div>
                <div class="chip">
                    <div class="chip-dot"></div>
                    <span>Up to 20 MB</span>
                </div>
            </div>

            <!-- Use this form with your Laravel route -->
            <!-- Example: route('documents.summarize') -->
            <form id="document-upload-form"
                  action="#"
{{--                  action="{{ route('documents.summarize') }}"--}}
                  method="POST"
                  enctype="multipart/form-data">
                <!-- Laravel CSRF token -->
                @csrf

                <!-- Dropzone (click or drag-and-drop) -->
                <label class="dropzone"
                       for="document"
                       role="button"
                       tabindex="0">
                    <div class="dropzone-top">
                        <div class="dropzone-icon" aria-hidden="true">
                            <!-- Simple document icon (stroke) -->
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M8 3.5h6.1L19 8.4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 7 20V5A1.5 1.5 0 0 1 8.5 3.5Z"
                                      stroke="currentColor"
                                      stroke-width="1.4"
                                      stroke-linecap="round"
                                      stroke-linejoin="round" />
                                <path d="M14 3.5V8h4.5"
                                      stroke="currentColor"
                                      stroke-width="1.4"
                                      stroke-linecap="round"
                                      stroke-linejoin="round" />
                                <path d="M9.5 12h5M9.5 15h3.5"
                                      stroke="currentColor"
                                      stroke-width="1.4"
                                      stroke-linecap="round" />
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

                    <!-- Actual file input -->
                    <input type="file"
                           id="document"
                           name="document"
                           accept=".pdf,.txt,.doc,.docx,.rtf,.md,.jpg,.jpeg,.png,.tiff,.bmp" />
                </label>

                <!-- File name + quick info (JS will update .file-name text) -->
                <div style="margin-top: 10px;">
                    <div class="file-pill">
                        <span class="file-badge"></span>
                        <span class="file-name" id="file-name-label">No file selected yet</span>
                    </div>
                </div>

                <!-- Optional settings / params you might use in backend -->
                <div style="margin-top: 14px; display: flex; flex-wrap: wrap; gap: 10px;">
                    <div class="pill-soft">
                        <input type="checkbox"
                               id="include-key-points"
                               name="include_key_points"
                               checked
                               style="margin-right: 6px;" />
                        <label for="include-key-points">Include key points</label>
                    </div>
                    <div class="pill-soft">
                        <input type="checkbox"
                               id="include-tone"
                               name="include_tone"
                               style="margin-right: 6px;" />
                        <label for="include-tone">Detect document tone</label>
                    </div>
                    <div class="pill-soft">
                        <input type="checkbox"
                               id="short-summary"
                               name="short_summary"
                               checked
                               style="margin-right: 6px;" />
                        <label for="short-summary">Short summary (1–3 paragraphs)</label>
                    </div>
                </div>

                <!-- Footer: submit + status -->
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary">
                        <span class="icon">⚡</span>
                        <span>Summarize document</span>
                    </button>

                    <div class="status-text">
                        <span class="status-dot"></span>
                        <span>
                            <strong>Tip:</strong> larger documents may take a few seconds while OCR runs.
                        </span>
                    </div>
                </div>
            </form>
        </section>

        <!-- RIGHT: Summary / Preview / History -->
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
                                <span class="pill-soft">Length: — pages</span>
                                <span class="pill-soft">Language: —</span>
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
                            <span>
                                You can then copy the summary, store it in your DB, or display it in your app UI.
                            </span>
                        </div>
                    </div>

                    <div class="summary-footer">
                        <span>Last processed: <span id="summary-updated-at">—</span></span>
                        <span>Avg. response: ~ a few seconds (depending on file size)</span>
                    </div>
                </div>
            </div>

            <!-- Optional: recent documents list -->
            <div style="margin-top: 18px;">
                <div class="secondary-heading">Recent documents</div>
                <div class="summary-card" style="min-height: auto;">
                    <div class="summary-body" id="recent-documents">
                        <!--
                            You can render a loop from Laravel here, e.g.:

{{--                            @foreach ($documents as $doc)--}}
                                <div>…</div>
{{--                            @endforeach--}}
                        -->
                        <div class="summary-placeholder">
                            <span>No history yet.</span>
                            <span class="highlight">After you process a document, you can list it here.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="page-footer">
        Built for OCR + OpenAI backend · Plug this view into Laravel and attach your JS logic.
    </footer>
</div>

<!-- JS HOOKS (you add your own logic here) -->
<script>
    // Simple example: show selected filename in pill
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('document');
        const fileNameLabel = document.getElementById('file-name-label');

        if (fileInput && fileNameLabel) {
            fileInput.addEventListener('change', function () {
                if (fileInput.files && fileInput.files.length > 0) {
                    fileNameLabel.textContent = fileInput.files[0].name;
                } else {
                    fileNameLabel.textContent = 'No file selected yet';
                }
            });
        }

        // Here you can intercept the form submit and hit your Laravel endpoint via fetch/AJAX
        // to keep it "single-page", e.g.:
        //
        // const form = document.getElementById('document-upload-form');
        // form.addEventListener('submit', async (e) => {
        //     e.preventDefault();
        //     // build FormData, send to backend route, update #summary-body, #summary-title, etc.
        // });
    });
</script>
</body>
</html>
