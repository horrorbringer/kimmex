# RichEditor Image Enhancement

## Overview

Filament's default RichEditor does not support image resizing or repositioning out of the box. This project includes a JavaScript enhancement that adds these capabilities.

## Features

### 1. **Image Alignment** (Single Click)
- **Click once** on any image to cycle through alignments:
  - First click: **Center** the image
  - Second click: **Align right** (with text wrapping)
  - Third click: **Align left** (default)

### 2. **Image Resizing** (Double Click)
- **Double-click** on any image to open a prompt
- Enter the desired width:
  - `300px` - fixed pixel width
  - `50%` - percentage of container width
  - `auto` - reset to original size

### 3. **Visual Feedback**
- Images show a blue border on hover
- Cursor changes to pointer when hovering over images

## Usage Guide

### For Content Editors

1. **Insert an image** using the RichEditor's file attachment button
2. **Resize the image**: Double-click it and enter a width value
3. **Align the image**: Click once to center, twice to align right, three times to reset to left

### Technical Details

The enhancement is automatically loaded in the admin panel via:
- **Script**: `resources/js/admin-enhancements.js`
- **Loaded in**: `app/Providers/Filament/AdminPanelProvider.php`
- **Compiled by**: Vite (see `vite.config.js`)

### How It Works

The script:
1. Waits for TipTap editors (Filament's rich text editor) to load
2. Monitors for new images added to the editor
3. Attaches click and double-click handlers to each image
4. Applies CSS classes for alignment and inline styles for sizing

### Limitations

- **Not WYSIWYG for alignment**: The alignment classes work in the editor, but final rendering depends on your frontend CSS
- **Manual save required**: Changes are reflected in the editor immediately, but you must save the form to persist them
- **Browser-only**: This is a client-side enhancement and requires JavaScript

## Frontend Rendering

To ensure images display correctly on your public website, add these styles to your frontend CSS:

```css
/* In resources/css/app.css or your main stylesheet */

.content img.img-center {
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.content img.img-right {
    float: right;
    margin-left: 1rem;
    margin-bottom: 1rem;
}
```

Or add them directly to your content rendering views:

```blade
<!-- Example in resources/views/pages/news/show.blade.php -->
<style>
    .content img.img-center {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .content img.img-right {
        float: right;
        margin-left: 1rem;
    }
</style>

<div class="content">
    {!! $article->content !!}
</div>
```

## Alternative: Manual HTML

For more control, editors can switch to the RichEditor's source code view and manually add inline styles:

```html
<!-- Center an image -->
<img src="image.jpg" style="display: block; margin: 0 auto; width: 500px;">

<!-- Right align with text wrap -->
<img src="image.jpg" style="float: right; margin-left: 20px; width: 400px;">

<!-- Custom size -->
<img src="image.jpg" style="width: 300px; height: auto;">
```

## Future Improvements

Potential enhancements:
- Add visual resize handles (drag corners)
- Add alignment toolbar buttons
- Add caption support
- Integrate with Filament's native action system

## References

- **Filament Issue**: [#16860 - RichEditor Can't center or change the size/position of images](https://github.com/filamentphp/filament/issues/16860)
- **TipTap Image Extension**: https://tiptap.dev/api/nodes/image
- **Custom TipTap Extensions**: https://tiptap.dev/guide/custom-extensions
