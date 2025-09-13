# TechSite - Modern Static Website

## Overview

TechSite is a modern, component-based static website built for Ideovent Technologies. It showcases web development services through a responsive, Bootstrap-powered interface with modular HTML components and a sophisticated design system. The site features dynamic theme switching, smooth animations, and a professional service-oriented layout with comprehensive navigation structure.

## Recent Changes

### December 2024 - Navigation & Content Expansion
- **Comprehensive Navigation**: Implemented dropdown menu system with Services, Industries, Solutions, Insights, and About Us sections
- **New Pages Created**: Five main pages with detailed content covering all business verticals
- **Path Resolution**: Updated to root-relative URL structure for consistent navigation across all page levels
- **Dropdown Styling**: Advanced CSS styling with dark mode support and mobile-responsive design
- **Content Organization**: Structured content with hero sections, detailed service descriptions, and call-to-action elements

### September 2025 - Individual Dedicated Pages Implementation
- **Services Pages Complete**: Created all 9 individual service pages with comprehensive content (App Development, Web Development, UI/UX Design, Custom Development, Digital Marketing, Social Media Marketing, IT Consulting, Maintenance & Support, SEO Optimization)
- **Individual Page Structure**: Organized pages in subdirectories (`pages/services/`, `pages/industries/`, etc.) for better structure and scalability
- **Navigation Update**: Updated all navbar dropdown links to point to individual pages instead of section anchors
- **Representative Pages**: Created key pages for other categories - Banking & Financial (industries), CRM & ERP (solutions), Portfolio & Blogs (insights), Who We Are & Leadership Team (about)
- **Consistent Template System**: Established consistent page template with hero sections, detailed content, and call-to-action elements
- **SEO Optimization**: Each page includes proper meta tags, descriptions, and semantic HTML structure

### September 2025 - Latest Insights & Blogs Component
- **Blog Section Addition**: Added comprehensive "Latest Insights & Blogs" component to home page with professional design
- **6-Card Grid Layout**: Featured 3x2 grid showcasing AI Revolution in Software Testing, Modern QA Operating Models, Modernizing QA Stack, Cloud Native Testing, QA Advisory Partnership, and QA Roadmap Building
- **Unique Card Styling**: Creamy-beige background (#FFFBEB) with soft borders, rounded corners, and smooth hover animations
- **Professional Images**: High-quality Unsplash images for each blog post with proper alt text and responsive design
- **Interactive Elements**: Mustard-yellow "Read More" links with hover effects and unified "View All Blogs" call-to-action button
- **Mobile Responsive**: Bootstrap 5 grid system ensuring perfect layout across desktop (3-column), tablet (2-column), and mobile (1-column)

### September 2025 - Advanced Hero Section Enhancement
- **Dynamic Background**: Replaced static background with animated gradient using deep blue (#0D2240) to primary blue (#0D6EFD) with slow 15s animation cycle
- **Text Animations**: Added staggered fade-in-up animations for headline, subheadline, buttons, and social proof elements with proper timing delays
- **Layout Optimization**: Converted to modern 2-column layout (6-6) with better mobile responsiveness and centered/left-aligned text
- **Interactive Elements**: Implemented mouse parallax effect on video wrapper for desktop users with smooth transform animations
- **Animated Counter**: Added JavaScript-powered satisfaction counter that animates from 0% to 98% using Intersection Observer for performance
- **Enhanced Buttons**: Updated to rounded pill-style buttons with improved hover effects and proper anchor navigation
- **Mobile Performance**: Smart video/image switching - professional team image placeholder for desktop, optimized fallback image for mobile
- **Visual Hierarchy**: Updated typography with display-3 headline, improved spacing, and better contrast with animated background

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Framework**: Vanilla HTML/CSS/JavaScript with Bootstrap 5.3.2
- **Component System**: Modular HTML components loaded dynamically via JavaScript
- **Build Pattern**: Static site with client-side component assembly
- **Styling**: CSS component architecture with global variables and theme support

### Design System
- **Theme Management**: CSS custom properties with light/dark mode switching
- **Color Palette**: Primary (#6366f1) and secondary (#8b5cf6) with gradient combinations
- **Typography**: Inter font family with consistent weight hierarchy
- **Spacing**: Bootstrap utility classes with custom component spacing

### Component Architecture
- **Modular Components**: Separate HTML files for reusable UI elements (navbar, hero, cards, footer)
- **Dynamic Loading**: JavaScript-based component loader that fetches and injects HTML partials
- **Template Customization**: Service cards and feature cards support dynamic content injection
- **State Management**: Local storage for theme persistence
- **Navigation System**: Bootstrap 5 dropdown menus with root-relative path resolution

### Page Structure
- **Home Page**: Main landing page with hero section, services overview, and testimonials
- **Services Page**: Comprehensive service offerings including app development, web development, UI/UX design, digital marketing
- **Industries Page**: Sector-specific solutions for banking, healthcare, retail, education, and more
- **Solutions Page**: Business solutions including ERP, CRM, HRMS, POS, and digital transformation services  
- **Insights Page**: Knowledge hub with blogs, case studies, portfolio showcase, and testimonials
- **About Page**: Company information, leadership team, partnerships, and core values

### Development Setup
- **Build Tool**: http-server for local development
- **File Structure**: Organized by component type with separate CSS files per component
- **Asset Management**: CDN-based Bootstrap and icons, local CSS and JavaScript

### User Experience Features
- **Responsive Design**: Mobile-first approach with Bootstrap grid system
- **Theme Toggle**: Light/dark mode with icon switching and local storage persistence
- **Smooth Scrolling**: Anchor-based navigation with offset calculations
- **Interactive Elements**: Hover animations, card transitions, and button effects

## External Dependencies

### CDN Resources
- **Bootstrap CSS**: v5.3.2 for responsive framework and utilities
- **Bootstrap Icons**: v1.11.1 for iconography system
- **Google Fonts**: Inter font family (referenced but not explicitly loaded)

### Development Dependencies
- **http-server**: v14.1.1 for local development server
- **Node.js**: Package management and script execution

### Image Assets
- **Unsplash**: External service for placeholder images (client avatars, testimonials)
- **Local Assets**: Hero background and component images (referenced but files not present)

### Browser APIs
- **Local Storage**: Theme preference persistence
- **Fetch API**: Dynamic component loading
- **Intersection Observer**: Potential scroll animations (referenced in architecture)