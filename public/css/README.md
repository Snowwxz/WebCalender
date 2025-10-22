# CSS Structure Documentation

## Overview
CSS telah dipisahkan menjadi file-file terpisah berdasarkan komponen untuk meningkatkan maintainability dan organization.

## File Structure

### Base Files
- **`base.css`** - Base styles, common utilities, dan global styles
  - Reset CSS
  - Typography (Poppins font)
  - Common button styles
  - Form styles
  - Card styles
  - Alert styles
  - Utility classes

### Component Files

#### Layout Components
- **`header.css`** - Header navigation dan user profile dropdown
- **`sidebar.css`** - Sidebar navigation dan mini calendar

#### Dashboard Components
- **`dashboard-bulan.css`** - Dashboard bulan view styles
- **`dashboard-hari.css`** - Dashboard hari view styles
- **`dashboard-tahun.css`** - Dashboard tahun view styles

#### Feature Components
- **`approve.css`** - Approval page styles
- **`agenda-create.css`** - Agenda creation form styles
- **`super-admin.css`** - Super admin page styles

## Usage

### In Blade Templates

#### For pages using `layouts.main`:
```blade
@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/component-name.css') }}">
@endpush
```

#### For standalone pages:
```blade
<!-- Base CSS -->
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<!-- Component CSS -->
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
<link rel="stylesheet" href="{{ asset('css/component-name.css') }}">
```

## File Dependencies

### Always Required:
- `base.css` - Contains fundamental styles

### Layout Dependencies:
- Pages with header: `header.css`
- Pages with sidebar: `sidebar.css`

### Component Dependencies:
- Each component file is independent and can be included as needed

## Maintenance

### Adding New Styles:
1. Identify the appropriate component file
2. If it's a new component, create a new CSS file
3. Update the corresponding Blade template to include the CSS

### Modifying Existing Styles:
1. Locate the relevant component file
2. Make changes within that file
3. Test across all pages that use the component

### Best Practices:
- Keep styles modular and component-specific
- Use consistent naming conventions
- Document complex CSS rules
- Test responsive design across all breakpoints

## Backup
Original `style.css` has been backed up as `style.css.backup` for reference.
