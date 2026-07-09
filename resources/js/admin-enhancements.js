// Enhanced RichEditor Image Controls
// Place this in resources/js/admin-enhancements.js

document.addEventListener('DOMContentLoaded', function() {
    // Wait for TipTap editors to load
    setTimeout(() => {
        enhanceRichEditorImages();
    }, 1000);
});

function enhanceRichEditorImages() {
    const editors = document.querySelectorAll('.tiptap.ProseMirror');
    
    editors.forEach(editor => {
        // Add CSS for better image handling
        const style = document.createElement('style');
        style.textContent = `
            .tiptap.ProseMirror img {
                max-width: 100%;
                height: auto;
                cursor: pointer;
                border: 2px solid transparent;
                transition: border-color 0.2s;
            }
            .tiptap.ProseMirror img:hover {
                border-color: #3b82f6;
            }
            .tiptap.ProseMirror img.img-center {
                display: block;
                margin-left: auto;
                margin-right: auto;
            }
            .tiptap.ProseMirror img.img-right {
                float: right;
                margin-left: 1rem;
            }
        `;
        document.head.appendChild(style);
        
        // Add click handler for alignment
        editor.addEventListener('click', (e) => {
            if (e.target.tagName === 'IMG') {
                const img = e.target;
                
                // Cycle through alignments: left -> center -> right -> left
                if (img.classList.contains('img-center')) {
                    img.classList.remove('img-center');
                    img.classList.add('img-right');
                } else if (img.classList.contains('img-right')) {
                    img.classList.remove('img-right');
                } else {
                    img.classList.add('img-center');
                }
            }
        });
        
        // Add double-click handler for size prompt
        editor.addEventListener('dblclick', (e) => {
            if (e.target.tagName === 'IMG') {
                const img = e.target;
                const currentWidth = img.style.width || img.offsetWidth + 'px';
                const newWidth = prompt('Enter image width (e.g., 300px, 50%, auto):', currentWidth);
                
                if (newWidth) {
                    img.style.width = newWidth;
                }
            }
        });
    });
}
