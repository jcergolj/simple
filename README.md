# Simple Time Tracker

## 🆕 What I’m Working On

I started this project because I was frustrated with time trackers like Toggl — too many clicks, too many features, and far more complexity than I need. I wanted something **simple, fast, and frictionless**, built with Laravel and Hotwire.

**Key ideas:**  
- Track time with **minimal friction**  
- **Timers remember** your last client/project  
- **Keyboard shortcuts** to start/stop quickly  
- MIT-licensed, open-source, focused on doing **one thing really well**

You **can try it now**, but things are still rough. Waiting until next week gives you a **more polished interface** ready for everyday use. Feedback is very welcome — I’d love to see how it fits your workflow.

> **Status — Early development.** This project is under active development. APIs, database schema, and UX may change and breaking changes are expected. Do not run this in production systems.

A minimal time-tracking app built with Laravel 12 and Hotwire. Track time across clients and projects — no bloat, just tracking.

---

## ✨ Features

### ⏰ Timer Management
- **One-Click Timer**: Start timing with a single click.
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

### 🎨 Modern UI/UX
- **Hotwire-Powered**: Fast, server-rendered interactions with minimal JavaScript.
- **DaisyUI Components**: Clean, accessible UI components.
- **Mobile-First**: Designed for mobile-first use, adapts to larger screens.

### ⚙️ User Preferences
- **Date Format Options**: Choose between US (MM/DD/YYYY), UK (DD/MM/YYYY), or EU (DD.MM.YYYY) formats.
- **Time Format Options**: Select 12-hour (2:30 PM) or 24-hour (14:30) time display.
- **Personalized Display**: All dates and times throughout the app adapt to your preferences.
- **Independent Settings**: Date and time formats can be configured separately for maximum flexibility.

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
- PHP 8.4 or higher

### Quick Start (Automatic)
1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd simple-time-tracker
   ```

2. **Run the installation script**
   ```bash
   ./install.sh
   ```

3. **Create your user account**
   ```bash
   php artisan app:create-user
   ```

4. **Start the development server**
   ```bash
   php artisan serve
   ```

### Manual Installation
If you prefer to install manually:

1. **Install dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

4. **Setup frontend assets**
   ```bash
   php artisan importmap:install
   php artisan importmap:optimize
   php artisan tailwindcss:download
   php artisan tailwindcss:install
   ```

5. **Optimize application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Create user**
   ```bash
   php artisan app:create-user
   ```

---

## ⚡ Quick Reference

### Common Commands
```bash
# User Management
php artisan app:create-user                    # Create your user account
php artisan user:reset-password user@email.com # Reset user password

# Development
php artisan serve                              # Start development server
php artisan migrate                            # Run database migrations
php artisan optimize:clear                     # Clear all caches

# User Preferences (Database Updates)
# Note: Preferences are typically managed through Settings → Preferences in the web UI
# But you can also update them directly in the database if needed:
php artisan tinker                             # Access database directly
# Example: User::first()->update(['date_format' => 'eu', 'time_format' => '24'])

# Analysis & Quality
composer analyse                               # Run all code quality checks
composer run pint                              # Format code
composer run test                              # Run tests
```

### Troubleshooting
```bash
# Clear caches if things seem broken
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Reset everything and optimize
php artisan optimize:clear
php artisan optimize
```

---

## 👤 User Management

This application is designed for **single-user use**. Registration is automatically disabled after the first user is created, making it perfect for personal time tracking.

### Creating Your User Account

After installation, create your user account:

```bash
php artisan app:create-user
```

This interactive command will prompt you for:
- **Name**: Your display name
- **Email**: Your login email address
- **Password**: Secure password (minimum 8 characters)
- **Hourly Rate**: Your default hourly rate (optional)

### Resetting User Password

If you forget your password or need to reset it, you can use the command line:

#### Interactive Password Reset
```bash
php artisan user:reset-password your-email@example.com
```

This command will:
1. Verify the user exists
2. Prompt you to enter a new password (securely hidden)
3. Ask you to confirm the password
4. Validate the password meets requirements (minimum 8 characters)
5. Update the user's password

#### Non-Interactive Password Reset
For automated scripts or when you want to provide the password directly:

```bash
php artisan user:reset-password your-email@example.com --password="your-new-password"
```

⚠️ **Security Warning**: When using the `--password` option, the password may be visible in your shell history. Use the interactive method for better security.

#### Examples
```bash
# Interactive (recommended)
php artisan user:reset-password john@example.com

