<?php
$is_embed = ($this->input->get('embed') === '1');
?>
<style>
    /* Base A4 Paper Preview Styling */
    #reportContainer {
        width: 794px;
        max-width: 100%;
        box-sizing: border-box;
        background: #ffffff;
    }
    <?php if ($is_embed): ?>
    body {
        background: #f1f5f9 !important;
        padding: 20px 16px 32px 16px !important;
        margin: 0 !important;
    }
    .standalone-toolbar {
        display: none !important;
    }
    <?php endif; ?>

    /* Clean Print to PDF Styling (A4 Portrait) */
    @media print {
        @page {
            size: A4 portrait;
            margin: 5mm;
        }
        html, body {
            background: #ffffff !important;
            color: #0f172a !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            zoom: 0.88;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .no-print, .standalone-toolbar {
            display: none !important;
        }
        #reportContainer, .print-page {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
        }
        .print-section, tr, tbody tr, .avoid-break {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        thead {
            display: table-header-group !important;
        }
    }
</style>

<script>
    function renderCleanPDF(elementId, filename, isPrint = false, btn = null) {
        if (isPrint) {
            // Native clean print-to-PDF directly (no extra popup window!)
            window.print();
            return;
        }

        let originalText = '';
        if (btn) {
            originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Generating PDF...';
            btn.disabled = true;
        }

        const sourceEl = document.getElementById(elementId);
        if (!sourceEl) {
            if (btn) { btn.innerHTML = originalText; btn.disabled = false; }
            return;
        }

        // Save original inline styles & classes so we can restore after capture
        const origBodyPadding = document.body.style.padding;
        const origBodyMargin = document.body.style.margin;
        const origBodyBg = document.body.style.background;
        const origElStyle = sourceEl.getAttribute('style') || '';
        const hadMxAuto = sourceEl.classList.contains('mx-auto');
        const hadShadow = sourceEl.classList.contains('shadow-xl');
        const hadRounded = sourceEl.classList.contains('rounded-2xl');
        const hadBorder = sourceEl.classList.contains('border');
        const toolbar = document.querySelector('.standalone-toolbar');
        const origToolbarDisplay = toolbar ? toolbar.style.display : '';

        // Snap #reportContainer cleanly to (0, 0) at exact 794px width for zero offset / zero cutoff
        if (toolbar) toolbar.style.display = 'none';
        document.body.style.padding = '0';
        document.body.style.margin = '0';
        document.body.style.background = '#ffffff';
        sourceEl.classList.remove('mx-auto', 'shadow-xl', 'rounded-2xl', 'border');
        sourceEl.style.width = '794px';
        sourceEl.style.minWidth = '794px';
        sourceEl.style.maxWidth = '794px';
        sourceEl.style.margin = '0';
        sourceEl.style.borderRadius = '0';
        sourceEl.style.boxShadow = 'none';
        window.scrollTo(0, 0);

        const cleanup = () => {
            if (toolbar) toolbar.style.display = origToolbarDisplay;
            document.body.style.padding = origBodyPadding;
            document.body.style.margin = origBodyMargin;
            document.body.style.background = origBodyBg;
            sourceEl.setAttribute('style', origElStyle);
            if (hadMxAuto) sourceEl.classList.add('mx-auto');
            if (hadShadow) sourceEl.classList.add('shadow-xl');
            if (hadRounded) sourceEl.classList.add('rounded-2xl');
            if (hadBorder) sourceEl.classList.add('border');
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ type: 'INSTROOM_PDF_DONE' }, '*');
            }
        };

        const captureHeight = sourceEl.scrollHeight || 1122;
        const pageW = 210; // Exact A4 width in mm
        const renderH = Math.ceil((captureHeight / 794) * pageW);
        const pdfPageH = Math.max(297, renderH); // Exact A4 height or seamless single-page fit

        const opt = {
            margin:       0,
            filename:     filename || 'Instroom_Executive_Report.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  {
                scale: 2,
                useCORS: true,
                logging: false,
                width: 794,
                windowWidth: 794,
                height: captureHeight,
                x: 0,
                y: 0,
                scrollX: 0,
                scrollY: 0,
                backgroundColor: '#ffffff'
            },
            jsPDF:        { unit: 'mm', format: [pageW, pdfPageH], orientation: 'portrait', compress: true }
        };

        html2pdf().set(opt).from(sourceEl).save().then(cleanup).catch(err => {
            console.error('PDF Download Error:', err);
            cleanup();
        });
    }

    // Listen for commands from the parent Manager Reports Preview Modal
    window.addEventListener('message', function(event) {
        if (!event.data || !event.data.type) return;
        if (event.data.type === 'INSTROOM_TRIGGER_PRINT') {
            window.print();
        } else if (event.data.type === 'INSTROOM_TRIGGER_DOWNLOAD') {
            const fname = event.data.filename || (document.title.replace(/[^a-zA-Z0-9_-]/g, '_') + '.pdf');
            renderCleanPDF('reportContainer', fname, false, null);
        }
    });
</script>
