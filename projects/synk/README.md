# Synk

## What is Synk?

Synk is a web-based application designed to simplify the process of transferring playlists between different music providers. 

Many users hesitate to switch to a new music provider, even if it offers better services, due to the effort invested in curating and personalizing playlists over months or even years. With Synk, that concern is now a thing of the past. 

Let Synk take care of seamlessly transferring your playlists for you!

## Features

- Transfer playlists between Spotify and YouTube Music
- User authentication and profile management
- Modern, responsive web interface
- Real-time playlist synchronization
- Secure OAuth integration with music providers

## Technical Stack

- **Backend**: Laravel 10 (PHP 8.1+)
- **Frontend**: Vue.js 3 with Inertia.js
- **Styling**: Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Cache**: Redis
- **Authentication**: Laravel Breeze with Sanctum
- **Build Tool**: Vite

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP 8.1 or higher
- Composer
- Node.js 16+ and npm
- Redis server
- MySQL or PostgreSQL database

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd synk
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your environment variables**
   Edit `.env` file and set up:
   - Database credentials
   - Redis connection
   - Spotify API credentials
   - YouTube Music API credentials

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

## Development

### Starting the development server

1. **Start the Laravel development server**
   ```bash
   php artisan serve
   ```

2. **Start the Vite development server (in a separate terminal)**
   ```bash
   npm run dev
   ```

3. **Start Redis server**
   ```bash
   redis-server
   ```

### Available commands

- `php artisan serve` - Start Laravel development server
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build production assets
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Reset database and seed with sample data

## API Integration

### Spotify API Setup
1. Create a Spotify Developer account
2. Create a new application
3. Add your redirect URI: `http://localhost:8000/auth/spotify/callback`
4. Add your client ID and secret to `.env`

### YouTube Music API Setup
1. The application uses a custom YTMusicProxy service
2. Configure the proxy URL in your `.env` file
3. Set up YouTube Music authentication

## Testing

Run the test suite:
```bash
php artisan test
```

## Inspiration

I wanted to build and deploy an application with the technology stack that I am currently using. I wanted to build an application from scratch and learn about the intricacies of Laravel.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).
