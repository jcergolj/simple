# Simple Time Tracker

## 🆕 What I’m Working On

I started this project because I was frustrated with time trackers like Toggl — too many clicks, too many features, and far more complexity than I need. I wanted something **simple, fast, and frictionless**, built with Laravel and Hotwire.

**Key ideas:**  
- Track time with **minimal friction**  
- **Timers remember** your last client/project  
- **Keyboard shortcuts** to start/stop quickly  
- MIT-licensed, open-source, focused on doing **one thing really well**

You **can try it now**, but things are still rough. Waiting until next week gives you a **more polished interface** ready for everyday use. Feedback is very welcome — I’d love to see how it fits your workflow.

> **Status — Early development.** This project is under active development. APIs, database schema, and UX may change and breaking changes are expected. Do not run this in critical production systems without backups.

A minimal time-tracking app built with Laravel 12 and Hotwire. Track time across clients and projects — no bloat, just tracking.

---

## ✨ Features

### ⏰ Timer Management
- **One-Click Timer**: Start timing with a single click (or tap).
- **Single Active Session**: Only one timer runs at a time — prevents accidentally starting multiple timers.
- **Page Refresh Continuation**: Active timers persist through page refreshes (browser reload safe).
- **Keyboard Shortcuts**: `Ctrl+Shift+S` (start) and `Ctrl+Shift+T` (stop).

### 👥 Client & Project Management
- **Auto-Preselection**: The last-used client/project is preselected for quick reuse.
- **Client Association**: Link projects to clients for clearer organization.
- **Hourly Rates**: Set hourly rates at client or project level (currency support included).

### 📊 Dashboard & Analytics
- **Weekly Summary**: Overview of total hours and earnings for the week.
- **Recent Activities**: Quickly view your most recent time entries.
- **Responsive Design**: Layout adapts to desktop, tablet, and mobile.

### 📈 Reporting & Export
- **Flexible Filtering**: Filter entries by client, project, or date range.
- **CSV Export**: Export entries to CSV with totals for hours and earnings.
- **Earnings Calculation**: Earnings are calculated automatically from hourly rates.
- **Time Summaries**: Per-period summaries showing total hours and earnings.

### 🎨 Modern UI/UX
- **Hotwire-Powered**: Fast, server-rendered interactions with minimal JavaScript.
- **DaisyUI Components**: Clean, accessible UI components.
- **Mobile-First**: Designed for mobile-first use, adapts to larger screens.

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12 (tested on 12.30.1)
- **PHP Version**: 8.2+
- **Database**: SQLite (default)
- **Frontend**: Hotwire (Turbo + Stimulus)
- **CSS Framework**: Tailwind CSS + DaisyUI
- **JavaScript**: Vanilla JS with Stimulus controllers

### Key Dependencies
- `hotwired-laravel/turbo-laravel` — Hotwire Turbo integration
- `hotwired-laravel/stimulus-laravel` — Stimulus JavaScript framework
- `tonysm/tailwindcss-laravel` — Tailwind CSS integration
- `tonysm/importmap-laravel` — Modern JavaScript without bundling
- `blade-ui-kit/blade-heroicons` — SVG icons

---

## 🚀 Installation

### Requirements
- PHP 8.2 or higher  
- Composer

### Quick Start
1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd simple-time-tracker
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

---

## 📱 Usage

### Starting a Timer
1. Navigate to the dashboard.  
2. Select or search for a client.  
3. Optionally select a project.  
4. Click the green play button or use `Ctrl+Shift+S`.

### Managing Time Entries
- **View Recent Entries**: At the bottom of the dashboard.  
- **Edit Entries**: Click an entry to modify and save changes.  
- **Add Manual Entries**: Use the Time Entries page to log entries manually.  

### Generating Reports
1. Go to the Reports section.  
2. Filter by client, project, or date range.  
3. View summaries or export to CSV.  
4. CSV includes individual entries and a totals row.  

### Creating Clients & Projects
- **Inline Creation**: Start typing in search fields and create if no results are found.  
- **Dedicated Pages**: Manage clients and projects in bulk.  
- **Rate Management**: Set hourly rates at client or project level.  

---

## 🔧 Configuration

### Environment Variables
Set options in `.env`:

```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Application
APP_NAME="Simple Time Tracker"
APP_URL=http://localhost:8000

# Timezone
APP_TIMEZONE=UTC
```

> ⚠️ Adjust `APP_TIMEZONE` to your local timezone.

### Customization
- **Themes**: Modify `tailwind.config.js`  
- **Components**: Extend DaisyUI components in `resources/css/app.css`  
- **Stimulus Controllers**: Add custom JavaScript in `resources/js/controllers/`  

---

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

---

## 🔍 Code Quality

This project includes tools for consistent code quality:

- **Laravel Pint**: Code formatting (`composer run pint`)  
- **Larastan**: Static analysis (`composer run analyse`)  
- **Rector**: Automated refactoring (`composer run rector`)  

---

## 📚 Architecture

### Directory Structure
```
app/
├── Http/Controllers/
│   ├── Turbo/           # Hotwire-specific controllers
│   └── Api/             # API endpoints
├── Models/              # Eloquent models
└── Requests/            # Form request validation

resources/
├── js/
│   └── controllers/     # Stimulus controllers
├── turbo/               # Turbo-specific views
└── views/               # Standard Blade views
```

### Key Design Patterns
- **Hotwire-First**: Minimal JavaScript, server-rendered HTML.  
- **Component Architecture**: Reusable Blade components.  
- **Single Responsibility**: Focused controllers and models.  
- **Progressive Enhancement**: Works even without JavaScript.  

---

## 🤝 Contributing

1. Fork the repository.  
2. Create a feature branch (`git checkout -b feature/your-feature`).  
3. Commit your changes (`git commit -m 'Add feature'`).  
4. Push to the branch (`git push origin feature/your-feature`).  
5. Open a Pull Request.  

Please run tests and code quality checks before submitting.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🆘 Support

For support, open an issue in the GitHub repository.  

Currently, I am looking for someone to help **revamp the UI** to make it truly simple and minimalistic. Contributions and suggestions for design improvements are very welcome.

## 💰 Paid Features

The core functionality of **Simple Time Tracker** remains fully free and open-source under the MIT license.  

Some additional features or modules may be offered as paid extras in the future. These paid modules are optional and do not affect the usability of the free, core application.  

Contributors’ work is fully credited, and monetization is intended to support ongoing development of the project, not restrict access to the free core functionality.
