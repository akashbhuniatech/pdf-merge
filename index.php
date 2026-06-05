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
    
    <title>Merge PDF Files Online | 100% Free & Secure | WebHubCode</title>
    <meta name="description" content="Merge your PDF files instantly in your browser. 100% secure, client-side processing means your sensitive documents never leave your device. Fast, free, and no installation required." />
    <meta name="keywords" content="merge pdf, combine pdf online, secure pdf merger, client-side pdf tools, webhubcode pdf, free pdf combiner" />
    <meta name="author" content="WebHubCode" />
    <meta name="robots" content="index, follow" />
    
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#0a0a0f',
                        surface: '#13131a',
                        surface2: '#1c1c26',
                        border: '#2a2a3a',
                        accent: '#6c63ff',
                        accent2: '#ff6584',
                        accent3: '#43e8b0',
                        text: '#f0f0ff',
                        muted: '#7a7a99',
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        display: ['Syne', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0a0a0f;
            color: #f0f0ff;
        }
        /* Animated background blobs */
        body::before {
            content: ''; position: fixed; top: -40%; left: -20%; width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(108,99,255,0.12) 0%, transparent 65%);
            pointer-events: none; z-index: 0; animation: drift 12s ease-in-out infinite alternate;
        }
        body::after {
            content: ''; position: fixed; bottom: -30%; right: -10%; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(67,232,176,0.08) 0%, transparent 65%);
            pointer-events: none; z-index: 0; animation: drift 16s ease-in-out infinite alternate-reverse;
        }
        @keyframes drift { from { transform: translate(0,0); } to { transform: translate(40px, 30px); } }

        /* Drag and Drop states */
        .drag-over { border-color: #6c63ff !important; transform: translateY(-2px); }
        .drag-over::before { opacity: 1 !important; }
        .file-item.dragging { opacity: 0.4; border-style: dashed; }
        .file-item.drag-target { border-color: #43e8b0; background: rgba(67,232,176,0.05); }
        
        .gradient-text {
            background: linear-gradient(135deg, #6c63ff, #ff6584);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col relative overflow-x-hidden">

    <header class="relative z-10 px-6 py-5 md:px-10 flex items-center justify-between border-b border-border backdrop-blur-md">
        <div class="font-display font-extrabold text-xl tracking-tight">
            Web<span class="text-accent">Hub</span>Code
        </div>
        <div class="text-[11px] font-medium text-accent3 bg-accent3/10 border border-accent3/25 px-3 py-1 rounded-full tracking-wide">
            100% Client-Side · No Upload
        </div>
    </header>

    <main class="relative z-10 flex-1 w-full max-w-3xl mx-auto px-6 pt-16 pb-12">
        
        <div class="text-center mb-12">
            <h1 class="font-display text-4xl md:text-6xl font-extrabold leading-tight tracking-tight mb-4">
                Merge PDFs<br/><span class="gradient-text">Instantly</span>
            </h1>
            <p class="text-muted text-base font-light leading-relaxed">
                Drag, drop, reorder and merge your PDF files.<br/>
                Everything runs in your browser — your files never leave your device.
            </p>
        </div>

        <div id="dropzone" class="relative overflow-hidden border-2 border-dashed border-border rounded-2xl bg-surface p-12 text-center cursor-pointer transition-all duration-250 hover:border-accent hover:-translate-y-0.5 group mb-6">
            <div class="absolute inset-0 bg-gradient-to-br from-accent/5 to-accent3/5 opacity-0 group-hover:opacity-100 transition-opacity duration-250 pointer-events-none before-bg"></div>
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-accent/20 to-accent3/10 rounded-2xl flex items-center justify-center text-3xl">📄</div>
            <h3 class="font-display text-lg font-bold mb-1">Drop PDF files here</h3>
            <p class="text-muted text-sm">or <span class="text-accent font-medium underline cursor-pointer" onclick="document.getElementById('file-input').click()">browse your files</span></p>
            <input type="file" id="file-input" class="hidden" multiple accept=".pdf,application/pdf"/>
        </div>

        <div id="file-list-wrap" class="hidden mb-6">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-display text-[13px] font-semibold text-muted uppercase tracking-wider">📋 Files to merge <span id="file-count" class="text-accent"></span></h4>
                <button class="text-xs text-accent2 opacity-80 hover:opacity-100 hover:underline font-sans" onclick="clearAll()">Clear all</button>
            </div>
            <div id="file-list" class="flex flex-col gap-2"></div>
        </div>

        <div id="stats-row" class="hidden flex-wrap gap-2 justify-center mb-6">
            <div class="text-xs text-muted bg-surface border border-border rounded-full px-3 py-1">Files: <span id="stat-files" class="text-text font-medium">0</span></div>
            <div class="text-xs text-muted bg-surface border border-border rounded-full px-3 py-1">Total size: <span id="stat-size" class="text-text font-medium">0 KB</span></div>
        </div>

        <div class="flex flex-wrap gap-3 mb-6">
            <div class="flex-1 min-w-[160px] bg-surface border border-border rounded-xl p-4">
                <label class="text-xs text-muted block mb-2 font-medium uppercase tracking-wide">Output filename</label>
                <input type="text" id="output-name" value="merged" placeholder="merged" class="w-full bg-surface2 border border-border rounded-lg px-3 py-2 text-text text-sm outline-none focus:border-accent transition-colors" />
            </div>
        </div>

        <div id="progress-wrap" class="hidden mb-4">
            <div class="h-1 bg-surface2 rounded-full overflow-hidden mb-2">
                <div id="progress-fill" class="h-full bg-gradient-to-r from-accent to-accent3 w-0 transition-all duration-300"></div>
            </div>
            <div id="progress-text" class="text-xs text-muted text-center">Merging...</div>
        </div>

        <div id="status" class="hidden items-center gap-2 p-3 rounded-xl text-sm font-medium mb-4"></div>

        <button id="merge-btn" onclick="mergePDFs()" disabled class="w-full relative overflow-hidden bg-gradient-to-br from-accent to-[#8b7cf8] p-4 rounded-2xl text-white font-display text-base font-bold tracking-wide transition-all hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(108,99,255,0.4)] active:translate-y-0 disabled:opacity-40 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none mb-4 group">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <span class="relative z-10">⚡ Merge PDFs</span>
        </button>

        <a id="download-btn" class="hidden w-full p-4 bg-accent3/10 border border-accent3/30 rounded-2xl text-accent3 font-display text-[15px] font-bold text-center hover:bg-accent3/20 transition-all hover:-translate-y-px decoration-none">
            ⬇️ Download Merged PDF
        </a>

    </main>

    <section class="relative z-10 bg-surface border-t border-border mt-12 py-16">
        <div class="max-w-4xl mx-auto px-6 text-muted">
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <h2 class="font-display text-2xl font-bold text-text mb-4">Why use WebHubCode PDF Merger?</h2>
                    <p class="text-sm leading-relaxed mb-4">
                        Managing documents shouldn't require downloading clunky software or risking your privacy by uploading sensitive files to a remote server. Our online PDF combiner leverages modern web technologies to process your files directly inside your browser. 
                    </p>
                    <p class="text-sm leading-relaxed">
                        Whether you are compiling financial reports, combining scanned documents, or organizing study materials, our tool guarantees a seamless, lightning-fast experience without compromising data security.
                    </p>
                </div>
                <div>
                    <h2 class="font-display text-2xl font-bold text-text mb-4">100% Secure & Client-Side</h2>
                    <ul class="list-none space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="text-accent3">✓</span> 
                            <div><strong>Zero Uploads:</strong> Your files are never sent to our servers. All merging happens via JavaScript on your device.</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-accent3">✓</span> 
                            <div><strong>No File Limits:</strong> Because processing relies on your device's memory, you bypass standard server upload limits.</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-accent3">✓</span> 
                            <div><strong>Instant Results:</strong> Skip the upload and download wait times. Merging begins the second you click the button.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <footer class="relative z-10 text-center py-8 border-t border-border text-muted text-xs bg-bg">
        <div>© <?php echo date("Y"); ?> <a href="https://webhubcode.com" target="_blank" class="text-accent hover:underline">WebHubCode</a> · MIT License</div>
        <div class="mt-1 text-[#3a3a55]">Your files are processed entirely in your browser. Nothing is uploaded to any server.</div>
    </footer>

    <script>
        const { PDFDocument } = PDFLib;

        let files = [];
        let dragSrcIndex = null;

        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file-input');
        const fileListWrap = document.getElementById('file-list-wrap');
        const fileList = document.getElementById('file-list');
        const mergeBtn = document.getElementById('merge-btn');
        const downloadBtn = document.getElementById('download-btn');
        const progressWrap = document.getElementById('progress-wrap');
        const progressFill = document.getElementById('progress-fill');
        const progressText = document.getElementById('progress-text');
        const statusEl = document.getElementById('status');
        const statsRow = document.getElementById('stats-row');

        // Drop zone events
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
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
            downloadBtn.classList.remove('block');
        }

        function renderList() {
            fileList.innerHTML = '';
            files.forEach((f, i) => {
                const item = document.createElement('div');
                item.className = 'file-item bg-surface border border-border rounded-xl p-3 flex items-center gap-3 transition-all cursor-grab select-none hover:border-accent/40 hover:bg-surface2';
                item.draggable = true;
                item.dataset.index = i;
                
                item.innerHTML = `
                    <div class="text-muted text-base cursor-grab px-1">⠿</div>
                    <div class="w-9 h-9 bg-gradient-to-br from-[#e63946] to-[#c1121f] rounded-lg flex items-center justify-center font-display text-[10px] font-bold text-white shrink-0">PDF</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium whitespace-nowrap overflow-hidden text-ellipsis">${f.name}</div>
                        <div class="text-xs text-muted mt-0.5">${formatSize(f.size)}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="w-7 h-7 rounded border border-border flex items-center justify-center text-muted hover:bg-surface2 hover:text-text transition-colors text-sm disabled:opacity-30 disabled:cursor-not-allowed" onclick="moveUp(${i})" title="Move up" ${i===0?'disabled':''}>↑</button>
                        <button class="w-7 h-7 rounded border border-border flex items-center justify-center text-muted hover:bg-surface2 hover:text-text transition-colors text-sm disabled:opacity-30 disabled:cursor-not-allowed" onclick="moveDown(${i})" title="Move down" ${i===files.length-1?'disabled':''}>↓</button>
                        <button class="w-7 h-7 rounded border border-border flex items-center justify-center text-muted hover:bg-[#ff6584]/10 hover:border-accent2 hover:text-accent2 transition-colors text-sm" onclick="removeFile(${i})" title="Remove">✕</button>
                    </div>`;

                // Drag to reorder
                item.addEventListener('dragstart', () => { dragSrcIndex = i; item.classList.add('dragging'); });
                item.addEventListener('dragend', () => item.classList.remove('dragging'));
                item.addEventListener('dragover', e => { e.preventDefault(); item.classList.add('drag-target'); });
                item.addEventListener('dragleave', () => item.classList.remove('drag-target'));
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

            const hasFiles = files.length > 0;
            if (hasFiles) {
                fileListWrap.classList.remove('hidden');
                statsRow.classList.remove('hidden');
                statsRow.classList.add('flex');
            } else {
                fileListWrap.classList.add('hidden');
                statsRow.classList.add('hidden');
                statsRow.classList.remove('flex');
            }
            
            mergeBtn.disabled = files.length < 2;
            document.getElementById('file-count').textContent = `(${files.length})`;
            document.getElementById('stat-files').textContent = files.length;
            document.getElementById('stat-size').textContent = formatSize(files.reduce((a, f) => a + f.size, 0));
        }

        function moveUp(i) { if (i > 0) { [files[i-1], files[i]] = [files[i], files[i-1]]; renderList(); } }
        function moveDown(i) { if (i < files.length-1) { [files[i], files[i+1]] = [files[i+1], files[i]]; renderList(); } }
        function removeFile(i) { files.splice(i, 1); renderList(); if (!files.length) hideStatus(); }
        function clearAll() { files = []; fileInput.value = ''; renderList(); hideStatus(); downloadBtn.classList.add('hidden'); downloadBtn.classList.remove('block'); }

        async function mergePDFs() {
            if (files.length < 2) return;
            mergeBtn.disabled = true;
            downloadBtn.classList.add('hidden');
            downloadBtn.classList.remove('block');
            hideStatus();
            showProgress(0, 'Starting merge...');

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

                showProgress(95, 'Finalizing...');
                const pdfBytes = await merged.save();
                showProgress(100, 'Done!');

                const blob = new Blob([pdfBytes], { type: 'application/pdf' });
                const url = URL.createObjectURL(blob);
                const name = (document.getElementById('output-name').value.trim() || 'merged') + '.pdf';

                downloadBtn.href = url;
                downloadBtn.download = name;
                downloadBtn.textContent = `⬇️ Download ${name} (${formatSize(blob.size)})`;
                downloadBtn.classList.remove('hidden');
                downloadBtn.classList.add('block');

                showStatus(`✅ Successfully merged ${files.length} PDFs into ${name}`, 'success');
            } catch (err) {
                showStatus(`❌ Error: ${err.message || 'Failed to merge PDFs. Make sure all files are valid PDFs.'}`, 'error');
            } finally {
                mergeBtn.disabled = false;
                setTimeout(() => {
                    progressWrap.classList.add('hidden');
                    progressWrap.classList.remove('block');
                }, 1500);
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
            progressText.textContent = text;
        }

        function showStatus(msg, type) {
            statusEl.textContent = msg;
            statusEl.classList.remove('hidden');
            statusEl.classList.add('flex');
            
            if(type === 'success') {
                statusEl.className = 'flex items-center gap-2 p-3 rounded-xl text-sm font-medium mb-4 bg-accent3/10 border border-accent3/30 text-accent3';
            } else {
                statusEl.className = 'flex items-center gap-2 p-3 rounded-xl text-sm font-medium mb-4 bg-accent2/10 border border-accent2/30 text-accent2';
            }
        }
        function hideStatus() { 
            statusEl.classList.add('hidden'); 
            statusEl.classList.remove('flex');
        }

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
            return (bytes/(1024*1024)).toFixed(2) + ' MB';
        }
    </script>
</body>
</html>
