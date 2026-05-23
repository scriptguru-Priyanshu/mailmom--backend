# MailMoM.ai

> AI-powered Minutes of Meeting (MoM) Generator & Auto Mailer

MailMoM.ai helps teams automatically generate structured Minutes of Meeting (MoM), extract action items, assign tasks, and send summary emails to participants.

---

# ✨ Features

* 🧠 AI-generated meeting summaries
* ✅ Automatic action item extraction
* 👥 Participant management
* 📧 Auto-mailing of meeting summaries
* 📌 Task assignment system
* 🔐 Authentication with Laravel Sanctum
* ⚡ Modern Next.js frontend
* 🚀 REST API powered by Laravel

---

# 🏗️ Project Structure

```bash
MailMoM.ai/
│
├── backend/     # Laravel API
└── frontend/    # Next.js frontend
```

---

# 🛠️ Tech Stack

## Frontend

* Next.js
* React
* Tailwind CSS
* Axios

## Backend

* Laravel
* Sanctum Authentication
* MySQL
* Queue Jobs
* Mail System

---

# 🚀 Getting Started

## 1. Clone Repository

```bash
git clone https://github.com/your-username/mailmom-ai.git
cd mailmom-ai
```

---

# ⚙️ Backend Setup (Laravel)

## Navigate to backend

```bash
cd backend
```

## Install dependencies

```bash
composer install
```

## Copy environment file

```bash
cp .env.example .env
```

## Generate application key

```bash
php artisan key:generate
```

## Configure database

Update your `.env` file:

```env
DB_DATABASE=mailmom
DB_USERNAME=root
DB_PASSWORD=
```

---

## Run migrations

```bash
php artisan migrate
```

---

## Start backend server

```bash
php artisan serve
```

Backend will run on:

```bash
http://127.0.0.1:8000
```

---

# 🌐 Frontend Setup (Next.js)

## Navigate to frontend

```bash
cd frontend
```

## Install dependencies

```bash
npm install
```

---

## Configure environment variables

Create:

```bash
.env.local
```

Add:

```env
NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api
```

---

## Start frontend server

```bash
npm run dev
```

Frontend will run on:

```bash
http://localhost:3000
```

---

# 🔐 Sanctum Authentication Setup

Ensure backend CORS configuration allows frontend origin.

Example:

```php
'allowed_origins' => ['http://localhost:3000'],
```

Frontend requests should include credentials:

```js
withCredentials: true
```

---

# 📬 Mail Configuration

Configure mail credentials inside backend `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="MailMoM.ai"
```

---

# 🧠 AI Workflow

```text
Meeting Transcript
        ↓
AI Summary Generation
        ↓
Action Item Extraction
        ↓
Task Creation
        ↓
Auto Email Sending
```

---

# 📦 API Features

## Authentication

* Register
* Login
* Logout
* Current User

## Meetings

* Create meeting
* Generate summary
* Extract action items
* List meetings
* Meeting details

## Tasks

* Assign tasks
* Update task status
* Track progress

## Dashboard

* Meetings count
* Tasks count
* Participants count

---

# 📸 Screenshots



---

# 🧪 Useful Commands

## Backend

```bash
php artisan serve
php artisan migrate
php artisan queue:work
php artisan config:clear
php artisan cache:clear
```

---

## Frontend

```bash
npm run dev
npm run build
npm run start
```

---

# 🚀 Future Improvements

* 🎙️ Real-time meeting transcription
* 📊 Analytics dashboard
* 🔔 Task reminders
* 🧵 Threaded meeting discussions
* 🤖 AI follow-up suggestions
* 📅 Calendar integrations

---

# 🤝 Contributing

Contributions are welcome.

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

# 📄 License

MIT License

---

# 💡 About

MailMoM.ai is designed to simplify meeting workflows by combining AI-powered summaries with automatic task extraction and email automation.
