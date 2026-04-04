<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## SSO Configuration

This application supports flexible Single Sign-On (SSO) authentication through environment variables only. No config file modifications are needed during installation.

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

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `SSO_ENABLED` | Yes | `false` | Enable/disable SSO functionality |
| `SSO_PROVIDER` | Yes | - | Provider identifier (e.g., `authentik`, `keycloak`, `github`) |
| `SSO_CLIENT_ID` | Yes | - | OAuth client ID |
| `SSO_CLIENT_SECRET` | Yes | - | OAuth client secret |
| `SSO_NAME` | No | `SSO Login` | Display name for the SSO button |
| `SSO_COLOR` | No | Blue | Hex color for the SSO button (e.g., `#2f2a6b`) |
| `SSO_ALLOW_REGISTRATION` | No | `false` | Allow new users to register via SSO |
| `SSO_VERIFY_SSL` | No | `true` | Verify SSL certificates (set to `false` for dev with custom CA) |
| `SSO_BASE_URL` | No* | - | Base URL for OIDC auto-discovery |
| `SSO_AUTHORIZE_URL` | No* | - | Authorization endpoint (manual override) |
| `SSO_TOKEN_URL` | No* | - | Token endpoint (manual override) |
| `SSO_USERINFO_URL` | No* | - | User info endpoint (manual override) |

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

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
