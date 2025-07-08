# Side Hunt

**Side Hunt** is a modern web-based job marketplace application built with Laravel that connects job creators with skilled workers for various side jobs and freelance opportunities. The platform facilitates seamless job posting, application management, secure transactions, and real-time communication between users.

## 🚀 Features

### For Job Creators
- **Job Posting**: Create detailed job listings with location, salary range, and worker requirements
- **Application Management**: Review, accept, or reject job applications
- **Worker Communication**: Built-in messaging system to communicate with potential workers
- **Payment Management**: Secure transaction processing through integrated wallet system
- **Job Tracking**: Monitor job progress and manage multiple active postings

### For Job Seekers
- **Job Discovery**: Browse available jobs with detailed descriptions and requirements
- **Easy Application**: Simple application process with status tracking
- **Real-time Chat**: Direct communication with job creators
- **Wallet System**: Secure payment reception and balance management
- **Application History**: Track all job applications and their statuses

### Platform Features
- **User Authentication**: Secure login and registration system
- **Admin Panel**: Administrative controls for platform management
- **Responsive Design**: Mobile-friendly interface for all devices
- **Real-time Notifications**: Instant updates for job applications and messages
- **Location Services**: GPS coordinates for job locations
- **Dark Mode**: User preference for UI theme

## 🛠️ Technology Stack

- **Backend**: Laravel (PHP Framework)
- **Frontend**: Blade Templates with JavaScript
- **Database**: MySQL with Eloquent ORM
- **Styling**: CSS/SCSS for responsive design
- **Chat System**: Chatify package for real-time messaging
- **Authentication**: Laravel's built-in authentication
- **Payment System**: Custom wallet implementation with Xendit integration

## 📊 Project Statistics

- **Languages**: 
  - Blade Templates: 51.8%
  - PHP: 27.0%
  - JavaScript: 13.9%
  - CSS: 7.2%
  - SCSS: 0.1%

## 🗄️ Database Architecture

The application uses a well-structured relational database design with the following core entities:

- **Users**: Platform users (both job creators and workers)
- **Side Jobs (Pekerjaans)**: Job postings with detailed requirements
- **Applications (Pelamars)**: Job application management
- **Transactions (Transaksis)**: Financial transaction handling
- **Financial Transactions**: Unified system for payments and payouts
- **Ratings**: User and job rating system
- **Notifications**: Real-time user notifications
- **Chat System**: Real-time messaging infrastructure

For detailed information about database relationships, table structures, and foreign key constraints, please refer to our comprehensive **[Database Relations Documentation](DATABASE.MD)**.

## 🏗️ Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/MariaDB
- Node.js & NPM (for asset compilation)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/AnonGhostID/side-hunt.git
   cd side-hunt
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

5. **Database configuration**
   - Update `.env` file with your database credentials
   - Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
   - Fresh database command: 
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Compile assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## 👥 Default Users

The application comes with pre-seeded users for testing:

- **Admin**: admin@example.com (Password: admin1234)
- **Job Creator**: owner1@example.com (Password: owner1234)
- **Worker**: orang1@example.com (Password: orang1234)

## 🔧 Key Components

### Models & Relationships
- **User Model**: Handles authentication and user relationships
- **Pekerjaan Model**: Manages job postings and creator relationships
- **Pelamar Model**: Handles job applications and status tracking
- **Transaksi Model**: Manages financial transactions between users
- **FinancialTransaction Model**: Unified system for payments and payouts
- **Notification Model**: Handles user notifications
- **Rating Model**: Manages user and job ratings

### Controllers
- **PekerjaanController**: Job management and application processing
- **UsersController**: User authentication and profile management
- **TransaksiController**: Transaction handling and wallet operations
- **ManagementPageController**: Admin and user dashboard management
- **TopUpController**: Wallet top-up functionality
- **PayoutController**: Wallet withdrawal functionality
- **NotificationController**: User notification management

### Middleware & Security
- **Role Middleware**: Role-based access control (user, mitra, admin)
- **Authentication middleware**: Protected routes for authenticated users
- **CSRF protection**: Form submissions security
- **Password hashing**: Secure password storage

## 📱 Usage Flow

1. **Registration**: Users sign up as either job creators or workers
2. **Job Creation**: Creators post jobs with requirements and compensation
3. **Job Discovery**: Workers browse and filter available opportunities
4. **Application Process**: Workers apply to relevant jobs
5. **Selection**: Creators review applications and select workers
6. **Communication**: Built-in chat system for project coordination
7. **Payment**: Secure transactions through the wallet system
8. **Completion**: Job completion and feedback system

## 🤝 Contributing

We welcome contributions to improve Side Hunt! Please feel free to:

- Report bugs and issues
- Suggest new features
- Submit pull requests
- Improve documentation

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

## 📞 Support

For questions, issues, or contributions, please contact the development team or create an issue in this repository.

---

**Side Hunt** - Connecting opportunities with talent, one side job at a time! 🎯