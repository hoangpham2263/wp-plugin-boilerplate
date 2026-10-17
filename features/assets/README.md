# QHW Plugin Features - Assets Directory

This directory contains all CSS, JavaScript, and image assets for the QHW Plugin features.

## 📁 Directory Structure

```
assets/
├── css/                    # CSS files
│   ├── features.css        # Main features styling
│   ├── tab-styles.css     # Tab navigation styling
│   └── notifications.css  # Notification system styling
├── js/                     # JavaScript files
│   └── features.js        # Main features functionality
├── images/                 # Image assets
│   └── (future images)    # Placeholder for future images
└── README.md              # This file
```

## 🎨 CSS Files

### **features.css**
- Main styling for all feature components
- Feature cards, sections, and layouts
- Responsive design rules
- Hover effects and animations

### **tab-styles.css**
- Tab navigation styling
- Tab content layouts
- Active tab highlighting
- Mobile-responsive tab design

### **notifications.css**
- Notification system styling
- Success, error, warning, info states
- Mobile-responsive notifications
- Smooth animations

## ⚡ JavaScript Files

### **features.js**
- Tab system functionality
- Interactive elements
- Responsive behavior
- Public API for external use

## 🖼️ Image Assets

The `images/` directory is reserved for future image assets such as:
- Icons and graphics
- Feature screenshots
- UI elements
- Branding materials

## 🔧 Usage

### **Loading Assets**
Assets are automatically loaded by `f0-features-handler.php`:

```php
// CSS files
wp_enqueue_style('qhw-features-styles', ...);
wp_enqueue_style('qhw-tab-styles', ...);
wp_enqueue_style('qhw-notification-styles', ...);

// JavaScript files
wp_enqueue_script('qhw-features-script', ...);
```

### **Adding New Assets**
1. Place new CSS files in `css/` directory
2. Place new JS files in `js/` directory
3. Place new images in `images/` directory
4. Update `f0-features-handler.php` to load new assets

## 📱 Responsive Design

All assets are designed to be mobile-responsive:
- CSS uses media queries for different screen sizes
- JavaScript adjusts behavior based on viewport
- Touch-friendly interactions on mobile devices

## 🎯 Best Practices

- **CSS**: Use BEM methodology for class naming
- **JavaScript**: Follow WordPress coding standards
- **Images**: Optimize for web use
- **Performance**: Minify assets for production

## 🔄 Version Control

Assets are versioned using WordPress enqueue system:
- CSS files: version 1.0.0
- JS files: version 1.0.0
- Update versions when making significant changes
