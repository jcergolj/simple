# Password Reset Artisan Command

Since the forgot password functionality has been removed from the web interface, administrators can reset user passwords using the Artisan command line interface.

## Usage

### Reset password with prompt
```bash
php artisan user:reset-password user@example.com
```
This will prompt you to enter and confirm a new password securely.

### Reset password with inline option
```bash
php artisan user:reset-password user@example.com --password="newpassword123"
```
This sets the password directly (less secure for production).

## Features

- **Secure password prompting**: When no password is provided, the command will securely prompt for the new password
- **Password confirmation**: Requires password confirmation to prevent typos
- **Validation**: Ensures the password meets minimum requirements (8+ characters)
- **User lookup**: Finds users by email address
- **Error handling**: Clear error messages for invalid emails or passwords
- **Success confirmation**: Shows confirmation when password is successfully reset

## Examples

```bash
# Reset password with interactive prompts
php artisan user:reset-password admin@company.com

# Reset password with command option (for scripts)
php artisan user:reset-password user@example.com --password="SecurePassword123"
```

## Security Notes

- The interactive prompt mode (without --password flag) is recommended for security
- When using the --password flag, be aware that the password may be visible in command history
- Only administrators with server access can use this command
- All password changes are hashed using Laravel's secure Hash facade