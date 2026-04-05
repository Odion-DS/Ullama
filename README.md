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

## Deployment with Portainer

### Prerequisites

- Portainer installed and configured
- Optional: NVIDIA GPU Runtime for Ollama (if GPU acceleration is desired)

### Deployment Steps

1. **Create Stack**: Navigate to "Stacks" → "Add stack" in Portainer

2. **Stack Configuration**:
    - Name: `ullama`
    - Build method: Web editor
    - Paste the following Docker Compose content:

```yaml
version: '3.8'

services:
    ullama:
        image: ghcr.io/odion-ds/ullama:main
        container_name: ullama_app
        restart: unless-stopped
        ports:
            - "8080:80"
        env_file:
            - stack.env
        environment:
            - APP_ENV=production
            - APP_DEBUG=false
            - APP_KEY=${APP_KEY}
            - DB_HOST=${DB_HOST:-mysql}
            - DB_PORT=${DB_PORT:-3306}
            - DB_CONNECTION=${DB_CONNECTION:-mysql}
            - DB_DATABASE=${DB_DATABASE:-ullama}
            - DB_USERNAME=${DB_USER:-ullama_user}
            - DB_PASSWORD=${DB_PASSWORD}
            - OLLAMA_BASE_URL=${OLLAMA_HOST:-http://ollama:11434}
            - QUEUE_CONNECTION=database
            - SSO_ENABLED=${SSO_ENABLED:-false}
            - SSO_PROVIDER=${SSO_PROVIDER:-none}
            - SSO_CLIENT_ID=${SSO_CLIENT_ID}
            - SSO_CLIENT_SECRET=${SSO_CLIENT_SECRET}
            - SSO_NAME=${SSO_NAME}
            - SSO_COLOR=${SSO_COLOR}
            - SSO_VERIFY_SSL=${SSO_VERIFY_SSL:-true}
            - SSO_ALLOW_REGISTRATION=${SSO_ALLOW_REGISTRATION:-false}
            - SSO_BASE_URL=${SSO_BASE_URL}
            - SSO_AUTHORIZE_URL=${SSO_AUTHORIZE_URL}
            - SSO_TOKEN_URL=${SSO_TOKEN_URL}
            - SSO_USERINFO_URL=${SSO_USERINFO_URL}
        volumes:
            - ullama_storage:/var/www/html/storage
            - ullama_logs:/var/www/html/storage/logs
        depends_on:
            - mysql
            - ollama
        networks:
            - ullama_network

    queue:
        image: ghcr.io/odion-ds/ullama:main
        container_name: ullama_queue
        restart: unless-stopped
        command: php artisan queue:work --verbose --tries=3 --timeout=120
        env_file:
            - stack.env
        environment:
            - APP_ENV=production
            - APP_DEBUG=false
            - APP_KEY=${APP_KEY}
            - DB_CONNECTION=${DB_CONNECTION:-mysql}
            - DB_HOST=${DB_HOST:-mysql}
            - DB_PORT=${DB_PORT:-3306}
            - DB_DATABASE=${DB_DATABASE:-ullama}
            - DB_USERNAME=${DB_USER:-ullama_user}
            - DB_PASSWORD=${DB_PASSWORD}
            - OLLAMA_BASE_URL=${OLLAMA_HOST:-http://ollama:11434}
            - QUEUE_CONNECTION=database
        volumes:
            - ullama_storage:/var/www/html/storage
            - ullama_logs:/var/www/html/storage/logs
        depends_on:
            - mysql
            - ollama
        networks:
            - ullama_network

    ollama:
        image: ollama/ollama:latest
        container_name: ullama_ollama
        restart: unless-stopped
        env_file:
            - stack.env
        ports:
            - "11434:11434"
        volumes:
            - ollama_data:/root/.ollama
        gpus:
            -   driver: nvidia
                count: all
        environment:
            - NVIDIA_VISIBLE_DEVICES=all
            - NVIDIA_DRIVER_CAPABILITIES=compute,utility
        networks:
            - ullama_network

    mysql:
        image: mysql:8.0
        container_name: ullama_mysql
        restart: unless-stopped
        env_file:
            - stack.env
        environment:
            MYSQL_DATABASE: ${DB_DATABASE:-ullama}
            MYSQL_USER: ${DB_USER:-ullama_user}
            MYSQL_PASSWORD: ${DB_PASSWORD}
            MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
        volumes:
            - mysql_data:/var/lib/mysql
        networks:
            - ullama_network

volumes:
    ullama_storage:
    ullama_logs:
    ollama_data:
    mysql_data:

networks:
    ullama_network:
        driver: bridge
```

3. **Configure Environment Variables**:

   Scroll to "Environment variables" and add the following variables:

   **Required Variables (must be set):**

    - `APP_KEY`: Laravel Application Key
        - Generation: Run the following command:
          `docker run --rm ghcr.io/odion-ds/ullama:main php artisan key:generate --show`
        - Format: `base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`

    - `DB_PASSWORD`: Database Password
        - Choose a secure password (minimum 16 characters recommended)
        - Example: `MySecureDbPassword2024!`

   **Optional Variables:**

    - `DB_DATABASE=ullama` (default)
    - `DB_USER=ullama_user` (default)
    - `DB_HOST=mysql` (default)
    - `DB_PORT=3306` (default)
    - `OLLAMA_HOST=http://ollama:11434` (default)

   **SSO Configuration (optional):**

   If you want to enable SSO, see the "SSO Configuration" section above.

4. **Deploy Stack**: Click "Deploy the stack"

5. **Access**:
    - The application will be available at `http://your-server-ip:8080` after startup
    - Create an admin user with: `docker exec -it ullama_app php artisan make:filament-user`

### Important Notes

- **GPU Support**: If no NVIDIA GPU is available, remove the `gpus` block from the `ollama` service
- **Port Adjustment**: Change `8080:80` to another port if 8080 is already in use
- **Security**: Use strong passwords for `DB_PASSWORD` in production environments
- **Persistence**: Volumes (`ullama_storage`, `mysql_data`, `ollama_data`) are automatically created and persist across
  stack updates

## Side Notes

Why did I make this?
This was just a small weekend project. I made it because I wanted to restrict access to Ollama for some educational
projects and other private projects. And when you think why has this small application SSO, this is because I like my
Authentik and I hate when an app hasn't SSO

## AI Disclaimer

Not everything in this project is written by me. Some features, like the model download mechanic or parts of the README,
were created with AI
assistance.

## License

Code released under the [MIT license](https://opensource.org/licenses/MIT).