# Direct password (less secure)
php artisan user:reset-password john@example.com --password="MyNewSecurePassword123"

# If user email has spaces or special characters, use quotes
php artisan user:reset-password "user with spaces@example.com"
```

#### Troubleshooting
- **"User not found"**: Double-check the email address is correct
- **"Password validation failed"**: Ensure password is at least 8 characters
- **"Passwords do not match"**: Make sure you type the same password twice in interactive mode

### Single User Restriction

- Only **one user** is allowed per installation
- **Registration routes are automatically disabled** once a user exists
- The login form **hides the "Create Account" link** when a user exists
- Perfect for **personal use** or **single-person businesses**

### Force Create Additional User

If you need to create another user for testing (not recommended for production):

```bash
php artisan app:create-user --force
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

### Customizing Preferences
Navigate to **Settings → Preferences** to personalize your experience:

#### Date Format Options
Choose how dates appear throughout the application:
- **US Format**: 12/25/2025 (MM/DD/YYYY)
- **UK Format**: 25/12/2025 (DD/MM/YYYY)  
- **EU Format**: 25.12.2025 (DD.MM.YYYY)

#### Time Format Options
Select your preferred time display:
- **12-Hour Format**: 2:30 PM (with AM/PM)
- **24-Hour Format**: 14:30 (military time)

#### Examples of Format Combinations
| Date Format | Time Format | Date Example | DateTime Example |
|-------------|-------------|--------------|------------------|
| **US** + 12-hour | `12/25/2025` | `12/25/2025 2:30 PM` |
| **US** + 24-hour | `12/25/2025` | `12/25/2025 14:30` |
| **UK** + 12-hour | `25/12/2025` | `25/12/2025 2:30 PM` |
| **UK** + 24-hour | `25/12/2025` | `25/12/2025 14:30` |
| **EU** + 12-hour | `25.12.2025` | `25.12.2025 2:30 PM` |
| **EU** + 24-hour | `25.12.2025` | `25.12.2025 14:30` |

All dates and times in reports, time entries, and dashboards will automatically update to match your preferences.

> 💡 **Tip**: You can mix and match date and time formats independently. For example, use EU date format (25.12.2025) with 12-hour time (2:30 PM) if that's your preference!

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

### User Preferences
User preferences (date/time formats, hourly rates) are stored per-user in the database and can be configured through the web interface at **Settings → Preferences**.

#### Default Values
- **Date Format**: US format (MM/DD/YYYY)
- **Time Format**: 12-hour format (with AM/PM)
- **Hourly Rate**: Not set (optional)

#### Database Storage
Preferences are stored in the `users` table:
```sql
-- Date format: 'us', 'uk', or 'eu'
date_format VARCHAR DEFAULT 'us'

-- Time format: '12' or '24'  
time_format VARCHAR DEFAULT '12'
```

---

## 🧪 Analyse

Analyse the project, run Ladrastan, Rector, Pint, tests:
```bash
composer analyse
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

### Date/Time Format Implementation
The date and time formatting system uses:
- **Enums**: `App\Enums\DateFormat` and `App\Enums\TimeFormat` for type safety
- **Blade Components**: `<x-user-date>`, `<x-user-time>`, `<x-user-datetime>` for consistent display
- **User Preferences**: Stored in database and accessed via `User->getPreferredDateFormat()` / `User->getPreferredTimeFormat()`
- **Automatic Updates**: All views automatically respect user preferences without manual formatting

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

### Common Issues

**Forgot Password?**  
Use the command line to reset: `php artisan user:reset-password your-email@example.com`

**Can't Login?**  
Check if your user exists: `php artisan tinker` then `App\Models\User::all()`

**Application Errors?**  
Clear caches: `php artisan optimize:clear` then `php artisan optimize`

Currently, I am looking for someone to help **revamp the UI** to make it truly simple and minimalistic. Contributions and suggestions for design improvements are very welcome.

## 💰 Paid Features

The core functionality of **Simple Time Tracker** remains fully free and open-source under the MIT license.  

Some additional features or modules may be offered as paid extras in the future. These paid modules are optional and do not affect the usability of the free, core application.  

Contributors’ work is fully credited, and monetization is intended to support ongoing development of the project, not restrict access to the free core functionality.
