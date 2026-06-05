<?php
/**
 * © 2026 WebHubCode
 * https://webhubcode.com
 * License: MIT
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title>Merge PDF Files Online | Clean & Fast | WebHubCode</title>
    <meta name="description" content="Merge your PDF files instantly in your browser. 100% secure, client-side processing means your sensitive documents never leave your device." />
    <meta name="keywords" content="merge pdf, combine pdf online, secure pdf merger, client-side pdf tools, webhubcode pdf" />
    <meta name="author" content="WebHubCode" />
    <meta name="robots" content="index, follow" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#f8fafc',
                        surface: '#ffffff',
                        border: '#e2e8f0',
                        primary: '#3b82f6',
                        primaryHover: '#2563eb',
                        danger: '#ef4444',
                        success: '#10b981',
                        textMain: '#0f172a',
                        textMuted: '#64748b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.05)',
                        'floating': '0 20px 40px -10px rgba(59, 130, 246, 0.15)',
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #f8fafc; color: #0f172a; }
        
        /* Subtle background pattern */
        .bg-pattern {
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Drag and Drop states */
        .drag-over { 
            border-color: #3b82f6 !important; 
            background-color: #eff6ff !important;
            transform: scale(1.01);
        }
        
        .file-item { transition: all 0.2s ease; }
        .file-item.dragging { opacity: 0.5; border-style: dashed; box-shadow: none; }
        .file-item.drag-target { border-color: #3b82f6; border-bottom-width: 4px; padding-bottom: 9px; }
        
        /* Custom Scrollbar for file list if it gets too long */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col relative bg-pattern">

    <header class="bg-surface/80 backdrop-blur-md border-b border-border sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="font-display font-extrabold text-2xl tracking-tight text-textMain">
                Web<span class="text-primary">Hub</span>Code
            </div>
            <div class="hidden sm:block text-xs font-semibold text-success bg-success/10 border border-success/20 px-3 py-1.5 rounded-full">
                🔒 100% Client-Side Processing
            </div>
        </div>
    </header>

    <main class="flex-1 w-full max-w-3xl mx-auto px-4 py-12">
        
        <div class="text-center mb-10">
            <h1 class="font-display text-4xl md:text-5xl font-extrabold text-textMain mb-4">
                Merge PDF Files
            </h1>
            <p class="text-textMuted text-lg max-w-xl mx-auto">
                Combine multiple PDFs into a single document instantly. Your files are processed securely in your browser and never uploaded.
            </p>
        </div>

        <div class="bg-surface rounded-3xl shadow-soft border border-border p-6 md:p-8">

            <div id="dropzone" class="border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 p-10 text-center cursor-pointer transition-all duration-200 hover:border-primary hover:bg-blue-50 group mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-white shadow-sm border border-border rounded-xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                    📄
                </div>
                <h3 class="font-display text-xl font-bold text-textMain mb-2">Drag & drop PDFs here</h3>
                <p class="text-textMuted text-sm">or <span class="text-primary font-semibold hover:underline" onclick="document.getElementById('file-input').click()">browse your device</span></p>
                <input type="file" id="file-input" class="hidden" multiple accept=".pdf,application/pdf"/>
            </div>

            <div id="workspace" class="hidden space-y-6">
                
                <div class="flex items-center justify-between pb-4 border-b border-border">
                    <div class="flex items-center gap-3">
                        <h4 class="font-display font-bold text-textMain text-lg">Files to Merge</h4>
                        <span id="file-count-badge" class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-md">0</span>
                    </div>
                    <button onclick="clearAll()" class="text-sm font-medium text-danger hover:text-red-700 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Clear All
                    </button>
                </div>

                <div id="file-list" class="flex flex-col gap-3 max-h-[400px] overflow-y-auto custom-scrollbar pr-2 pb-2">
                    </div>

                <div class="flex justify-end text-sm text-textMuted font-medium pt-2">
                    Total Output Size: <span id="stat-size" class="text-textMain ml-1 font-bold">0 KB</span>
                </div>

                <div class="bg-slate-50 border border-border rounded-xl p-4">
                    <label class="block text-sm font-semibold text-textMain mb-2">Save as...</label>
                    <div class="flex items-center">
                        <input type="text" id="output-name" value="merged_document" class="flex-1 bg-white border border-border rounded-l-lg px-4 py-2.5 text-textMain text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                        <div class="bg-slate-100 border border-l-0 border-border rounded-r-lg px-4 py-2.5 text-textMuted text-sm font-medium">.pdf</div>
                    </div>
                </div>

                <div id="progress-wrap" class="hidden">
                    <div class="flex justify-between text-xs font-bold text-primary mb-2">
                        <span id="progress-text">Merging documents...</span>
                        <span id="progress-percent">0%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div id="progress-fill" class="h-full bg-primary w-0 transition-all duration-300"></div>
                    </div>
                </div>

                <div id="status" class="hidden text-sm font-medium p-4 rounded-lg"></div>

                <button id="merge-btn" onclick="mergePDFs()" disabled class="w-full bg-primary hover:bg-primaryHover text-white font-display text-lg font-bold py-4 rounded-xl shadow-floating transition-all transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Merge PDFs
                </button>

                <a id="download-btn" class="hidden w-full bg-success hover:bg-emerald-600 text-white font-display text-lg font-bold py-4 rounded-xl shadow-[0_10px_20px_-10px_rgba(16,185,129,0.4)] transition-all text-center flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Merged File
                </a>

            </div>
        </div>
    </main>

    <section class="bg-white border-t border-border mt-auto py-16">
        <div class="max-w-4xl mx-auto px-6 text-textMuted">
            <h2 class="font-display text-2xl font-bold text-textMain mb-6 text-center">Fast, Free, and Secure PDF Combination</h2>
            <div class="grid md:grid-cols-2 gap-8 text-sm">
                <div>
                    <p class="leading-relaxed mb-4">
                        Unlike traditional server-based tools, WebHubCode's PDF Merger utilizes modern browser APIs to process your documents locally. This means zero upload times, zero server limits, and absolute privacy for your sensitive data.
                    </p>
                </div>
                <div>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2"><span class="text-success font-bold">✓</span> No files are sent to the cloud.</li>
                        <li class="flex items-center gap-2"><span class="text-success font-bold">✓</span> Unlimited file size and daily usage.</li>
                        <li class="flex items-center gap-2"><span class="text-success font-bold">✓</span> Drag and drop reordering.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-6 border-t border-border bg-slate-50 text-textMuted text-sm">
        <div>© <?php echo date("Y"); ?> <a href="https://webhubcode.com" target="_blank" class="text-primary font-medium hover:underline">WebHubCode</a></div>
        <div class="text-xs mt-1">Licensed under MIT</div>
    </footer>

    <script>
        const { PDFDocument } = PDFLib;

        let files = [];
        let dragSrcIndex = null;

        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file-input');
        const workspace = document.getElementById('workspace');
        const fileList = document.getElementById('file-list');
        const mergeBtn = document.getElementById('merge-btn');
        const downloadBtn = document.getElementById('download-btn');
        const progressWrap = document.getElementById('progress-wrap');
        const progressFill = document.getElementById('progress-fill');
        const progressPercent = document.getElementById('progress-percent');
        const statusEl = document.getElementById('status');

        // Drop zone events
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'); });
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('drag-over');
            addFiles([...e.dataTransfer.files]);
        });
        dropzone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => addFiles([...fileInput.files]));

        function addFiles(newFiles) {
            const pdfs = newFiles.filter(f => f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf'));
            if (pdfs.length === 0) { showStatus('Please select PDF files only.', 'error'); return; }
            files = [...files, ...pdfs];
            renderList();
            hideStatus();
            downloadBtn.classList.add('hidden');
            downloadBtn.classList.remove('flex');
            mergeBtn.classList.remove('hidden');
        }

        function renderList() {
            fileList.innerHTML = '';
            
            if (files.length > 0) {
                workspace.classList.remove('hidden');
                dropzone.classList.add('hidden'); // Hide massive dropzone to save space
            } else {
                workspace.classList.add('hidden');
                dropzone.classList.remove('hidden');
            }

            files.forEach((f, i) => {
                const item = document.createElement('div');
                item.className = 'file-item bg-white border border-border shadow-sm rounded-xl p-3 flex items-center gap-4 cursor-grab hover:shadow-md';
                item.draggable = true;
                item.dataset.index = i;
                
                item.innerHTML = `
                    <div class="text-slate-300 hover:text-slate-500 transition-colors px-1 cursor-grab">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 11-4 0 2 2 0 014 0zM8 12a2 2 0 11-4 0 2 2 0 014 0zM8 18a2 2 0 11-4 0 2 2 0 014 0zM20 6a2 2 0 11-4 0 2 2 0 014 0zM20 12a2 2 0 11-4 0 2 2 0 014 0zM20 18a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="w-10 h-10 bg-red-50 text-danger rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.267 14.68c-.184 0-.308.018-.372.036v1.178c.076.018.171.023.302.023.479 0 .774-.242.774-.651 0-.366-.254-.586-.704-.586zm3.487.012c-.2 0-.33.018-.407.036v2.61c.077.018.201.018.313.018.817.006 1.349-.444 1.349-1.396.006-.83-.479-1.268-1.255-1.268z"/><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM9.498 16.19c-.309.29-.765.42-1.296.42a2.23 2.23 0 01-.308-.018v1.426H7v-3.936A7.558 7.558 0 018.219 14c.557 0 .953.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zm3.807 1.355c-.42.349-1.059.515-1.84.515-.468 0-.799-.03-1.024-.06v-3.917A7.947 7.947 0 0111.66 14c.757 0 1.249.136 1.633.426.415.308.675.799.675 1.504 0 .763-.279 1.29-.663 1.615zM17 14.77h-1.532v.911H16.9v.734h-1.432v1.604h-.906V14.03H17v.74z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-textMain whitespace-nowrap overflow-hidden text-ellipsis">${f.name}</div>
                        <div class="text-xs text-textMuted font-medium mt-0.5">${formatSize(f.size)}</div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button class="w-8 h-8 rounded-md bg-slate-50 border border-border flex items-center justify-center text-textMuted hover:bg-slate-100 hover:text-textMain transition-colors disabled:opacity-30 disabled:cursor-not-allowed" onclick="moveUp(${i})" title="Move up" ${i===0?'disabled':''}>↑</button>
                        <button class="w-8 h-8 rounded-md bg-slate-50 border border-border flex items-center justify-center text-textMuted hover:bg-slate-100 hover:text-textMain transition-colors disabled:opacity-30 disabled:cursor-not-allowed" onclick="moveDown(${i})" title="Move down" ${i===files.length-1?'disabled':''}>↓</button>
                        <button class="w-8 h-8 rounded-md bg-red-50 border border-red-100 flex items-center justify-center text-danger hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors" onclick="removeFile(${i})" title="Remove">✕</button>
                    </div>`;

                // Drag to reorder
                item.addEventListener('dragstart', () => { dragSrcIndex = i; item.classList.add('dragging'); });
                item.addEventListener('dragend', () => item.classList.remove('dragging'));
                item.addEventListener('dragover', e => { e.preventDefault(); item.classList.add('drag-target'); });
                item.addEventListener('dragleave', () => item.classList.remove('drag-target'); });
                item.addEventListener('drop', e => {
                    e.preventDefault();
                    item.classList.remove('drag-target');
                    if (dragSrcIndex !== null && dragSrcIndex !== i) {
                        const moved = files.splice(dragSrcIndex, 1)[0];
                        files.splice(i, 0, moved);
                        renderList();
                    }
                });

                fileList.appendChild(item);
            });

            mergeBtn.disabled = files.length < 2;
            document.getElementById('file-count-badge').textContent = files.length;
            document.getElementById('stat-size').textContent = formatSize(files.reduce((a, f) => a + f.size, 0));
        }

        function moveUp(i) { if (i > 0) { [files[i-1], files[i]] = [files[i], files[i-1]]; renderList(); } }
        function moveDown(i) { if (i < files.length-1) { [files[i], files[i+1]] = [files[i+1], files[i]]; renderList(); } }
        function removeFile(i) { files.splice(i, 1); renderList(); if (!files.length) { hideStatus(); dropzone.classList.remove('hidden'); } }
        function clearAll() { files = []; fileInput.value = ''; renderList(); hideStatus(); downloadBtn.classList.add('hidden'); downloadBtn.classList.remove('flex'); mergeBtn.classList.remove('hidden'); dropzone.classList.remove('hidden'); workspace.classList.add('hidden'); }

        async function mergePDFs() {
            if (files.length < 2) return;
            mergeBtn.disabled = true;
            hideStatus();
            showProgress(0, 'Initializing...');

            try {
                const merged = await PDFDocument.create();

                for (let i = 0; i < files.length; i++) {
                    const pct = Math.round((i / files.length) * 90);
                    showProgress(pct, `Processing ${files[i].name}...`);
                    const bytes = await readFileAsArrayBuffer(files[i]);
                    const pdf = await PDFDocument.load(bytes);
                    const pages = await merged.copyPages(pdf, pdf.getPageIndices());
                    pages.forEach(p => merged.addPage(p));
                }

                showProgress(95, 'Finalizing document...');
                const pdfBytes = await merged.save();
                showProgress(100, 'Complete!');

                const blob = new Blob([pdfBytes], { type: 'application/pdf' });
                const url = URL.createObjectURL(blob);
                const name = (document.getElementById('output-name').value.trim() || 'merged_document') + '.pdf';

                // Swap buttons
                mergeBtn.classList.add('hidden');
                downloadBtn.href = url;
                downloadBtn.download = name;
                downloadBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Download ${name} (${formatSize(blob.size)})`;
                downloadBtn.classList.remove('hidden');
                downloadBtn.classList.add('flex');

                showStatus(`✅ Successfully merged ${files.length} documents!`, 'success');
            } catch (err) {
                showStatus(`❌ Error: ${err.message || 'Failed to merge. Ensure files are valid PDFs.'}`, 'error');
                mergeBtn.disabled = false;
            } finally {
                setTimeout(() => {
                    progressWrap.classList.add('hidden');
                    progressWrap.classList.remove('block');
                }, 2000);
            }
        }

        function readFileAsArrayBuffer(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => resolve(e.target.result);
                reader.onerror = reject;
                reader.readAsArrayBuffer(file);
            });
        }

        function showProgress(pct, text) {
            progressWrap.classList.remove('hidden');
            progressWrap.classList.add('block');
            progressFill.style.width = pct + '%';
            progressPercent.textContent = pct + '%';
            document.getElementById('progress-text').textContent = text;
        }

        function showStatus(msg, type) {
            statusEl.textContent = msg;
            statusEl.classList.remove('hidden');
            statusEl.classList.add('block');
            
            if(type === 'success') {
                statusEl.className = 'block text-center p-3 rounded-xl text-sm font-bold mb-4 bg-emerald-50 text-emerald-600 border border-emerald-100';
            } else {
                statusEl.className = 'block text-center p-3 rounded-xl text-sm font-bold mb-4 bg-red-50 text-red-600 border border-red-100';
            }
        }
        
        function hideStatus() { 
            statusEl.classList.add('hidden'); 
            statusEl.classList.remove('block');
        }

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
            return (bytes/(1024*1024)).toFixed(2) + ' MB';
        }
    </script>
</body>
</html>
