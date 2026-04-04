<p align="center"><a href="https://laravel.com" target="_blank"><img style="background: white; padding: 30px; border-radius: 30px" src="public/img/logo.png" height="200" alt="Laravel Logo"></a></p>

# Ullama

## SSO Configuration

This application supports flexible Single Sign-On (SSO) authentication through environment variables only. No config
file modifications are needed during installation.

### Environment Variables

Configure SSO by setting these variables in your `.env` file:

```env
# Basic SSO Settings
SSO_ENABLED=true
SSO_PROVIDER=authentik
SSO_CLIENT_ID=your-client-id
SSO_CLIENT_SECRET=your-client-secret
SSO_NAME="Sign in with Authentik"
SSO_COLOR=#2f2a6b
SSO_ALLOW_REGISTRATION=false
SSO_VERIFY_SSL=true
```

#### OIDC Providers (Authentik, Keycloak, Auth0, etc.)

For OIDC providers with auto-discovery support:

```env
SSO_BASE_URL=https://authentik.example.com
```

#### Plain OAuth2 Providers (GitHub, Facebook, etc.)

For providers without OIDC discovery, specify URLs manually:

```env
SSO_AUTHORIZE_URL=https://github.com/login/oauth/authorize
SSO_TOKEN_URL=https://github.com/login/oauth/access_token
SSO_USERINFO_URL=https://api.github.com/user
```

### Configuration Options

| Variable                 | Required | Default     | Description                                                     |
|--------------------------|----------|-------------|-----------------------------------------------------------------|
| `SSO_ENABLED`            | Yes      | `false`     | Enable/disable SSO functionality                                |
| `SSO_PROVIDER`           | Yes      | -           | Provider identifier (e.g., `authentik`, `keycloak`, `github`)   |
| `SSO_CLIENT_ID`          | Yes      | -           | OAuth client ID                                                 |
| `SSO_CLIENT_SECRET`      | Yes      | -           | OAuth client secret                                             |
| `SSO_NAME`               | No       | `SSO Login` | Display name for the SSO button                                 |
| `SSO_COLOR`              | No       | Blue        | Hex color for the SSO button (e.g., `#2f2a6b`)                  |
| `SSO_ALLOW_REGISTRATION` | No       | `false`     | Allow new users to register via SSO                             |
| `SSO_VERIFY_SSL`         | No       | `true`      | Verify SSL certificates (set to `false` for dev with custom CA) |
| `SSO_BASE_URL`           | No*      | -           | Base URL for OIDC auto-discovery                                |
| `SSO_AUTHORIZE_URL`      | No*      | -           | Authorization endpoint (manual override)                        |
| `SSO_TOKEN_URL`          | No*      | -           | Token endpoint (manual override)                                |
| `SSO_USERINFO_URL`       | No*      | -           | User info endpoint (manual override)                            |

\* Either `SSO_BASE_URL` (for OIDC) or all three manual URLs (for OAuth2) must be provided.

### Example: Authentik Setup

1. Create an OAuth2/OIDC provider in Authentik
2. Configure redirect URI: `https://yourdomain.com/auth/callback`
3. Set environment variables:

```env
SSO_ENABLED=true
SSO_PROVIDER=authentik
SSO_CLIENT_ID=your-authentik-client-id
SSO_CLIENT_SECRET=your-authentik-client-secret
SSO_BASE_URL=https://authentik.yourdomain.com/application/o/your-app
SSO_NAME="Sign in with Authentik"
SSO_COLOR=#fd4b2d
SSO_ALLOW_REGISTRATION=false
```

### Security Notes

- **Production**: Always set `SSO_VERIFY_SSL=true` in production
- **Registration**: Set `SSO_ALLOW_REGISTRATION=false` to prevent unauthorized user creation
- **Development**: Only disable SSL verification (`SSO_VERIFY_SSL=false`) in development with self-signed certificates

### Manual User Creation

If you need to create users manually (e.g., when SSO registration is disabled), you can use the Filament command:

```bash
php artisan make:filament-user
```

This command works in both development and production environments and allows you to create admin users directly.

## Side Notes

Why did I make this?
This was just a small weekend project. I made it because I wanted to restrict access to Ollama for some educational projects and other private projects. And when you think why has this small application SSO, this is because I like my Authentik and I hate when an app hasn't SSO

## AI Disclaimer

Not everything in this project is written by me. Some features, like the model download mechanic, were created with AI
assistance.

## License

Code released under the [MIT license](https://opensource.org/licenses/MIT).
