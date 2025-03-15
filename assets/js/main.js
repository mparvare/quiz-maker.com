import ApiService from './utils/api-service.js';

// مدیریت وضعیت کاربر
const UserService = {
    currentUser: null,
    
    async init() {
        try {
            const response = await ApiService.checkAuthStatus();
            if (response.isAuthenticated) {
                this.currentUser = response.user;
                this.updateUIForAuthenticatedUser();
            } else {
                this.updateUIForUnauthenticatedUser();
            }
        } catch (error) {
            console.error('خطا در بررسی وضعیت کاربر:', error);
            this.updateUIForUnauthenticatedUser();
        }
    },

    updateUIForAuthenticatedUser() {
        // نمایش المان‌های مخصوص کاربران احراز هویت شده
        document.querySelectorAll('.auth-links').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.user-links').forEach(el => el.style.display = 'block');
        
        // به‌روزرسانی نام کاربر
        document.querySelectorAll('.user-name').forEach(el => {
            el.textContent = this.currentUser.name || 'کاربر';
        });
    },

    updateUIForUnauthenticatedUser() {
        // نمایش المان‌های ورود و ثبت‌نام
        document.querySelectorAll('.auth-links').forEach(el => el.style.display = 'block');
        document.querySelectorAll('.user-links').forEach(el => el.style.display = 'none');
    },

    async login(email, password) {
        try {
            const response = await ApiService.login(email, password);
            if (response.isAuthenticated) {
                this.currentUser = response.user;
                this.updateUIForAuthenticatedUser();
                window.location.href = '/dashboard.html';
            }
        } catch (error) {
            ApiService.handleError(error);
        }
    },

    async logout() {
        try {
            await ApiService.logout();
            this.currentUser = null;
            this.updateUIForUnauthenticatedUser();
            window.location.href = '/index.html';
        } catch (error) {
            ApiService.handleError(error);
        }
    }
};

// تنظیم رویدادها
function setupEventListeners() {
    // فرم ورود
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            await UserService.login(email, password);
        });
    }

    // دکمه خروج
    const logoutButtons = document.querySelectorAll('.logout-link');
    logoutButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            await UserService.logout();
        });
    });
}

// اجرای اولیه
document.addEventListener('DOMContentLoaded', async () => {
    // مقداردهی اولیه سرویس‌ها
    await UserService.init();
    
    // تنظیم رویدادها
    setupEventListeners();
});

// صادر کردن سرویس‌ها برای استفاده در سایر ماژول‌ها
export { UserService, ApiService };