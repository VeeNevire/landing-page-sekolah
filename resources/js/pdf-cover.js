import * as pdfjsLib from 'pdfjs-dist';
import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;

export function isPdfFile(file) {
    return file && (file.type === 'application/pdf' || /\.pdf$/i.test(file.name));
}

window.isPdfFile = isPdfFile;
window.renderPdfCover = renderPdfFirstPage;

export async function renderPdfFirstPage(file) {
    const data = await file.arrayBuffer();
    const loadingTask = pdfjsLib.getDocument({ data });
    const doc = await loadingTask.promise;
    if (!doc.numPages) {
        throw new Error('PDF tanpa halaman');
    }

    const page = await doc.getPage(1);
    const viewport = page.getViewport({ scale: 2 });

    const canvas = document.createElement('canvas');
    canvas.width = Math.min(viewport.width, 1200);
    canvas.height = Math.min(viewport.height, 1600);
    const ratio = canvas.width / viewport.width;
    const scale = viewport.scale * ratio;

    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    await page.render({
        canvasContext: ctx,
        viewport: page.getViewport({ scale }),
    }).promise;

    await loadingTask.destroy();

    return new Promise((resolve, reject) => {
        canvas.toBlob((blob) => {
            if (blob) {
                resolve(blob);
            } else {
                reject(new Error('Gagal membuat gambar cover'));
            }
        }, 'image/png');
    });
}