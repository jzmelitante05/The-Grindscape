# The Grindscape – Final Integrative Web Project

## Project Overview

The Grindscape is a responsive multi-page coffee shop website created as a Final Integrative Web Project for the subject Web Development Fundamentals (A.Y. 2025–2026).

This project demonstrates the integration of:

- Bootstrap 5 Responsive Design
- Custom CSS Styling
- JavaScript Form Validation
- Git & GitHub Version Control
- GitHub Pages Deployment
- PHP Back-End Form Handling

The website was designed with a modern coffee shop aesthetic featuring responsive layouts, animated sections, interactive navigation, modal forms, and themed menu sections.

---

## Live Website

GitHub Pages URL:

https://jzmelitante05.github.io/The-Grindscape/

---

## GitHub Repository

Repository URL:

https://github.com/jzmelitante05/The-Grindscape

---

## Features

### Responsive Navigation
- Bootstrap 5 responsive navbar
- Mobile hamburger menu
- Smooth navigation links

### Hero Sections
- Landing page hero banner
- Call-to-action buttons
- Responsive typography

### Menu & Variety Pages
- Coffee menu cards
- Seasonal specials
- Gallery section
- Coffee origin showcase

### Bootstrap Components Used
- Navbar
- Cards
- Grid System
- Modal
- Buttons
- Forms
- Alerts
- Responsive Utilities

### Form Validation
- Bootstrap `.needs-validation`
- Required field validation
- Character limits
- Age validation
- Dynamic feedback messages

### Custom Styling
- Custom CSS variables
- Coffee-inspired color palette
- Custom animations
- Responsive layouts

### JavaScript Features
- Modal interactions
- Character counter
- Form validation handling
- Reveal animations

### PHP Back-End
- Handles contact form submission
- Uses `$_POST`
- Sanitizes inputs using `htmlspecialchars()`
- Displays confirmation messages
- Includes server-side validation

---

## Technologies Used

- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- PHP
- Git
- GitHub Pages

---

## Project Structure

```plaintext
The-Grindscape/
│
├── index.html
├── history.html
├── variety.html
├── contact.html
├── contact.php
│
├── css/
│   └── custom.css
│
├── js/
│   └── main.js
│
├── README.md
└── .gitignore
```

---

## How to Run the Project

### Static Website

Open:

```plaintext
index.html
```

Or visit the live GitHub Pages link.

---

## Running the PHP Feature Locally

GitHub Pages does not support PHP execution.

To run the PHP functionality locally:

### Option 1 — Using XAMPP

1. Install XAMPP
2. Place the project folder inside:

```plaintext
htdocs/
```

3. Start:
- Apache

4. Open browser:

```plaintext
http://localhost/The-Grindscape/
```

---

### Option 2 — Using PHP Built-in Server

Open terminal inside the project folder:

```bash
php -S localhost:8000
```

Then open:

```plaintext
http://localhost:8000
```

---

## Git & GitHub Workflow

This project uses:

- Git version control
- GitHub repository hosting
- Feature branch workflow

### Example Commands

```bash
git init
git add .
git commit -m "Initial project setup"
git branch feature/php-form
git checkout feature/php-form
git merge feature/php-form
git push origin main
```

---

## Bootstrap Validation

The contact form uses:

```html
needs-validation
```

and JavaScript `checkValidity()` for client-side validation.

Server-side validation is additionally handled in `contact.php`.

---

## Security Features

The PHP form handler sanitizes user input using:

```php
htmlspecialchars()
```

to help prevent basic XSS vulnerabilities.

---

## Academic Information

Course:
Web Development Fundamentals

Academic Year:
2025–2026

Project Type:
Final Integrative Web Project

Instructor:
Edward James V. Grageda

---

## Developers

Created by:
Castro, Macadagdag, Matacot, Melitante, and Pajimola

---

## Notes

- Fully responsive design
- Optimized for desktop and mobile devices
- Built for academic purposes
- Developed using Bootstrap 5 and PHP fundamentals

---
